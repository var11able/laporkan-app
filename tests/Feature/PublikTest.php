<?php

namespace Tests\Feature;

use App\Enums\StatusPengaduan;
use CodeIgniter\Exceptions\PageNotFoundException;
use Config\Services;
use Tests\Support\FeatureTestCase;

final class PublikTest extends FeatureTestCase
{
    public function testLaporanBelumDiverifikasiTidakTampilDiPublik(): void
    {
        $laporan = $this->buatLaporan($this->buatWarga(), 'Kronologi rahasia yang belum diverifikasi.');

        foreach (['/', 'laporan', 'laporan?q=rahasia'] as $path) {
            $this->assertStringNotContainsString('Kronologi rahasia', $this->buka($path)->getBody(), $path);
        }

        $this->buka('laporan/' . $laporan->nomor_laporan)
            ->assertRedirectTo(url_to('lacak') . '?nomor=' . $laporan->nomor_laporan);
    }

    public function testPublikHanyaMelihatRingkasanTanpaIdentitas(): void
    {
        $warga   = $this->buatWarga('sukarni');
        $laporan = $this->buatLaporan($warga, 'Petugas loket bernama Fulan meminta Rp150.000.', [
            'instansi_terlapor' => 'Kantor Perizinan Rahasia',
            'pihak_terlapor'    => 'Fulan',
        ]);
        $this->jadikanPublik($laporan, $this->buatPetugas(), 'Dugaan pungutan liar di sebuah kantor perizinan.');

        foreach (['/', 'laporan', 'laporan/' . $laporan->nomor_laporan] as $path) {
            $html = $this->buka($path)->getBody();

            $this->assertStringContainsString('Dugaan pungutan liar di sebuah kantor perizinan.', $html, $path);

            foreach ([$warga->nama, $warga->no_telepon, 'Fulan', 'Kantor Perizinan Rahasia', 'Rp150.000', 'Diverifikasi.'] as $rahasia) {
                $this->assertStringNotContainsString($rahasia, $html, $path . ' memuat ' . $rahasia);
            }
        }
    }

    public function testPencarianPublikHanyaMencariRingkasan(): void
    {
        $petugas = $this->buatPetugas();
        $a       = $this->buatLaporan($this->buatWarga('pelapor-a'), 'Kronologi menyebut kata bendungan.');
        $b       = $this->buatLaporan($this->buatWarga('pelapor-b'), 'Kronologi tentang perizinan.');
        $this->jadikanPublik($a, $petugas, 'Dugaan kecurangan pengadaan proyek irigasi.');
        $this->jadikanPublik($b, $petugas, 'Dugaan pungutan liar pengurusan izin usaha.');

        $html = $this->buka('laporan?q=irigasi')->getBody();
        $this->assertStringContainsString('proyek irigasi', $html);
        $this->assertStringNotContainsString('izin usaha', $html);

        $this->assertStringContainsString('Tidak ada laporan', $this->buka('laporan?q=bendungan')->getBody());
    }

    public function testDetailPublikMenampilkanStatusDanInstansiTujuanTanpaPesanAdmin(): void
    {
        $petugas = $this->buatPetugas();
        $laporan = $this->jadikanPublik($this->buatLaporan($this->buatWarga()), $petugas);
        Services::tanggapan(false)->tambah($laporan, $petugas, StatusPengaduan::Diteruskan, 'Pesan pribadi untuk pelapor.', null, 'Komisi Pemberantasan Korupsi (KPK)');

        $html = $this->buka('laporan/' . $laporan->nomor_laporan)->getBody();

        $this->assertStringContainsString('Diteruskan ke instansi berwenang', $html);
        $this->assertStringContainsString('Komisi Pemberantasan Korupsi (KPK)', $html);
        $this->assertStringContainsString('Laporan diterima', $html);
        $this->assertStringNotContainsString('Pesan pribadi untuk pelapor.', $html);
    }

    public function testPelaporMembukaTautanLaporanSendiriDiarahkanKeHalamanPribadi(): void
    {
        $warga   = $this->buatWarga();
        $laporan = $this->buatLaporan($warga);

        $this->sebagaiWarga($warga)->buka('laporan/' . $laporan->nomor_laporan)
            ->assertRedirectTo(url_to('warga.laporan.detail', $laporan->id_pengaduan));
    }

    public function testNomorTidakDikenalMenghasilkan404(): void
    {
        $this->expectException(PageNotFoundException::class);
        $this->buka('laporan/LP-2026-999999');
    }

    public function testLacakLaporanPublikMengarahkanKeDetail(): void
    {
        $laporan = $this->jadikanPublik($this->buatLaporan($this->buatWarga()), $this->buatPetugas());

        $this->buka('lacak?nomor=' . strtolower($laporan->nomor_laporan))
            ->assertRedirectTo(url_to('laporan.detail', $laporan->nomor_laporan));
    }

    public function testLacakLaporanBelumPublikHanyaMenampilkanStatus(): void
    {
        $laporan = $this->buatLaporan($this->buatWarga(), 'Isi laporan yang tidak boleh terlihat.');
        Services::tanggapan(false)->tambah($laporan, $this->buatPetugas(), StatusPengaduan::Diverifikasi, 'Pesan admin yang tidak boleh terlihat.');

        $html = $this->buka('lacak?nomor=' . $laporan->nomor_laporan)->getBody();

        $this->assertStringContainsString('Sedang diverifikasi', $html);
        $this->assertStringNotContainsString('tidak boleh terlihat', $html);
    }

    public function testLacakNomorTidakDikenalMenampilkanPesanJelas(): void
    {
        $hasil = $this->buka('lacak?nomor=LP-0000-000000');

        $hasil->assertOK();
        $hasil->assertSee('tidak ditemukan');
    }

    public function testUrlLamaDialihkanPermanen(): void
    {
        $laporan = $this->buatLaporan($this->buatWarga());

        $hasil = $this->buka('landing/detailPengaduan/' . $laporan->id_pengaduan);

        $hasil->assertStatus(301);
        $hasil->assertRedirectTo(url_to('laporan.detail', $laporan->nomor_laporan));
    }

    public function testFilterStatus(): void
    {
        $petugas  = $this->buatPetugas();
        $valid    = $this->jadikanPublik($this->buatLaporan($this->buatWarga('pelapor-a')), $petugas, 'Ringkasan laporan yang masih valid.');
        $teruskan = $this->jadikanPublik($this->buatLaporan($this->buatWarga('pelapor-b')), $petugas, 'Ringkasan laporan yang sudah diteruskan.');
        Services::tanggapan(false)->tambah($teruskan, $petugas, StatusPengaduan::Diteruskan, 'Diteruskan.', null, 'Kejaksaan Negeri');

        $html = $this->buka('laporan?status=pengerjaan')->getBody();

        $this->assertStringContainsString('sudah diteruskan', $html);
        $this->assertStringNotContainsString('masih valid', $html);
        $this->assertNotNull($valid);
    }

    public function testHalamanInformasiTampil(): void
    {
        foreach (['tentang-korupsi', 'kebijakan-privasi', 'syarat-ketentuan', 'saran'] as $path) {
            $this->buka($path)->assertOK();
        }

        $this->buka('tentang-korupsi')->assertSee('Tujuh kelompok tindak pidana korupsi');
    }

    public function testKirimSaran(): void
    {
        $this->kirim('saran', [
            'nama' => 'ahmad', 'no_telepon' => '081234567890', 'alamat' => 'Jl. Contoh', 'saran' => 'Tolong tambah panduan melapor.',
        ])->assertRedirectTo(url_to('saran'));

        $this->seeInDatabase('saran', ['nama' => 'Ahmad', 'saran' => 'Tolong tambah panduan melapor.']);
    }

    public function testSaranBolehAnonim(): void
    {
        $this->kirim('saran', ['saran' => 'Tambahkan berita tentang korupsi.'])->assertRedirectTo(url_to('saran'));

        $this->seeInDatabase('saran', ['saran' => 'Tambahkan berita tentang korupsi.', 'nama' => null, 'no_telepon' => null, 'alamat' => null]);
    }

    public function testApiKabupatenKota(): void
    {
        $hasil = $this->buka('api/wilayah/' . $this->idProvinsi('35') . '/kabupaten-kota');

        $hasil->assertOK();
        $nama = array_column(json_decode($hasil->getJSON(), true), 'nama');
        $this->assertCount(38, $nama);
        $this->assertContains('Kabupaten Pasuruan', $nama);
    }
}
