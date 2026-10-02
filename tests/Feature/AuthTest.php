<?php

namespace Tests\Feature;

use App\Services\AuthService;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\Security\Exceptions\SecurityException;
use Tests\Support\FeatureTestCase;

final class AuthTest extends FeatureTestCase
{
    public function testHalamanMasukTampil(): void
    {
        $hasil = $this->buka('masuk');
        $hasil->assertOK();
        $hasil->assertSee('Masuk', 'h1');
        $this->assertStringContainsString('name="username"', $hasil->getBody());
    }

    public function testWargaMasukDenganHashBcryptLama(): void
    {
        $warga = $this->buatWarga('andre123');

        $hasil = $this->kirim('masuk', ['username' => 'andre123', 'password' => 'rahasia123']);

        $hasil->assertRedirectTo(url_to('warga.beranda'));
        $hasil->assertSessionHas(AuthService::KEY_WARGA, $warga->getId());
    }

    public function testPesanGagalSamaUntukUsernameTidakAdaDanPasswordSalah(): void
    {
        $this->buatWarga('budi');

        $salahPassword = $this->kirim('masuk', ['username' => 'budi', 'password' => 'bukan-ini']);
        $salahPassword->assertRedirect();
        $pesan1 = $_SESSION['error'] ?? null;

        $tidakAda = $this->kirim('masuk', ['username' => 'tidak-ada', 'password' => 'bukan-ini']);
        $tidakAda->assertRedirect();
        $pesan2 = $_SESSION['error'] ?? null;

        $this->assertSame('Username atau kata sandi salah.', $pesan1);
        $this->assertSame($pesan1, $pesan2);
    }

    public function testPercobaanMasukDibatasi(): void
    {
        $this->buatWarga('budi');

        for ($i = 0; $i < 5; $i++) {
            $this->kirim('masuk', ['username' => 'budi', 'password' => 'salah']);
        }

        $this->kirim('masuk', ['username' => 'budi', 'password' => 'rahasia123'])->assertSessionMissing(AuthService::KEY_WARGA);
        $this->assertStringContainsString('Terlalu banyak percobaan', (string) ($_SESSION['error'] ?? ''));
    }

    public function testPetugasMasukKePanelDanTercatat(): void
    {
        $admin = $this->buatPetugas('administrator', 'admin');

        $hasil = $this->kirim('petugas/masuk', ['username' => 'admin', 'password' => 'rahasia123']);

        $hasil->assertRedirectTo(url_to('petugas.dasbor'));
        $hasil->assertSessionHas(AuthService::KEY_PETUGAS, $admin->getId());
        $this->seeInDatabase('log', ['id_user' => $admin->getId(), 'isi_log' => 'Masuk ke panel admin']);
    }

    public function testAkunWargaTidakBisaMasukSebagaiPetugas(): void
    {
        $this->buatWarga('budi');

        $this->kirim('petugas/masuk', ['username' => 'budi', 'password' => 'rahasia123'])
            ->assertSessionMissing(AuthService::KEY_PETUGAS);
    }

    public function testPortalWargaMembutuhkanLogin(): void
    {
        $this->buka('warga')->assertRedirectTo(url_to('masuk'));
    }

    public function testPanelPetugasMembutuhkanLoginPetugas(): void
    {
        $this->sebagaiWarga($this->buatWarga())->buka('petugas')->assertRedirectTo(url_to('petugas.masuk'));
    }

    public function testPenggunaYangSudahMasukDialihkanDariHalamanMasuk(): void
    {
        $this->sebagaiWarga($this->buatWarga())->buka('masuk')->assertRedirectTo(url_to('warga.beranda'));
        $this->sebagaiPetugas($this->buatPetugas())->buka('daftar')->assertRedirectTo(url_to('petugas.dasbor'));
    }

    public function testOperatorDitolakDariMenuAdministratorDanTercatat(): void
    {
        $operator = $this->buatPetugas('operator', 'operator1');

        $this->sebagaiPetugas($operator)->buka('petugas/pengguna')->assertRedirectTo(url_to('petugas.dasbor'));

        $this->assertSame(1, $this->db->table('log')->where('id_user', $operator->getId())->like('isi_log', 'Ditolak')->countAllResults());
    }

    public function testDaftarMembuatAkunLaluMasuk(): void
    {
        $hasil = $this->kirim('daftar', [
            'nama'                => 'siti AMINAH',
            'username'            => 'siti.aminah',
            'no_telepon'          => '081234567890',
            'alamat'              => 'Jl. Contoh No. 10',
            'password'            => 'rahasia123',
            'password_konfirmasi' => 'rahasia123',
        ]);

        $hasil->assertRedirectTo(url_to('warga.beranda'));
        $this->seeInDatabase('masyarakat', ['username' => 'siti.aminah', 'nama' => 'Siti Aminah']);
    }

    public function testUsernameYangSudahDipakaiDitolak(): void
    {
        $this->buatWarga('budi');

        $this->kirim('daftar', [
            'nama'     => 'Budi Lain', 'username' => 'budi', 'no_telepon' => '081234567890', 'alamat' => 'Jl. Contoh',
            'password' => 'rahasia123', 'password_konfirmasi' => 'rahasia123',
        ])->assertRedirect();

        $this->assertSame(1, $this->db->table('masyarakat')->where('username', 'budi')->countAllResults());
        $this->assertArrayHasKey('username', $_SESSION['_ci_validation_errors'] ?? []);
    }

    public function testPasswordKurangDari8KarakterDitolak(): void
    {
        $this->kirim('daftar', [
            'nama'     => 'Budi', 'username' => 'budi', 'no_telepon' => '081234567890', 'alamat' => 'Jl. Contoh',
            'password' => 'abc', 'password_konfirmasi' => 'abc',
        ]);

        $this->dontSeeInDatabase('masyarakat', ['username' => 'budi']);
    }

    public function testPostTanpaTokenCsrfDitolak(): void
    {
        $this->buatWarga('budi');

        $this->expectException(SecurityException::class);
        $this->kirim('masuk', ['username' => 'budi', 'password' => 'rahasia123'], 'POST', false);
    }

    public function testKeluarLewatGetTidakAda(): void
    {
        $this->expectException(PageNotFoundException::class);
        $this->sebagaiWarga($this->buatWarga())->buka('keluar');
    }

    public function testKeluarLewatPost(): void
    {
        $hasil = $this->sebagaiWarga($this->buatWarga())->kirim('keluar');

        $hasil->assertRedirectTo(url_to('beranda'));
        $hasil->assertSessionMissing(AuthService::KEY_WARGA);
    }
}
