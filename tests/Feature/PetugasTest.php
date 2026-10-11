<?php

namespace Tests\Feature;

use App\Enums\StatusLaporan;
use Config\Services;
use Tests\Support\FeatureTestCase;

/**
 * @internal
 */
final class PetugasTest extends FeatureTestCase
{
    public function testSemuaHalamanPanelTampil(): void
    {
        $admin   = $this->buatPetugas('administrator', 'admin');
        $laporan = $this->buatLaporan($this->buatWarga());
        $t       = Services::tanggapan(false)->tambah($laporan, $admin, StatusLaporan::Diverifikasi, 'Ditinjau.');

        $halaman = [
            'petugas', 'petugas/laporan', 'petugas/laporan/baru', 'petugas/laporan/' . $laporan->id_laporan,
            'petugas/laporan/' . $laporan->id_laporan . '/ubah', 'petugas/tanggapan/' . $t->id_tanggapan . '/ubah',
            'petugas/provinsi', 'petugas/provinsi/baru', 'petugas/kabupaten-kota', 'petugas/kabupaten-kota?provinsi=' . $this->idProvinsi(), 'petugas/kabupaten-kota/baru',
            'petugas/warga', 'petugas/warga/baru', 'petugas/pengguna', 'petugas/pengguna/baru',
            'petugas/saran', 'petugas/log', 'petugas/rekap', 'petugas/rekap/cetak', 'petugas/profil', 'petugas/profil/password',
        ];

        foreach ($halaman as $path) {
            $this->sebagaiPetugas($admin)->buka($path)->assertStatus(200);
        }
    }

    public function testKotakMasukMenampilkanPelaporDanFilterStatus(): void
    {
        $warga = $this->buatWarga('sukarni');
        $this->buatLaporan($warga, 'Pungutan di loket tiga kantor pelayanan.');

        $html = $this->sebagaiPetugas($this->buatPetugas())->buka('petugas/laporan?status=belum_ditanggapi')->getBody();

        $this->assertStringContainsString('Pungutan di loket tiga kantor pelayanan.', $html);
        $this->assertStringContainsString($warga->nama, $html);
    }

    public function testMenanggapiLaporanMengubahStatus(): void
    {
        $laporan  = $this->buatLaporan($this->buatWarga());
        $operator = $this->buatPetugas();

        $this->sebagaiPetugas($operator)->kirim('petugas/laporan/' . $laporan->id_laporan . '/tanggapan', [
            'status_tanggapan' => 'proses', 'isi_tanggapan' => 'Sedang kami verifikasi.',
        ])->assertRedirectTo(url_to('petugas.laporan.detail', $laporan->id_laporan));

        $this->seeInDatabase('laporan', ['id_laporan' => $laporan->id_laporan, 'status_laporan' => 'proses']);
        $this->seeInDatabase('tanggapan', ['id_laporan' => $laporan->id_laporan, 'id_user' => $operator->getId()]);
    }

    public function testTransisiStatusIlegalDitolakLewatForm(): void
    {
        $laporan = $this->buatLaporan($this->buatWarga());

        $this->sebagaiPetugas($this->buatPetugas())->kirim('petugas/laporan/' . $laporan->id_laporan . '/tanggapan', [
            'status_tanggapan' => 'selesai', 'isi_tanggapan' => 'Langsung selesai.',
        ])->assertRedirect();

        $this->assertStringContainsString('tidak dapat diubah', (string) ($_SESSION['error'] ?? ''));
        $this->seeInDatabase('laporan', ['id_laporan' => $laporan->id_laporan, 'status_laporan' => 'belum_ditanggapi']);
    }

    public function testOperatorTidakBisaMenghapusLaporanDanTercatat(): void
    {
        $laporan  = $this->buatLaporan($this->buatWarga());
        $operator = $this->buatPetugas();

        $this->sebagaiPetugas($operator)->kirim('petugas/laporan/' . $laporan->id_laporan, [], 'DELETE')
            ->assertRedirectTo(url_to('petugas.dasbor'));

        $this->seeInDatabase('laporan', ['id_laporan' => $laporan->id_laporan]);
        $this->assertSame(1, $this->db->table('log')->where('id_user', $operator->getId())->like('isi_log', 'Ditolak')->countAllResults());
    }

    public function testAdminMenghapusLaporan(): void
    {
        $laporan = $this->buatLaporan($this->buatWarga());

        $this->sebagaiPetugas($this->buatPetugas('administrator', 'admin'))->kirim('petugas/laporan/' . $laporan->id_laporan, [], 'DELETE')
            ->assertRedirectTo(url_to('petugas.laporan'));

        $this->dontSeeInDatabase('laporan', ['id_laporan' => $laporan->id_laporan]);
    }

    public function testOperatorTidakBisaMenghapusTanggapan(): void
    {
        $laporan   = $this->buatLaporan($this->buatWarga());
        $operator  = $this->buatPetugas();
        $tanggapan = Services::tanggapan(false)->tambah($laporan, $operator, StatusLaporan::Diverifikasi, 'Ditinjau.');

        $this->sebagaiPetugas($operator)->kirim('petugas/tanggapan/' . $tanggapan->id_tanggapan, [], 'DELETE')
            ->assertRedirectTo(url_to('petugas.dasbor'));

        $this->seeInDatabase('tanggapan', ['id_tanggapan' => $tanggapan->id_tanggapan]);
    }

    public function testUbahStatusMassal(): void
    {
        $warga = $this->buatWarga();
        $a     = $this->buatLaporan($warga, 'Laporan pertama untuk aksi massal.');
        $b     = $this->buatLaporan($warga, 'Laporan kedua untuk aksi massal.');

        $this->sebagaiPetugas($this->buatPetugas())->kirim('petugas/laporan/status-massal', [
            'id' => [(string) $a->id_laporan, (string) $b->id_laporan], 'status_tanggapan' => 'proses', 'isi_tanggapan' => 'Semua sedang kami verifikasi.',
        ])->assertRedirect();

        $this->assertSame(2, $this->db->table('laporan')->where('status_laporan', 'proses')->countAllResults());
    }

    public function testMeneruskanLaporanWajibMenyebutInstansiTujuan(): void
    {
        $operator = $this->buatPetugas();
        $warga    = $this->buatWarga();
        $laporan  = $this->buatLaporan($warga);
        Services::tanggapan(false)->tambah($laporan, $operator, StatusLaporan::Diverifikasi, 'Diverifikasi.');
        Services::tanggapan(false)->tambah($this->muatUlang($laporan), $operator, StatusLaporan::Valid, 'Valid.');
        $url = 'petugas/laporan/' . $laporan->id_laporan . '/tanggapan';

        $this->sebagaiPetugas($operator)->kirim($url, ['status_tanggapan' => 'pengerjaan', 'isi_tanggapan' => 'Kami teruskan.'])->assertRedirect();
        $this->assertStringContainsString('instansi tujuan', (string) ($_SESSION['error'] ?? ''));
        $this->seeInDatabase('laporan', ['id_laporan' => $laporan->id_laporan, 'status_laporan' => 'valid']);

        $this->sebagaiPetugas($operator)->kirim($url, [
            'status_tanggapan' => 'pengerjaan', 'isi_tanggapan' => 'Kami teruskan.', 'instansi_tujuan' => 'Kejaksaan Negeri',
        ])->assertRedirectTo(url_to('petugas.laporan.detail', $laporan->id_laporan));
        $this->seeInDatabase('tanggapan', ['id_laporan' => $laporan->id_laporan, 'status_tanggapan' => 'pengerjaan', 'instansi_tujuan' => 'Kejaksaan Negeri']);

        $this->sebagaiWarga($warga)->buka('warga/laporan/' . $laporan->id_laporan)->assertSee('Kejaksaan Negeri');
    }

    public function testPelaporRahasiaTidakTerlihatOlehOperator(): void
    {
        $warga    = $this->buatWarga('sukarni');
        $laporan  = $this->buatLaporan($warga, 'Laporan dengan identitas dirahasiakan.', ['rahasia' => true]);
        $operator = $this->buatPetugas();

        foreach (['petugas/laporan', 'petugas/laporan/' . $laporan->id_laporan, 'petugas/laporan/' . $laporan->id_laporan . '/ubah', 'petugas/rekap', 'petugas/rekap/cetak'] as $path) {
            $html = $this->sebagaiPetugas($operator)->buka($path)->getBody();
            $this->assertStringContainsString('Dirahasiakan', $html, $path);
            $this->assertStringNotContainsString($warga->nama, $html, $path);
            $this->assertStringNotContainsString($warga->no_telepon, $html, $path);
        }
    }

    public function testOperatorTidakBisaMembukaIdentitas(): void
    {
        $laporan  = $this->buatLaporan($this->buatWarga(), 'Laporan rahasia.', ['rahasia' => true]);
        $operator = $this->buatPetugas();

        $this->sebagaiPetugas($operator)->kirim('petugas/laporan/' . $laporan->id_laporan . '/identitas')
            ->assertRedirectTo(url_to('petugas.dasbor'));

        $this->assertSame(0, $this->db->table('log')->like('isi_log', 'Membuka identitas')->countAllResults());
    }

    public function testAdministratorMembukaIdentitasDanTercatat(): void
    {
        $warga   = $this->buatWarga('sukarni');
        $laporan = $this->buatLaporan($warga, 'Laporan rahasia.', ['rahasia' => true]);
        $admin   = $this->buatPetugas('administrator', 'admin');

        $this->sebagaiPetugas($admin)->kirim('petugas/laporan/' . $laporan->id_laporan . '/identitas')
            ->assertRedirectTo(url_to('petugas.laporan.detail', $laporan->id_laporan));

        $this->lanjutkanSesi()->buka('petugas/laporan/' . $laporan->id_laporan)->assertSee($warga->nama);
        $this->seeInDatabase('log', ['id_user' => $admin->getId(), 'isi_log' => 'Membuka identitas pelapor laporan ' . $laporan->nomor_laporan]);
    }

    public function testRingkasanPublikMembuatLaporanTampilSetelahValid(): void
    {
        $operator = $this->buatPetugas();
        $laporan  = $this->buatLaporan($this->buatWarga());
        $url      = 'petugas/laporan/' . $laporan->id_laporan . '/publik';

        $this->sebagaiPetugas($operator)->kirim($url, ['ringkasan_publik' => 'Dugaan pungutan liar di kantor pelayanan.'], 'PUT')->assertRedirect();
        $this->assertStringNotContainsString('Dugaan pungutan liar di kantor pelayanan.', $this->buka('laporan')->getBody());

        Services::tanggapan(false)->tambah($this->muatUlang($laporan), $operator, StatusLaporan::Diverifikasi, 'Diverifikasi.');
        Services::tanggapan(false)->tambah($this->muatUlang($laporan), $operator, StatusLaporan::Valid, 'Valid.');
        $this->assertStringContainsString('Dugaan pungutan liar di kantor pelayanan.', $this->buka('laporan')->getBody());

        $this->sebagaiPetugas($operator)->kirim($url, ['hapus' => '1'], 'PUT')->assertRedirect();
        $this->assertStringNotContainsString('Dugaan pungutan liar di kantor pelayanan.', $this->buka('laporan')->getBody());
        $this->seeInDatabase('log', ['isi_log' => 'Mengatur ringkasan publik laporan ' . $laporan->nomor_laporan]);
        $this->seeInDatabase('log', ['isi_log' => 'Menyembunyikan laporan ' . $laporan->nomor_laporan]);
    }

    public function testRingkasanPublikTerlaluPendekDitolak(): void
    {
        $laporan = $this->buatLaporan($this->buatWarga());

        $this->sebagaiPetugas($this->buatPetugas())->kirim('petugas/laporan/' . $laporan->id_laporan . '/publik', ['ringkasan_publik' => 'Pendek'], 'PUT')->assertRedirect();

        $this->assertArrayHasKey('ringkasan_publik', $_SESSION['_ci_validation_errors'] ?? []);
        $this->seeInDatabase('laporan', ['id_laporan' => $laporan->id_laporan, 'ringkasan_publik' => null]);
    }

    public function testKotakMasukBisaDisaringJenisDanProvinsi(): void
    {
        $warga = $this->buatWarga();
        $this->buatLaporan($warga, 'Laporan suap di Jawa Timur.', ['id_kategori' => $this->idKategori('Suap-menyuap')]);
        $this->buatLaporan($warga, 'Laporan pungli di Jakarta.', ['id_kabupaten_kota' => $this->idKabupaten('31.71')]);

        $petugas = $this->buatPetugas();
        $jenis   = $this->sebagaiPetugas($petugas)->buka('petugas/laporan?kategori=' . $this->idKategori('Suap-menyuap'))->getBody();
        $this->assertStringContainsString('suap di Jawa Timur', $jenis);
        $this->assertStringNotContainsString('pungli di Jakarta', $jenis);

        $provinsi = $this->sebagaiPetugas($petugas)->buka('petugas/laporan?provinsi=' . $this->idProvinsi('31'))->getBody();
        $this->assertStringContainsString('pungli di Jakarta', $provinsi);
        $this->assertStringNotContainsString('suap di Jawa Timur', $provinsi);
    }

    public function testProvinsiYangMasihPunyaKabupatenTidakBisaDihapus(): void
    {
        $idProvinsi = $this->idProvinsi();

        $this->sebagaiPetugas($this->buatPetugas())->kirim('petugas/provinsi/' . $idProvinsi, [], 'DELETE')->assertRedirect();

        $this->seeInDatabase('provinsi', ['id_provinsi' => $idProvinsi]);
        $this->assertStringContainsString('masih memiliki kabupaten/kota', (string) ($_SESSION['error'] ?? ''));
    }

    public function testKabupatenGandaDalamProvinsiYangSamaDitolak(): void
    {
        $this->sebagaiPetugas($this->buatPetugas())->kirim('petugas/kabupaten-kota', ['id_provinsi' => $this->idProvinsi(), 'kabupaten_kota' => 'Kabupaten Pasuruan'])->assertRedirect();

        $this->assertSame(1, $this->db->table('kabupaten_kota')->where('kabupaten_kota', 'Kabupaten Pasuruan')->countAllResults());
    }

    public function testMenambahKabupatenKota(): void
    {
        $idProvinsi = $this->idProvinsi();

        $this->sebagaiPetugas($this->buatPetugas())->kirim('petugas/kabupaten-kota', ['id_provinsi' => $idProvinsi, 'kabupaten_kota' => 'Kota Contoh Baru'])
            ->assertRedirectTo(url_to('petugas.kabupaten_kota') . '?provinsi=' . $idProvinsi);

        $this->seeInDatabase('kabupaten_kota', ['kabupaten_kota' => 'Kota Contoh Baru', 'id_provinsi' => $idProvinsi]);
    }

    public function testOperatorTidakBisaMenghapusWarga(): void
    {
        $warga = $this->buatWarga();

        $this->sebagaiPetugas($this->buatPetugas())->kirim('petugas/warga/' . $warga->getId(), [], 'DELETE')
            ->assertRedirectTo(url_to('petugas.dasbor'));

        $this->seeInDatabase('warga', ['id_warga' => $warga->getId()]);
    }

    public function testAdminTidakBisaMenghapusAkunSendiri(): void
    {
        $admin = $this->buatPetugas('administrator', 'admin');

        $this->sebagaiPetugas($admin)->kirim('petugas/pengguna/' . $admin->getId(), [], 'DELETE')->assertRedirect();

        $this->seeInDatabase('user', ['id_user' => $admin->getId()]);
    }

    public function testAdministratorTerakhirTidakBisaDiturunkan(): void
    {
        $admin = $this->buatPetugas('administrator', 'admin');

        $this->sebagaiPetugas($admin)->kirim('petugas/pengguna/' . $admin->getId(), [
            'nama' => 'Admin', 'no_telepon' => '0812', 'jabatan' => 'operator',
        ], 'PUT')->assertRedirect();

        $this->seeInDatabase('user', ['id_user' => $admin->getId(), 'jabatan' => 'administrator']);
    }

    public function testRekapCetakMemuatPelaporDanTandaTangan(): void
    {
        $warga = $this->buatWarga('sukarni');
        $this->buatLaporan($warga);
        $admin = $this->buatPetugas('administrator', 'admin');

        $html = $this->sebagaiPetugas($admin)->buka('petugas/rekap/cetak')->getBody();

        $this->assertStringContainsString('REKAPITULASI LAPORAN', $html);
        $this->assertStringContainsString($warga->nama, $html);
        $this->assertStringContainsString($admin->nama, $html);
    }
}
