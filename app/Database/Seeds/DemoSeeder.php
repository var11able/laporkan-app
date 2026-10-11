<?php

namespace App\Database\Seeds;

use App\Enums\StatusLaporan;
use App\Models\KabupatenKotaModel;
use App\Models\KategoriModel;
use App\Models\LaporanModel;
use App\Models\UserModel;
use App\Models\WargaModel;
use CodeIgniter\Database\Seeder;
use Config\Services;
use RuntimeException;

class DemoSeeder extends Seeder
{
    private const PASSWORD = 'demo12345';

    public function run(): void
    {
        if (ENVIRONMENT === 'production') {
            throw new RuntimeException('DemoSeeder tidak boleh dijalankan di production.');
        }

        $this->call(WilayahSeeder::class);

        $users = new UserModel();
        $warga = new WargaModel();

        $this->pastikan($users, 'admin.demo', [
            'nama' => 'Admin Demo', 'no_telepon' => '081200000000', 'jabatan' => 'administrator',
        ]);
        $idOperator = $this->pastikan($users, 'operator.demo', [
            'nama' => 'Operator Demo', 'no_telepon' => '081200000001', 'jabatan' => 'operator',
        ]);
        $idWarga = $this->pastikan($warga, 'warga.demo', [
            'nama' => 'Pelapor Demo', 'no_telepon' => '081300000001', 'alamat' => 'Jl. Contoh No. 1',
        ]);
        $this->pastikan($warga, 'warga.demo2', [
            'nama' => 'Pelapor Demo Dua', 'no_telepon' => '081300000002', 'alamat' => 'Jl. Contoh No. 2',
        ]);

        $kategori       = array_flip((new KategoriModel())->opsi());
        $wilayah        = array_column((new KabupatenKotaModel())->select('id_kabupaten_kota')->orderBy('id_kabupaten_kota')->findAll(6), 'id_kabupaten_kota');
        $operator       = $users->find($idOperator);
        $layananLaporan = Services::laporan(false);
        $tanggapan      = Services::tanggapan(false);

        $laporan = [
            [
                'kategori'  => 'Pungutan liar & pemerasan',
                'isi'       => 'Saat mengurus surat keterangan di kantor pelayanan, petugas loket meminta uang Rp150.000 di luar biaya resmi agar surat selesai hari itu.',
                'instansi'  => 'Kantor pelayanan administrasi kependudukan (contoh)',
                'status'    => [],
                'ringkasan' => null,
            ],
            [
                'kategori'  => 'Suap-menyuap',
                'isi'       => 'Beberapa peserta ujian perekrutan pegawai mengaku diminta membayar sejumlah uang kepada perantara agar dinyatakan lulus.',
                'instansi'  => 'Panitia seleksi pegawai daerah (contoh)',
                'status'    => [StatusLaporan::Diverifikasi],
                'ringkasan' => null,
            ],
            [
                'kategori'  => 'Kecurangan pengadaan barang/jasa',
                'isi'       => 'Harga pengadaan laptop untuk sekolah tercatat jauh di atas harga pasar untuk spesifikasi yang sama, dan pemenangnya sudah diumumkan sebelum tender dibuka.',
                'instansi'  => 'Dinas pendidikan (contoh)',
                'status'    => [StatusLaporan::Diverifikasi, StatusLaporan::Valid],
                'ringkasan' => 'Dugaan penggelembungan harga dan pengaturan pemenang dalam pengadaan perangkat sekolah.',
            ],
            [
                'kategori'  => 'Penggelapan dalam jabatan',
                'isi'       => 'Dana bantuan untuk kelompok tani tidak pernah diterima penuh; sebagian anggota hanya menerima setengah dari jumlah yang tercatat di laporan.',
                'instansi'  => 'Pengelola dana bantuan desa (contoh)',
                'status'    => [StatusLaporan::Diverifikasi, StatusLaporan::Valid, StatusLaporan::Diteruskan],
                'ringkasan' => 'Dugaan pemotongan dana bantuan kelompok tani oleh pengelolanya.',
            ],
            [
                'kategori'  => 'Gratifikasi',
                'isi'       => 'Pejabat penerbit izin menerima hadiah kendaraan dari pemohon izin beberapa minggu sebelum izin usahanya terbit.',
                'instansi'  => 'Kantor perizinan daerah (contoh)',
                'status'    => [StatusLaporan::Diverifikasi, StatusLaporan::Valid, StatusLaporan::Diteruskan, StatusLaporan::Selesai],
                'ringkasan' => 'Dugaan gratifikasi berupa kendaraan kepada pejabat penerbit izin usaha.',
            ],
            [
                'kategori'  => 'Lainnya',
                'isi'       => 'Pelayanan di kantor kelurahan sangat lambat dan antreannya panjang.',
                'instansi'  => 'Kantor kelurahan (contoh)',
                'status'    => [StatusLaporan::TidakValid],
                'ringkasan' => null,
            ],
        ];

        $isiTanggapan = [
            StatusLaporan::Diverifikasi->value => 'Laporan sudah kami terima dan sedang kami verifikasi.',
            StatusLaporan::Valid->value        => 'Laporan dan bukti sudah kami periksa. Laporan cukup lengkap dan sedang kami siapkan untuk diteruskan.',
            StatusLaporan::Diteruskan->value   => 'Laporan sudah kami teruskan ke instansi yang berwenang.',
            StatusLaporan::Selesai->value      => 'Instansi tujuan menyampaikan bahwa laporan sudah ditindaklanjuti. Terima kasih atas keberanian Anda.',
            StatusLaporan::TidakValid->value   => 'Laporan ini tentang kualitas layanan, bukan dugaan korupsi. Silakan sampaikan melalui SP4N-LAPOR! di lapor.go.id.',
        ];

        $hari = count($laporan) * 2;

        foreach ($laporan as $i => $l) {
            $baru = $layananLaporan->buat([
                'id_kategori'       => $kategori[$l['kategori']] ?? null,
                'isi_laporan'       => $l['isi'],
                'instansi_terlapor' => $l['instansi'],
                'id_kabupaten_kota' => $wilayah[$i % max(1, count($wilayah))] ?? null,
                'waktu_kejadian'    => date('Y-m-d', strtotime('-' . ($hari + 7) . ' days')),
                'rahasia'           => $i % 2 === 0,
            ], $idWarga);

            (new LaporanModel())->builder()
                ->where('id_laporan', $baru->id_laporan)
                ->update(['tgl_laporan' => date('Y-m-d H:i:s', strtotime("-{$hari} days")), 'ringkasan_publik' => $l['ringkasan']]);
            $hari -= 2;

            foreach ($l['status'] as $status) {
                $baru = (new LaporanModel())->find($baru->id_laporan);
                $tanggapan->tambah($baru, $operator, $status, $isiTanggapan[$status->value], null, $status === StatusLaporan::Diteruskan ? 'Komisi Pemberantasan Korupsi (KPK)' : null);
            }
        }
    }

    private function pastikan(UserModel|WargaModel $model, string $username, array $data): int
    {
        $akun = $model->cariUsername($username);

        if ($akun !== null) {
            return $akun->getId();
        }

        $id = $model->insert($data + ['username' => $username, 'password' => password_hash(self::PASSWORD, PASSWORD_DEFAULT)]);

        if ($id === false) {
            throw new RuntimeException('Gagal membuat akun ' . $username . ': ' . implode(' ', $model->errors()));
        }

        return (int) $id;
    }
}
