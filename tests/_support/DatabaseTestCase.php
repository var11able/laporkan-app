<?php

namespace Tests\Support;

use App\Database\Seeds\WilayahSeeder;
use App\Entities\Masyarakat;
use App\Entities\Pengaduan;
use App\Entities\User;
use App\Enums\StatusPengaduan;
use App\Models\MasyarakatModel;
use App\Models\PengaduanModel;
use App\Models\UserModel;
use App\Services\UploadService;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use Config\Services;

abstract class DatabaseTestCase extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate     = true;
    protected $migrateOnce = true;
    protected $refresh     = true;
    protected $namespace   = 'App';
    protected string $uploadDir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->kosongkanTabel();
        $this->seed(WilayahSeeder::class);

        $this->uploadDir = sys_get_temp_dir() . '/laporkan-test-uploads-' . getmypid();
        Services::injectMock('upload', new UploadService($this->uploadDir));
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Services::reset(true);
        helper('filesystem');
        if (is_dir($this->uploadDir)) {
            delete_files($this->uploadDir, true);
        }
    }

    protected function buatWarga(string $username = 'budi'): Masyarakat
    {
        $model = new MasyarakatModel();
        $id    = $model->insert([
            'nama'       => 'Warga ' . $username,
            'username'   => $username,
            'password'   => password_hash('rahasia123', PASSWORD_DEFAULT),
            'no_telepon' => '081234567890',
            'alamat'     => 'Jl. Contoh No. 1',
        ]);
        $this->assertNotFalse($id, implode(' ', $model->errors()));

        return $model->find($id);
    }

    protected function buatPetugas(string $jabatan = 'operator', string $username = 'petugas'): User
    {
        $model = new UserModel();
        $id    = $model->insert([
            'nama'       => ucfirst($jabatan),
            'username'   => $username,
            'password'   => password_hash('rahasia123', PASSWORD_DEFAULT),
            'no_telepon' => '081234567891',
            'jabatan'    => $jabatan,
        ]);
        $this->assertNotFalse($id, implode(' ', $model->errors()));

        return $model->find($id);
    }

    protected function idKabupaten(string $kode = '35.14'): int
    {
        return (int) $this->db->table('kabupaten_kota')->where('kode', $kode)->get()->getRow()->id_kabupaten_kota;
    }

    protected function idProvinsi(string $kode = '35'): int
    {
        return (int) $this->db->table('provinsi')->where('kode', $kode)->get()->getRow()->id_provinsi;
    }

    protected function idKategori(string $nama = 'Pungutan liar & pemerasan'): int
    {
        return (int) $this->db->table('kategori')->where('kategori', $nama)->get()->getRow()->id_kategori;
    }

    protected function buatLaporan(Masyarakat $warga, string $isi = 'Petugas loket meminta uang di luar biaya resmi.', array $tambahan = []): Pengaduan
    {
        return Services::pengaduan(false)->buat($tambahan + [
            'isi_laporan'       => $isi,
            'id_kabupaten_kota' => $this->idKabupaten(),
            'id_kategori'       => $this->idKategori(),
            'instansi_terlapor' => 'Kantor pelayanan contoh',
        ], $warga->getId());
    }

    protected function jadikanPublik(Pengaduan $pengaduan, User $petugas, string $ringkasan = 'Dugaan pungutan liar di sebuah kantor pelayanan.'): Pengaduan
    {
        Services::tanggapan(false)->tambah($pengaduan, $petugas, StatusPengaduan::Diverifikasi, 'Diverifikasi.');
        Services::tanggapan(false)->tambah($this->muatUlang($pengaduan), $petugas, StatusPengaduan::Valid, 'Valid.');
        Services::pengaduan(false)->aturRingkasanPublik($this->muatUlang($pengaduan), $petugas, $ringkasan);

        return $this->muatUlang($pengaduan);
    }

    protected function muatUlang(Pengaduan $pengaduan): Pengaduan
    {
        return (new PengaduanModel())->cariDenganRelasi($pengaduan->id_pengaduan);
    }

    private function kosongkanTabel(): void
    {
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');

        foreach (['log', 'bukti_pengaduan', 'tanggapan', 'pengaduan', 'saran', 'masyarakat', 'user', 'kabupaten_kota', 'provinsi', 'kelurahan', 'kecamatan'] as $tabel) {
            $this->db->table($tabel)->truncate();
        }

        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
    }
}
