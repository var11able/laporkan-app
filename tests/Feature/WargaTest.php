<?php

namespace Tests\Feature;

use App\Entities\Masyarakat;
use App\Enums\StatusPengaduan;
use App\Models\BuktiModel;
use App\Models\PengaduanModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use Config\Services;
use Tests\Support\FakeUploadedFile;
use Tests\Support\FeatureTestCase;

final class WargaTest extends FeatureTestCase
{
    public function testAlurBuatLaporanLimaLangkah(): void
    {
        $warga = $this->buatWarga();

        $this->sebagaiWarga($warga)->buka('warga/laporan/baru')
            ->assertRedirectTo(url_to('warga.laporan.langkah', 'jenis'));

        $this->lanjutkanSesi()->kirim('warga/laporan/baru/jenis', ['id_kategori' => (string) $this->idKategori()])
            ->assertRedirectTo(url_to('warga.laporan.langkah', 'kronologi'));

        $this->lanjutkanSesi()->kirim('warga/laporan/baru/kronologi', [
            'isi_laporan'        => 'Petugas loket meminta uang Rp150.000 di luar biaya resmi.',
            'waktu_kejadian'     => '2026-08-17',
            'perkiraan_kerugian' => 'Rp 150.000',
        ])->assertRedirectTo(url_to('warga.laporan.langkah', 'instansi'));

        $this->lanjutkanSesi()->kirim('warga/laporan/baru/instansi', [
            'instansi_terlapor' => 'Kantor pelayanan contoh',
            'pihak_terlapor'    => 'Petugas loket 3',
            'id_provinsi'       => (string) $this->idProvinsi(),
            'id_kabupaten_kota' => (string) $this->idKabupaten(),
        ])->assertRedirectTo(url_to('warga.laporan.langkah', 'bukti'));

        $this->lanjutkanSesi()->kirim('warga/laporan/baru/bukti', ['aksi' => 'lanjut'])
            ->assertRedirectTo(url_to('warga.laporan.langkah', 'periksa'));

        $periksa = $this->lanjutkanSesi()->buka('warga/laporan/baru/periksa');
        $periksa->assertOK();
        $periksa->assertSee('Petugas loket meminta uang');
        $periksa->assertSee('Kabupaten Pasuruan, Jawa Timur');
        $periksa->assertSee('Pungutan liar &amp; pemerasan');

        $kirim = $this->lanjutkanSesi()->kirim('warga/laporan/baru/periksa', ['rahasia' => '1']);

        $laporan = (new PengaduanModel())->milikWarga($warga->getId())->first();
        $this->assertNotNull($laporan);
        $this->assertSame($this->idKabupaten(), $laporan->id_kabupaten_kota);
        $this->assertSame($this->idKategori(), $laporan->id_kategori);
        $this->assertSame('Kantor pelayanan contoh', $laporan->instansi_terlapor);
        $this->assertSame('2026-08-17', $laporan->waktu_kejadian);
        $this->assertSame(150000, $laporan->perkiraan_kerugian);
        $this->assertTrue($laporan->rahasia);
        $kirim->assertRedirectTo(url_to('warga.laporan.terkirim', $laporan->id_pengaduan));
        $kirim->assertSessionMissing('laporan_baru');

        $this->lanjutkanSesi()->buka('warga/laporan/terkirim/' . $laporan->id_pengaduan)->assertSee($laporan->nomor_laporan);
    }

    public function testSetiapLangkahDanHalamanPelaporTampil(): void
    {
        $warga = $this->buatWarga();

        $this->sebagaiWarga($warga)->buka('warga/laporan/baru/jenis')->assertSee('Suap-menyuap');
        $this->lanjutkanSesi()->kirim('warga/laporan/baru/jenis', ['id_kategori' => (string) $this->idKategori()]);
        $this->lanjutkanSesi()->buka('warga/laporan/baru/kronologi')->assertSee('Ceritakan kejadiannya');
        $this->lanjutkanSesi()->kirim('warga/laporan/baru/kronologi', ['isi_laporan' => 'Petugas meminta uang di luar biaya resmi.']);
        $this->lanjutkanSesi()->buka('warga/laporan/baru/instansi')->assertSee('Jawa Timur');
        $this->lanjutkanSesi()->kirim('warga/laporan/baru/instansi', [
            'instansi_terlapor' => 'Kantor contoh', 'id_provinsi' => (string) $this->idProvinsi(), 'id_kabupaten_kota' => (string) $this->idKabupaten(),
        ]);
        $this->lanjutkanSesi()->buka('warga/laporan/baru/instansi')->assertSee('Kabupaten Pasuruan');
        $this->lanjutkanSesi()->buka('warga/laporan/baru/bukti')->assertSee('Tambahkan bukti');

        $laporan = $this->buatLaporan($warga);

        foreach (['warga', 'warga/laporan/' . $laporan->id_pengaduan, 'warga/laporan/' . $laporan->id_pengaduan . '/ubah', 'warga/rekap', 'warga/profil'] as $path) {
            $this->sebagaiWarga($warga)->buka($path)->assertStatus(200);
        }
    }

    public function testIdentitasTidakDirahasiakanJikaPelaporMematikannya(): void
    {
        $warga = $this->buatWarga();
        $this->isiSampaiPeriksa($warga);

        $this->lanjutkanSesi()->kirim('warga/laporan/baru/periksa');

        $this->assertFalse((new PengaduanModel())->milikWarga($warga->getId())->first()->rahasia);
    }

    public function testBuktiHanyaBisaDibukaPemilik(): void
    {
        $warga   = $this->buatWarga('pemilik');
        $laporan = Services::pengaduan(false)->buat(
            ['isi_laporan' => 'Petugas meminta uang di luar biaya resmi.', 'id_kabupaten_kota' => $this->idKabupaten()],
            $warga->getId(),
            [FakeUploadedFile::jpegDenganExif(300, 200)],
        );
        $bukti = (new BuktiModel())->untukPengaduan($laporan->id_pengaduan);
        $url   = 'warga/laporan/' . $laporan->id_pengaduan . '/bukti/' . $bukti[0]['id_bukti'];

        $hasil = $this->sebagaiWarga($warga)->buka($url);
        $hasil->assertOK();
        $hasil->assertHeader('Cache-Control', 'private, no-store');

        $this->expectException(PageNotFoundException::class);
        $this->sebagaiWarga($this->buatWarga('penyusup'))->buka($url);
    }

    public function testLangkahTidakBisaDilompati(): void
    {
        $this->sebagaiWarga($this->buatWarga())->buka('warga/laporan/baru/periksa')
            ->assertRedirectTo(url_to('warga.laporan.langkah', 'jenis'));
    }

    public function testLaporanYangBelumSelesaiBisaDilanjutkan(): void
    {
        $warga = $this->buatWarga();
        $this->sebagaiWarga($warga)->kirim('warga/laporan/baru/jenis', ['id_kategori' => (string) $this->idKategori()]);

        $beranda = $this->lanjutkanSesi()->buka('warga');
        $beranda->assertSee('belum selesai Anda kirim');

        $this->lanjutkanSesi()->buka('warga/laporan/baru')->assertRedirectTo(url_to('warga.laporan.langkah', 'kronologi'));

        $this->lanjutkanSesi()->buka('warga/laporan/baru?ulang=1')->assertRedirectTo(url_to('warga.laporan.langkah', 'jenis'));
        $this->assertArrayNotHasKey('laporan_baru', $_SESSION);
    }

    public function testKabupatenHarusDiProvinsiYangDipilih(): void
    {
        $warga = $this->buatWarga();
        $this->sebagaiWarga($warga)->kirim('warga/laporan/baru/jenis', ['id_kategori' => (string) $this->idKategori()]);
        $this->lanjutkanSesi()->kirim('warga/laporan/baru/kronologi', ['isi_laporan' => 'Petugas meminta uang di luar biaya resmi.']);

        $this->lanjutkanSesi()->kirim('warga/laporan/baru/instansi', [
            'instansi_terlapor' => 'Kantor contoh',
            'id_provinsi'       => (string) $this->idProvinsi('31'),
            'id_kabupaten_kota' => (string) $this->idKabupaten(),
        ])->assertRedirect();

        $this->assertArrayHasKey('id_kabupaten_kota', $_SESSION['_ci_validation_errors'] ?? []);
    }

    public function testWaktuKejadianTidakBolehDiMasaDepan(): void
    {
        $warga = $this->buatWarga();
        $this->sebagaiWarga($warga)->kirim('warga/laporan/baru/jenis', ['id_kategori' => (string) $this->idKategori()]);
        $this->lanjutkanSesi()->kirim('warga/laporan/baru/kronologi', [
            'isi_laporan' => 'Petugas meminta uang di luar biaya resmi.', 'waktu_kejadian' => date('Y-m-d', strtotime('+3 days')),
        ])->assertRedirect();

        $this->assertArrayHasKey('waktu_kejadian', $_SESSION['_ci_validation_errors'] ?? []);
    }

    public function testMencariLaporanSendiri(): void
    {
        $warga = $this->buatWarga();
        $this->buatLaporan($warga, 'Pungutan di loket perizinan usaha.');
        $this->buatLaporan($warga, 'Pemotongan dana bantuan kelompok tani.');

        $html = $this->sebagaiWarga($warga)->buka('warga?q=bantuan')->getBody();

        $this->assertStringContainsString('dana bantuan kelompok tani', $html);
        $this->assertStringNotContainsString('loket perizinan', $html);
    }

    public function testChipFilterMengelompokkanStatus(): void
    {
        $warga    = $this->buatWarga();
        $operator = $this->buatPetugas();
        $this->buatLaporan($warga, 'Laporan yang masih baru diterima.');
        $proses = $this->buatLaporan($warga, 'Laporan yang sudah terverifikasi.');
        Services::tanggapan(false)->tambah($proses, $operator, StatusPengaduan::Diverifikasi, 'Diverifikasi.');
        Services::tanggapan(false)->tambah($this->muatUlang($proses), $operator, StatusPengaduan::Valid, 'Valid.');

        $html = $this->sebagaiWarga($warga)->buka('warga?kelompok=diproses')->getBody();

        $this->assertStringContainsString('sudah terverifikasi', $html);
        $this->assertStringNotContainsString('masih baru diterima', $html);
    }

    public function testWargaTidakBisaMembukaLaporanOrangLain(): void
    {
        $laporan = $this->buatLaporan($this->buatWarga('pemilik'));

        $this->expectException(PageNotFoundException::class);
        $this->sebagaiWarga($this->buatWarga('penyusup'))->buka('warga/laporan/' . $laporan->id_pengaduan);
    }

    public function testWargaTidakBisaMengubahLaporanOrangLain(): void
    {
        $laporan  = $this->buatLaporan($this->buatWarga('pemilik'));
        $penyusup = $this->buatWarga('penyusup');

        try {
            $this->sebagaiWarga($penyusup)->kirim('warga/laporan/' . $laporan->id_pengaduan, $this->isianUbah('Diubah oleh orang lain.'), 'PUT');
            $this->fail('Seharusnya 404.');
        } catch (PageNotFoundException) {
        }

        $this->seeInDatabase('pengaduan', ['id_pengaduan' => $laporan->id_pengaduan, 'isi_laporan' => $laporan->isi_laporan]);
    }

    public function testWargaTidakBisaMenghapusLaporanOrangLain(): void
    {
        $laporan = $this->buatLaporan($this->buatWarga('pemilik'));

        try {
            $this->sebagaiWarga($this->buatWarga('penyusup'))->kirim('warga/laporan/' . $laporan->id_pengaduan, [], 'DELETE');
            $this->fail('Seharusnya 404.');
        } catch (PageNotFoundException) {
        }

        $this->seeInDatabase('pengaduan', ['id_pengaduan' => $laporan->id_pengaduan]);
    }

    public function testMengubahLaporanTidakMengubahTanggalLaporan(): void
    {
        $warga   = $this->buatWarga();
        $laporan = $this->buatLaporan($warga);
        $this->db->table('pengaduan')->where('id_pengaduan', $laporan->id_pengaduan)->update(['tgl_pengaduan' => '2026-02-03 10:00:00']);

        $this->sebagaiWarga($warga)->kirim('warga/laporan/' . $laporan->id_pengaduan, $this->isianUbah('Kronologi yang sudah diperbaiki pelapor.'), 'PUT')
            ->assertRedirectTo(url_to('warga.laporan.detail', $laporan->id_pengaduan));

        $this->seeInDatabase('pengaduan', [
            'id_pengaduan'  => $laporan->id_pengaduan,
            'isi_laporan'   => 'Kronologi yang sudah diperbaiki pelapor.',
            'tgl_pengaduan' => '2026-02-03 10:00:00',
        ]);
    }

    public function testLaporanYangSudahDitanggapiTidakBisaDiubah(): void
    {
        $warga   = $this->buatWarga();
        $laporan = $this->buatLaporan($warga);
        Services::tanggapan(false)->tambah($laporan, $this->buatPetugas(), StatusPengaduan::Diverifikasi, 'Diverifikasi.');

        $this->sebagaiWarga($warga)->buka('warga/laporan/' . $laporan->id_pengaduan . '/ubah')
            ->assertRedirectTo(url_to('warga.laporan.detail', $laporan->id_pengaduan));

        $this->sebagaiWarga($warga)->kirim('warga/laporan/' . $laporan->id_pengaduan, [], 'DELETE');
        $this->seeInDatabase('pengaduan', ['id_pengaduan' => $laporan->id_pengaduan]);
    }

    public function testWargaMenghapusLaporanSendiri(): void
    {
        $warga   = $this->buatWarga();
        $laporan = $this->buatLaporan($warga);

        $this->sebagaiWarga($warga)->kirim('warga/laporan/' . $laporan->id_pengaduan, [], 'DELETE')
            ->assertRedirectTo(url_to('warga.beranda'));

        $this->dontSeeInDatabase('pengaduan', ['id_pengaduan' => $laporan->id_pengaduan]);
    }

    public function testDaftarDanRekapHanyaBerisiLaporanSendiri(): void
    {
        $saya = $this->buatWarga('saya');
        $this->buatLaporan($saya, 'Laporan milik saya sendiri.');
        $this->buatLaporan($this->buatWarga('orang-lain'), 'Laporan milik orang lain.');

        foreach (['warga', 'warga/rekap', 'warga/rekap/cetak'] as $path) {
            $html = $this->sebagaiWarga($saya)->buka($path)->getBody();
            $this->assertStringContainsString('Laporan milik saya sendiri.', $html, $path);
            $this->assertStringNotContainsString('Laporan milik orang lain.', $html, $path);
        }
    }

    public function testGantiPasswordDenganPasswordLamaSalahDitolak(): void
    {
        $warga = $this->buatWarga();

        $this->sebagaiWarga($warga)->kirim('warga/profil/password', [
            'password_lama' => 'salah', 'password_baru' => 'baru12345', 'password_konfirmasi' => 'baru12345',
        ], 'PUT')->assertRedirect();

        $this->assertSame('Kata sandi saat ini salah.', $_SESSION['_ci_validation_errors']['password_lama'] ?? null);
        $this->assertTrue(password_verify('rahasia123', $this->db->table('masyarakat')->get()->getRow()->password));
    }

    public function testUbahProfil(): void
    {
        $warga = $this->buatWarga();

        $this->sebagaiWarga($warga)->kirim('warga/profil', [
            'nama' => 'nama baru', 'no_telepon' => '089999999999', 'alamat' => 'Alamat baru',
        ], 'PUT')->assertRedirectTo(url_to('warga.profil'));

        $this->seeInDatabase('masyarakat', ['id_masyarakat' => $warga->getId(), 'nama' => 'Nama Baru', 'no_telepon' => '089999999999']);
    }

    private function isiSampaiPeriksa(Masyarakat $warga): void
    {
        $this->sebagaiWarga($warga)->kirim('warga/laporan/baru/jenis', ['id_kategori' => (string) $this->idKategori()]);
        $this->lanjutkanSesi()->kirim('warga/laporan/baru/kronologi', ['isi_laporan' => 'Petugas meminta uang di luar biaya resmi.']);
        $this->lanjutkanSesi()->kirim('warga/laporan/baru/instansi', [
            'instansi_terlapor' => 'Kantor contoh', 'id_provinsi' => (string) $this->idProvinsi(), 'id_kabupaten_kota' => (string) $this->idKabupaten(),
        ]);
        $this->lanjutkanSesi()->kirim('warga/laporan/baru/bukti', ['aksi' => 'lanjut']);
    }

    private function isianUbah(string $isi): array
    {
        return [
            'id_kategori'       => (string) $this->idKategori(),
            'isi_laporan'       => $isi,
            'instansi_terlapor' => 'Kantor pelayanan contoh',
            'id_provinsi'       => (string) $this->idProvinsi(),
            'id_kabupaten_kota' => (string) $this->idKabupaten(),
        ];
    }
}
