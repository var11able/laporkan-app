<?php

namespace Tests\Feature\Services;

use App\Enums\StatusLaporan;
use App\Exceptions\AturanBisnisException;
use App\Models\BuktiModel;
use App\Services\LaporanService;
use App\Services\UploadService;
use Config\Services;
use Tests\Support\DatabaseTestCase;
use Tests\Support\FakeUploadedFile;

/**
 * @internal
 */
final class LaporanServiceTest extends DatabaseTestCase
{
    public function testLaporanBaruMendapatNomorDanStatusBaru(): void
    {
        $laporan = $this->buatLaporan($this->buatWarga());

        $this->assertSame(LaporanService::nomorLaporan($laporan->id_laporan), $laporan->nomor_laporan);
        $this->assertSame(StatusLaporan::Baru, $laporan->status());
        $this->assertSame('Kabupaten Pasuruan, Jawa Timur', $laporan->lokasi());
        $this->assertSame('Pungutan liar & pemerasan', $laporan->kategori);
        $this->assertFalse($laporan->tampilPublik());
    }

    public function testNilaiRupiahDibaca(): void
    {
        $this->assertSame(1500000, LaporanService::angkaRupiah('Rp 1.500.000'));
        $this->assertSame(250000, LaporanService::angkaRupiah('250000'));
        $this->assertNull(LaporanService::angkaRupiah(''));
    }

    public function testBuktiFotoDanPdfDisimpanPrivat(): void
    {
        $pdf = tempnam(sys_get_temp_dir(), 'pdf');
        file_put_contents($pdf, "%PDF-1.4\n1 0 obj << /Type /Catalog >> endobj\ntrailer << /Root 1 0 R >>\n%%EOF\n");

        $laporan = Services::laporan(false)->buat(
            ['isi_laporan' => 'Mark-up harga pengadaan laptop sekolah.', 'id_kabupaten_kota' => $this->idKabupaten()],
            $this->buatWarga()->getId(),
            [FakeUploadedFile::gambar(2400, 1200), FakeUploadedFile::dari($pdf, 'kuitansi.pdf')],
        );

        $bukti = (new BuktiModel())->untukLaporan($laporan->id_laporan);
        $this->assertCount(2, $bukti);
        $this->assertSame(['image/png', 'application/pdf'], array_column($bukti, 'mime'));
        $this->assertSame('kuitansi.pdf', $bukti[1]['nama_asli']);

        foreach ($bukti as $b) {
            $this->assertFileExists($this->uploadDir . '/bukti/' . $b['nama_file']);
        }

        $this->assertSame(1600, getimagesize($this->uploadDir . '/bukti/' . $bukti[0]['nama_file'])[0]);
        $this->assertSame([], glob($this->uploadDir . '/laporan/*') ?: []);
    }

    public function testBuktiPalingBanyakTiga(): void
    {
        $this->expectExceptionMessage('paling banyak 3');

        Services::laporan(false)->buat(
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
        $this->db->table('laporan')->where('id_laporan', $laporan->id_laporan)->update(['tgl_laporan' => '2026-01-02 08:00:00']);

        Services::laporan(false)->ubahOlehWarga($this->muatUlang($laporan), $warga, [
            'isi_laporan'       => 'Kronologi laporan yang sudah diperbaiki.',
            'id_kabupaten_kota' => $this->idKabupaten(),
        ]);

        $baru = $this->muatUlang($laporan);
        $this->assertSame('Kronologi laporan yang sudah diperbaiki.', $baru->isi_laporan);
        $this->assertSame('2026-01-02 08:00:00', $baru->tgl_laporan->format('Y-m-d H:i:s'));
        $this->assertNotNull($baru->updated_at);
    }

    public function testMengubahLaporanBisaMenghapusDanMenambahBukti(): void
    {
        $warga   = $this->buatWarga();
        $laporan = Services::laporan(false)->buat(['isi_laporan' => 'Laporan dengan satu bukti.'], $warga->getId(), [FakeUploadedFile::gambar()]);
        $lama    = (new BuktiModel())->untukLaporan($laporan->id_laporan)[0];

        Services::laporan(false)->ubahOlehWarga(
            $this->muatUlang($laporan),
            $warga,
            ['isi_laporan' => 'Laporan dengan bukti yang diganti.'],
            [FakeUploadedFile::gambar()],
            [(int) $lama['id_bukti']],
        );

        $bukti = (new BuktiModel())->untukLaporan($laporan->id_laporan);
        $this->assertCount(1, $bukti);
        $this->assertNotSame($lama['nama_file'], $bukti[0]['nama_file']);
        $this->assertFileDoesNotExist($this->uploadDir . '/bukti/' . $lama['nama_file']);
    }

    public function testWargaTidakBisaMengubahLaporanOrangLain(): void
    {
        $laporan = $this->buatLaporan($this->buatWarga('pemilik'));

        $this->expectException(AturanBisnisException::class);
        Services::laporan(false)->ubahOlehWarga($laporan, $this->buatWarga('penyusup'), ['isi_laporan' => 'Diubah oleh orang lain.']);
    }

    public function testWargaTidakBisaMenghapusLaporanYangSudahDitanggapi(): void
    {
        $warga   = $this->buatWarga();
        $laporan = $this->buatLaporan($warga);
        Services::tanggapan(false)->tambah($laporan, $this->buatPetugas(), StatusLaporan::Diverifikasi, 'Diverifikasi.');

        $this->expectExceptionMessage('sudah diperiksa admin');
        Services::laporan(false)->hapusOlehWarga($this->muatUlang($laporan), $warga);
    }

    public function testOperatorTidakBisaMenghapusLaporan(): void
    {
        $laporan = $this->buatLaporan($this->buatWarga());

        $this->expectException(AturanBisnisException::class);
        Services::laporan(false)->hapusOlehPetugas($laporan, $this->buatPetugas('operator'));
    }

    public function testAdminMenghapusLaporanBesertaTanggapanDanBukti(): void
    {
        $admin   = $this->buatPetugas('administrator', 'admin');
        $laporan = Services::laporan(false)->buat(
            ['isi_laporan' => 'Laporan palsu yang akan dihapus admin.', 'id_kabupaten_kota' => $this->idKabupaten()],
            $this->buatWarga()->getId(),
            [FakeUploadedFile::gambar()],
        );
        $bukti     = (new BuktiModel())->untukLaporan($laporan->id_laporan)[0];
        $tanggapan = Services::tanggapan(false)->tambah($laporan, $admin, StatusLaporan::Diverifikasi, 'Diverifikasi.', FakeUploadedFile::gambar());

        Services::laporan(false)->hapusOlehPetugas($this->muatUlang($laporan), $admin);

        $this->dontSeeInDatabase('laporan', ['id_laporan' => $laporan->id_laporan]);
        $this->dontSeeInDatabase('tanggapan', ['id_laporan' => $laporan->id_laporan]);
        $this->dontSeeInDatabase('bukti_laporan', ['id_laporan' => $laporan->id_laporan]);
        $this->assertFileDoesNotExist($this->uploadDir . '/bukti/' . $bukti['nama_file']);
        $this->assertFileDoesNotExist($this->uploadDir . '/tanggapan/' . $tanggapan->foto_tanggapan);
    }

    public function testLaporanGagalDisimpanTidakMeninggalkanBukti(): void
    {
        try {
            Services::laporan(false)->buat(['isi_laporan' => 'pendek'], $this->buatWarga()->getId(), [FakeUploadedFile::gambar()]);
            $this->fail('Isi laporan terlalu pendek seharusnya ditolak.');
        } catch (AturanBisnisException) {
        }

        $this->assertSame([], glob($this->uploadDir . '/bukti/*') ?: []);
    }

    public function testMembukaIdentitasHanyaAdministratorDanTercatat(): void
    {
        $laporan = $this->buatLaporan($this->buatWarga(), 'Laporan rahasia.', ['rahasia' => true]);

        try {
            Services::laporan(false)->bukaIdentitas($laporan, $this->buatPetugas('operator'));
            $this->fail('Operator seharusnya ditolak.');
        } catch (AturanBisnisException) {
        }

        $admin = $this->buatPetugas('administrator', 'admin');
        Services::laporan(false)->bukaIdentitas($laporan, $admin);

        $this->seeInDatabase('log', ['id_user' => $admin->getId(), 'isi_log' => 'Membuka identitas pelapor laporan ' . $laporan->nomor_laporan]);
    }
}
