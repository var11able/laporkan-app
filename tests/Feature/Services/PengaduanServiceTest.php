<?php

namespace Tests\Feature\Services;

use App\Enums\StatusPengaduan;
use App\Exceptions\AturanBisnisException;
use App\Models\BuktiModel;
use App\Services\PengaduanService;
use App\Services\UploadService;
use Config\Services;
use Tests\Support\DatabaseTestCase;
use Tests\Support\FakeUploadedFile;

final class PengaduanServiceTest extends DatabaseTestCase
{
    public function testLaporanBaruMendapatNomorDanStatusBaru(): void
    {
        $laporan = $this->buatLaporan($this->buatWarga());

        $this->assertSame(PengaduanService::nomorLaporan($laporan->id_pengaduan), $laporan->nomor_laporan);
        $this->assertSame(StatusPengaduan::Baru, $laporan->status());
        $this->assertSame('Kabupaten Pasuruan, Jawa Timur', $laporan->lokasi());
        $this->assertSame('Pungutan liar & pemerasan', $laporan->kategori);
        $this->assertFalse($laporan->tampilPublik());
    }

    public function testNilaiRupiahDibaca(): void
    {
        $this->assertSame(1500000, PengaduanService::angkaRupiah('Rp 1.500.000'));
        $this->assertSame(250000, PengaduanService::angkaRupiah('250000'));
        $this->assertNull(PengaduanService::angkaRupiah(''));
    }

    public function testBuktiFotoDanPdfDisimpanPrivat(): void
    {
        $pdf = tempnam(sys_get_temp_dir(), 'pdf');
        file_put_contents($pdf, "%PDF-1.4\n1 0 obj << /Type /Catalog >> endobj\ntrailer << /Root 1 0 R >>\n%%EOF\n");

        $laporan = Services::pengaduan(false)->buat(
            ['isi_laporan' => 'Mark-up harga pengadaan laptop sekolah.', 'id_kabupaten_kota' => $this->idKabupaten()],
            $this->buatWarga()->getId(),
            [FakeUploadedFile::gambar(2400, 1200), FakeUploadedFile::dari($pdf, 'kuitansi.pdf')],
        );

        $bukti = (new BuktiModel())->untukPengaduan($laporan->id_pengaduan);
        $this->assertCount(2, $bukti);
        $this->assertSame(['image/png', 'application/pdf'], array_column($bukti, 'mime'));
        $this->assertSame('kuitansi.pdf', $bukti[1]['nama_asli']);

        foreach ($bukti as $b) {
            $this->assertFileExists($this->uploadDir . '/bukti/' . $b['nama_file']);
        }

        $this->assertSame(1600, getimagesize($this->uploadDir . '/bukti/' . $bukti[0]['nama_file'])[0]);
        $this->assertSame([], glob($this->uploadDir . '/pengaduan/*') ?: []);
    }

    public function testBuktiPalingBanyakTiga(): void
    {
        $this->expectExceptionMessage('paling banyak 3');

        Services::pengaduan(false)->buat(
            ['isi_laporan' => 'Laporan dengan terlalu banyak bukti.'],
            $this->buatWarga()->getId(),
            [FakeUploadedFile::gambar(), FakeUploadedFile::gambar(), FakeUploadedFile::gambar(), FakeUploadedFile::gambar()],
        );
    }

    public function testBerkasSelainFotoDanPdfDitolak(): void
    {
        $palsu = tempnam(sys_get_temp_dir(), 'txt');
        file_put_contents($palsu, '<?php echo "bukan bukti";');

        $this->expectException(AturanBisnisException::class);
        (new UploadService($this->uploadDir))->simpanBukti(FakeUploadedFile::dari($palsu, 'bukti.pdf'));
    }

    public function testWargaMengubahLaporanTanpaMengubahTanggal(): void
    {
        $warga   = $this->buatWarga();
        $laporan = $this->buatLaporan($warga);
        $this->db->table('pengaduan')->where('id_pengaduan', $laporan->id_pengaduan)->update(['tgl_pengaduan' => '2026-01-02 08:00:00']);

        Services::pengaduan(false)->ubahOlehWarga($this->muatUlang($laporan), $warga, [
            'isi_laporan'       => 'Kronologi laporan yang sudah diperbaiki.',
            'id_kabupaten_kota' => $this->idKabupaten(),
        ]);

        $baru = $this->muatUlang($laporan);
        $this->assertSame('Kronologi laporan yang sudah diperbaiki.', $baru->isi_laporan);
        $this->assertSame('2026-01-02 08:00:00', $baru->tgl_pengaduan->format('Y-m-d H:i:s'));
        $this->assertNotNull($baru->updated_at);
    }

    public function testMengubahLaporanBisaMenghapusDanMenambahBukti(): void
    {
        $warga   = $this->buatWarga();
        $laporan = Services::pengaduan(false)->buat(['isi_laporan' => 'Laporan dengan satu bukti.'], $warga->getId(), [FakeUploadedFile::gambar()]);
        $lama    = (new BuktiModel())->untukPengaduan($laporan->id_pengaduan)[0];

        Services::pengaduan(false)->ubahOlehWarga(
            $this->muatUlang($laporan),
            $warga,
            ['isi_laporan' => 'Laporan dengan bukti yang diganti.'],
            [FakeUploadedFile::gambar()],
            [(int) $lama['id_bukti']],
        );

        $bukti = (new BuktiModel())->untukPengaduan($laporan->id_pengaduan);
        $this->assertCount(1, $bukti);
        $this->assertNotSame($lama['nama_file'], $bukti[0]['nama_file']);
        $this->assertFileDoesNotExist($this->uploadDir . '/bukti/' . $lama['nama_file']);
    }

    public function testWargaTidakBisaMengubahLaporanOrangLain(): void
    {
        $laporan = $this->buatLaporan($this->buatWarga('pemilik'));

        $this->expectException(AturanBisnisException::class);
        Services::pengaduan(false)->ubahOlehWarga($laporan, $this->buatWarga('penyusup'), ['isi_laporan' => 'Diubah oleh orang lain.']);
    }

    public function testWargaTidakBisaMenghapusLaporanYangSudahDitanggapi(): void
    {
        $warga   = $this->buatWarga();
        $laporan = $this->buatLaporan($warga);
        Services::tanggapan(false)->tambah($laporan, $this->buatPetugas(), StatusPengaduan::Diverifikasi, 'Diverifikasi.');

        $this->expectExceptionMessage('sudah diperiksa admin');
        Services::pengaduan(false)->hapusOlehWarga($this->muatUlang($laporan), $warga);
    }

    public function testOperatorTidakBisaMenghapusLaporan(): void
    {
        $laporan = $this->buatLaporan($this->buatWarga());

        $this->expectException(AturanBisnisException::class);
        Services::pengaduan(false)->hapusOlehPetugas($laporan, $this->buatPetugas('operator'));
    }

    public function testAdminMenghapusLaporanBesertaTanggapanDanBukti(): void
    {
        $admin   = $this->buatPetugas('administrator', 'admin');
        $laporan = Services::pengaduan(false)->buat(
            ['isi_laporan' => 'Laporan palsu yang akan dihapus admin.', 'id_kabupaten_kota' => $this->idKabupaten()],
            $this->buatWarga()->getId(),
            [FakeUploadedFile::gambar()],
        );
        $bukti     = (new BuktiModel())->untukPengaduan($laporan->id_pengaduan)[0];
        $tanggapan = Services::tanggapan(false)->tambah($laporan, $admin, StatusPengaduan::Diverifikasi, 'Diverifikasi.', FakeUploadedFile::gambar());

        Services::pengaduan(false)->hapusOlehPetugas($this->muatUlang($laporan), $admin);

        $this->dontSeeInDatabase('pengaduan', ['id_pengaduan' => $laporan->id_pengaduan]);
        $this->dontSeeInDatabase('tanggapan', ['id_pengaduan' => $laporan->id_pengaduan]);
        $this->dontSeeInDatabase('bukti_pengaduan', ['id_pengaduan' => $laporan->id_pengaduan]);
        $this->assertFileDoesNotExist($this->uploadDir . '/bukti/' . $bukti['nama_file']);
        $this->assertFileDoesNotExist($this->uploadDir . '/tanggapan/' . $tanggapan->foto_tanggapan);
    }

    public function testLaporanGagalDisimpanTidakMeninggalkanBukti(): void
    {
        try {
            Services::pengaduan(false)->buat(['isi_laporan' => 'pendek'], $this->buatWarga()->getId(), [FakeUploadedFile::gambar()]);
            $this->fail('Isi laporan terlalu pendek seharusnya ditolak.');
        } catch (AturanBisnisException) {
        }

        $this->assertSame([], glob($this->uploadDir . '/bukti/*') ?: []);
    }

    public function testMembukaIdentitasHanyaAdministratorDanTercatat(): void
    {
        $laporan = $this->buatLaporan($this->buatWarga(), 'Laporan rahasia.', ['rahasia' => true]);

        try {
            Services::pengaduan(false)->bukaIdentitas($laporan, $this->buatPetugas('operator'));
            $this->fail('Operator seharusnya ditolak.');
        } catch (AturanBisnisException) {
        }

        $admin = $this->buatPetugas('administrator', 'admin');
        Services::pengaduan(false)->bukaIdentitas($laporan, $admin);

        $this->seeInDatabase('log', ['id_user' => $admin->getId(), 'isi_log' => 'Membuka identitas pelapor laporan ' . $laporan->nomor_laporan]);
    }
}
