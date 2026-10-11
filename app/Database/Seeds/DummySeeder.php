<?php

namespace App\Database\Seeds;

use App\Enums\StatusLaporan;
use App\Models\KabupatenKotaModel;
use App\Models\KategoriModel;
use App\Models\LaporanModel;
use App\Models\UserModel;
use App\Models\WargaModel;
use App\Services\LaporanService;
use CodeIgniter\Database\Seeder;
use Config\Services;
use RuntimeException;

class DummySeeder extends Seeder
{
    private const PASSWORD       = 'demo12345';
    private const JUMLAH_WARGA   = 250;
    private const JUMLAH_LAPOR   = 1500;
    private const JUMLAH_SARAN   = 60;
    private const BOBOT_PROVINSI = [
        '11' => 54, '12' => 154, '13' => 58, '14' => 66, '15' => 37, '16' => 87, '17' => 21, '18' => 93, '19' => 15,
        '21' => 22, '31' => 107, '32' => 499, '33' => 375, '34' => 37, '35' => 415, '36' => 123, '51' => 44, '52' => 56,
        '53' => 55, '61' => 56, '62' => 27, '63' => 42, '64' => 39, '65' => 7, '71' => 27, '72' => 31, '73' => 94,
        '74' => 27, '75' => 12, '76' => 15, '81' => 19, '82' => 13, '91' => 10, '92' => 6, '93' => 5, '94' => 14,
        '95' => 14, '96' => 6,
    ];
    private const NAMA_DEPAN = [
        'Agus', 'Budi', 'Citra', 'Dewi', 'Eko', 'Fitri', 'Gilang', 'Hendra', 'Indah', 'Joko', 'Kartika', 'Lestari',
        'Muhammad', 'Nur', 'Oktaviani', 'Putri', 'Rahmat', 'Siti', 'Taufik', 'Utami', 'Wahyu', 'Yusuf', 'Zainal',
        'Andi', 'Bayu', 'Dimas', 'Fajar', 'Hani', 'Intan', 'Kurniawan', 'Maya', 'Nanda', 'Rina', 'Sari', 'Teguh',
        'Wulan', 'Yogi', 'Ayu', 'Rizky', 'Dian', 'Ahmad', 'Ratna', 'Bambang', 'Sri', 'Lukman', 'Nia', 'Arif',
    ];
    private const NAMA_BELAKANG = [
        'Saputra', 'Wijaya', 'Pratama', 'Santoso', 'Hidayat', 'Kusuma', 'Lubis', 'Nasution', 'Siregar', 'Harahap',
        'Setiawan', 'Rahman', 'Gunawan', 'Simanjuntak', 'Sihombing', 'Purnomo', 'Susanto', 'Wibowo', 'Halim',
        'Sitompul', 'Nugroho', 'Firmansyah', 'Ramadhan', 'Maharani', 'Anggraini', 'Permana', 'Hutapea', 'Tanjung',
    ];
    private const JALAN    = ['Merdeka', 'Sudirman', 'Diponegoro', 'Gatot Subroto', 'Ahmad Yani', 'Pemuda', 'Pahlawan', 'Kartini', 'Veteran', 'Imam Bonjol', 'Hasanuddin', 'Cendrawasih', 'Melati', 'Kenanga'];
    private const INSTANSI = [
        'Dinas Kependudukan dan Pencatatan Sipil', 'Kantor Kelurahan', 'Kantor Kecamatan', 'Dinas Pendidikan', 'Dinas Kesehatan',
        'RSUD', 'Puskesmas', 'Dinas Pekerjaan Umum dan Penataan Ruang', 'Dinas Perhubungan', 'Kantor Pertanahan',
        'Dinas Penanaman Modal dan PTSP', 'Badan Pengelola Keuangan dan Aset Daerah', 'Pemerintah Desa', 'Samsat',
        'Dinas Sosial', 'Dinas Lingkungan Hidup', 'Satpol PP', 'Dinas Perindustrian dan Perdagangan', 'SMA Negeri', 'SMP Negeri',
    ];
    private const PIHAK = [
        'Petugas loket pelayanan', 'Kepala seksi', 'Kepala bidang', 'Sekretaris dinas', 'Kepala desa', 'Bendahara desa',
        'Panitia pengadaan', 'Pejabat pembuat komitmen', 'Oknum pegawai honorer', 'Kepala sekolah', 'Petugas lapangan',
        'Lurah', 'Camat', 'Staf bagian keuangan', 'Kepala UPT',
    ];
    private const TUJUAN = [
        'Komisi Pemberantasan Korupsi (KPK)', 'Kejaksaan Negeri', 'Kejaksaan Tinggi', 'Kepolisian Negara Republik Indonesia (Polri)',
        'Inspektorat kabupaten/kota', 'Inspektorat provinsi', 'Inspektorat Jenderal kementerian terkait',
        'Badan Pengawasan Keuangan dan Pembangunan (BPKP)', 'Ombudsman Republik Indonesia',
    ];
    private const TEMPLAT = [
        'Pungutan liar & pemerasan' => [
            'Saat mengurus {urusan} di {instansi}, {pihak} meminta uang Rp{nominal} di luar biaya resmi agar berkas selesai lebih cepat. Tanpa uang tersebut berkas dibilang "masih antre" selama berminggu-minggu.',
            'Warga yang mengurus {urusan} di {instansi} diminta membayar Rp{nominal} oleh {pihak}. Tidak ada kuitansi, dan pembayaran diminta lewat transfer ke rekening pribadi.',
            '{pihak} di {instansi} menahan {urusan} milik saya dan meminta "uang terima kasih" Rp{nominal}. Hal yang sama dialami beberapa tetangga saya.',
        ],
        'Suap-menyuap' => [
            'Peserta seleksi di {instansi} mengaku diminta menyetor Rp{nominal} kepada perantara yang mengaku dekat dengan {pihak} agar dinyatakan lulus.',
            'Pengusaha setempat memberikan uang Rp{nominal} kepada {pihak} di {instansi} agar pelanggaran izinnya tidak ditindak.',
            '{pihak} di {instansi} menerima amplop dari pihak rekanan sebelum hasil penilaian diumumkan. Nilainya diperkirakan Rp{nominal}.',
        ],
        'Gratifikasi' => [
            '{pihak} di {instansi} menerima hadiah berupa {hadiah} dari pemohon izin beberapa minggu sebelum izinnya terbit.',
            'Menjelang hari raya, rekanan proyek mengirim {hadiah} ke rumah {pihak} di {instansi}. Nilainya ditaksir Rp{nominal} dan tidak pernah dilaporkan.',
            '{pihak} di {instansi} menerima fasilitas perjalanan dan {hadiah} dari vendor yang sedang mengikuti tender.',
        ],
        'Kecurangan pengadaan barang/jasa' => [
            'Harga pengadaan {barang} di {instansi} tercatat jauh di atas harga pasar. Selisihnya diperkirakan Rp{nominal}, dan pemenang tender sudah diketahui sebelum tender dibuka.',
            'Proyek {barang} di {instansi} dikerjakan tidak sesuai spesifikasi. Material yang dipakai lebih murah daripada yang tercantum di kontrak.',
            'Paket pengadaan {barang} di {instansi} dipecah menjadi beberapa paket kecil agar bisa ditunjuk langsung ke rekanan yang sama.',
        ],
        'Penggelapan dalam jabatan' => [
            'Dana {dana} yang dikelola {pihak} di {instansi} tidak disalurkan penuh. Penerima hanya menerima sebagian, total selisih sekitar Rp{nominal}.',
            '{pihak} di {instansi} diduga memakai dana {dana} untuk keperluan pribadi. Laporan pertanggungjawabannya berisi kegiatan yang tidak pernah ada.',
            'Iuran {dana} yang dipungut dari warga oleh {pihak} tidak pernah disetorkan ke kas {instansi}. Jumlahnya diperkirakan Rp{nominal}.',
        ],
        'Penyalahgunaan wewenang' => [
            '{pihak} di {instansi} memakai kendaraan dan fasilitas dinas untuk usaha pribadinya hampir setiap hari.',
            '{pihak} di {instansi} menerbitkan izin untuk lahan yang statusnya masih sengketa, diduga karena ada kepentingan pribadi.',
            '{pihak} di {instansi} memindahkan anggaran kegiatan ke pos lain tanpa persetujuan, lalu dananya tidak jelas penggunaannya.',
        ],
        'Benturan kepentingan' => [
            'Rekanan yang memenangkan proyek {barang} di {instansi} ternyata milik keluarga {pihak} yang menjadi panitia.',
            '{pihak} di {instansi} ikut menilai proposal hibah yang diajukan oleh yayasan yang ia pimpin sendiri.',
            'Pengadaan {barang} di {instansi} selalu dimenangkan perusahaan milik kerabat {pihak}.',
        ],
        'Lainnya' => [
            'Ada kejanggalan pada laporan keuangan {instansi}. Beberapa kegiatan yang tercatat tidak pernah dilaksanakan di lapangan.',
            'Pelayanan di {instansi} sangat lambat dan petugas sering tidak di tempat pada jam kerja.',
            'Saya mendengar kabar bahwa {pihak} di {instansi} meminta setoran dari bawahannya setiap bulan.',
        ],
    ];
    private const URUSAN    = ['KTP elektronik', 'kartu keluarga', 'akta kelahiran', 'sertifikat tanah', 'izin mendirikan bangunan', 'surat keterangan usaha', 'balik nama kendaraan', 'surat pindah', 'izin usaha', 'legalisir ijazah'];
    private const HADIAH    = ['satu unit sepeda motor', 'paket umrah', 'jam tangan mewah', 'telepon genggam keluaran terbaru', 'voucher belanja', 'parsel berisi emas batangan'];
    private const BARANG    = ['laptop sekolah', 'alat kesehatan', 'perbaikan jalan desa', 'pembangunan drainase', 'seragam sekolah', 'mobil ambulans', 'renovasi gedung kantor', 'bibit dan pupuk', 'lampu penerangan jalan'];
    private const DANA      = ['bantuan sosial', 'dana desa', 'bantuan kelompok tani', 'beasiswa siswa miskin', 'bantuan UMKM', 'iuran kebersihan', 'BOS sekolah'];
    private const TANGGAPAN = [
        'proses' => [
            'Laporan sudah kami terima dan sedang kami verifikasi. Kami akan menghubungi Anda bila perlu keterangan tambahan.',
            'Terima kasih atas laporannya. Tim kami sedang memeriksa kronologi dan bukti yang Anda kirim.',
        ],
        'valid' => [
            'Laporan dan bukti sudah kami periksa. Laporan cukup lengkap dan sedang kami siapkan untuk diteruskan.',
            'Hasil verifikasi menunjukkan laporan ini layak ditindaklanjuti. Kami sedang menyusun berkas untuk instansi berwenang.',
        ],
        'pengerjaan' => [
            'Laporan sudah kami teruskan ke instansi yang berwenang beserta bukti pendukungnya.',
            'Berkas laporan sudah kami serahkan. Perkembangan selanjutnya akan kami sampaikan di sini.',
        ],
        'selesai' => [
            'Instansi tujuan menyampaikan bahwa laporan sudah ditindaklanjuti. Terima kasih atas keberanian Anda.',
            'Instansi berwenang sudah melakukan pemeriksaan dan memberikan sanksi kepada pihak terkait.',
        ],
        'tidak_valid' => [
            'Laporan ini tentang kualitas layanan, bukan dugaan korupsi. Silakan sampaikan melalui SP4N-LAPOR! di lapor.go.id.',
            'Kronologi belum menyebutkan waktu, tempat, dan pihak yang terlibat. Silakan kirim laporan baru dengan keterangan yang lebih lengkap.',
            'Kami tidak menemukan cukup petunjuk untuk menindaklanjuti laporan ini. Anda dapat melapor kembali bila memiliki bukti tambahan.',
        ],
    ];
    private const SARAN = [
        'Tolong tambahkan fitur notifikasi lewat WhatsApp ketika status laporan berubah.',
        'Aplikasinya mudah dipakai, terima kasih. Semoga laporan benar-benar ditindaklanjuti.',
        'Mohon proses verifikasi dipercepat, laporan saya sudah seminggu belum diperiksa.',
        'Sebaiknya ada panduan contoh bukti apa saja yang bisa dilampirkan.',
        'Halaman lacak laporan sangat membantu, seperti melacak paket.',
        'Tolong tambahkan pilihan bahasa daerah untuk warga yang kurang lancar berbahasa Indonesia.',
        'Ukuran maksimal unggahan video sebaiknya diperbesar.',
        'Saya ingin bisa menyimpan laporan sebagai draf lalu melanjutkannya di HP lain.',
        'Terima kasih sudah menjaga identitas pelapor. Itu yang membuat saya berani melapor.',
        'Mohon tampilkan statistik laporan per kabupaten/kota di halaman depan.',
    ];

    public function run(): void
    {
        if (ENVIRONMENT === 'production') {
            throw new RuntimeException('DummySeeder tidak boleh dijalankan di production.');
        }

        mt_srand(20261005);

        $this->call(WilayahSeeder::class);

        $petugas  = $this->buatPetugas();
        $warga    = $this->buatWarga();
        $kategori = array_flip((new KategoriModel())->opsi());
        $wilayah  = $this->wilayahBerbobot();

        $laporan   = Services::laporan(false);
        $tanggapan = Services::tanggapan(false);
        $sekarang  = time();

        for ($i = 0; $i < self::JUMLAH_LAPOR; $i++) {
            $umurHari = (int) floor(365 * (1 - sqrt(mt_rand() / mt_getrandmax())));
            $waktu    = $sekarang - $umurHari * 86400 - mt_rand(0, 86399);
            $waktu    = min($waktu, $sekarang - 600);
            $namaKat  = array_rand(self::TEMPLAT);
            $isi      = $this->isiLaporan($namaKat);

            $baru = $laporan->buat([
                'id_kategori'        => $kategori[$namaKat] ?? null,
                'isi_laporan'        => $isi['teks'],
                'instansi_terlapor'  => $isi['instansi'],
                'pihak_terlapor'     => mt_rand(1, 100) <= 70 ? $isi['pihak'] : null,
                'id_kabupaten_kota'  => $this->pilihWilayah($wilayah),
                'waktu_kejadian'     => date('Y-m-d', $waktu - mt_rand(1, 60) * 86400),
                'perkiraan_kerugian' => mt_rand(1, 100) <= 60 ? (string) $isi['kerugian'] : null,
                'rahasia'            => mt_rand(1, 100) <= 65,
            ], $warga[array_rand($warga)]);

            $id = $baru->id_laporan;
            (new LaporanModel())->builder()->where('id_laporan', $id)->update([
                'tgl_laporan'   => date('Y-m-d H:i:s', $waktu),
                'nomor_laporan' => LaporanService::nomorLaporan($id, (int) date('Y', $waktu)),
            ]);

            $jejak = $this->jejakStatus($umurHari);
            $t     = $waktu;

            foreach ($jejak as $status) {
                $t     = min($sekarang - 60, $t + mt_rand(6, 120) * 3600);
                $oleh  = $petugas[array_rand($petugas)];
                $dapat = $tanggapan->tambah(
                    (new LaporanModel())->find($id),
                    $oleh,
                    $status,
                    self::TANGGAPAN[$status->value][array_rand(self::TANGGAPAN[$status->value])],
                    null,
                    $status === StatusLaporan::Diteruskan ? self::TUJUAN[array_rand(self::TUJUAN)] : null,
                );
                $tgl = date('Y-m-d H:i:s', $t);
                $this->db->table('tanggapan')->where('id_tanggapan', $dapat->id_tanggapan)->update(['tgl_tanggapan' => $tgl]);
                $idLog = $this->db->table('log')->selectMax('id_log')->where('id_user', $oleh->getId())->get()->getRow()->id_log;
                $this->db->table('log')->where('id_log', $idLog)->update(['tgl_log' => $tgl]);
            }

            $akhir = end($jejak) ?: StatusLaporan::Baru;
            (new LaporanModel())->builder()->where('id_laporan', $id)->update([
                'updated_at'       => date('Y-m-d H:i:s', $t),
                'ringkasan_publik' => $akhir->bisaTampilPublik() && mt_rand(1, 100) <= 85 ? $this->ringkasan($namaKat, $isi) : null,
            ]);
        }

        $this->buatSaran($sekarang);
        cache()->delete(config('PetaSebaran')->kunciCache);
    }

    private function wilayahBerbobot(): array
    {
        $baris = (new KabupatenKotaModel())
            ->select('kabupaten_kota.id_kabupaten_kota, provinsi.kode')
            ->join('provinsi', 'provinsi.id_provinsi = kabupaten_kota.id_provinsi')
            ->findAll();
        $hasil = [];

        foreach ($baris as $b) {
            $hasil[(string) $b['kode']]['kab'][] = (int) $b['id_kabupaten_kota'];
            $hasil[(string) $b['kode']]['bobot'] = self::BOBOT_PROVINSI[(string) $b['kode']] ?? 10;
        }

        return $hasil;
    }

    private function pilihWilayah(array $wilayah): int
    {
        $titik = mt_rand(1, array_sum(array_column($wilayah, 'bobot')));

        foreach ($wilayah as $w) {
            $titik -= $w['bobot'];

            if ($titik <= 0) {
                return $w['kab'][array_rand($w['kab'])];
            }
        }

        $akhir = end($wilayah);

        return $akhir['kab'][array_rand($akhir['kab'])];
    }

    private function buatPetugas(): array
    {
        $users = new UserModel();
        $akun  = [
            'operator.andi' => ['nama' => 'Andi Pratama', 'jabatan' => 'operator'],
            'operator.sari' => ['nama' => 'Sari Wulandari', 'jabatan' => 'operator'],
            'operator.bayu' => ['nama' => 'Bayu Nugroho', 'jabatan' => 'operator'],
            'admin.ratna'   => ['nama' => 'Ratna Kusuma', 'jabatan' => 'administrator'],
        ];
        $hasil = [];
        $sandi = password_hash(self::PASSWORD, PASSWORD_DEFAULT);

        foreach ($akun as $username => $data) {
            $ada = $users->cariUsername($username);
            $id  = $ada?->getId() ?? $users->insert($data + ['username' => $username, 'password' => $sandi, 'no_telepon' => $this->telepon()]);

            if ($id === false) {
                throw new RuntimeException('Gagal membuat akun ' . $username . ': ' . implode(' ', $users->errors()));
            }

            $hasil[] = $users->find((int) $id);
        }

        return $hasil;
    }

    private function buatWarga(): array
    {
        $model = new WargaModel();
        $sandi = password_hash(self::PASSWORD, PASSWORD_DEFAULT);
        $hasil = [];

        for ($i = 1; $i <= self::JUMLAH_WARGA; $i++) {
            $username = sprintf('warga%03d', $i);
            $ada      = $model->cariUsername($username);

            if ($ada !== null) {
                $hasil[] = $ada->getId();

                continue;
            }

            $id = $model->insert([
                'nama'       => self::NAMA_DEPAN[array_rand(self::NAMA_DEPAN)] . ' ' . self::NAMA_BELAKANG[array_rand(self::NAMA_BELAKANG)],
                'username'   => $username,
                'password'   => $sandi,
                'no_telepon' => $this->telepon(),
                'alamat'     => 'Jl. ' . self::JALAN[array_rand(self::JALAN)] . ' No. ' . mt_rand(1, 250),
            ]);

            if ($id === false) {
                throw new RuntimeException('Gagal membuat akun ' . $username . ': ' . implode(' ', $model->errors()));
            }

            $hasil[] = (int) $id;
        }

        return $hasil;
    }

    private function buatSaran(int $sekarang): void
    {
        for ($i = 0; $i < self::JUMLAH_SARAN; $i++) {
            $anonim = mt_rand(1, 100) <= 40;
            $this->db->table('saran')->insert([
                'nama'       => $anonim ? null : self::NAMA_DEPAN[array_rand(self::NAMA_DEPAN)] . ' ' . self::NAMA_BELAKANG[array_rand(self::NAMA_BELAKANG)],
                'no_telepon' => $anonim ? null : $this->telepon(),
                'alamat'     => null,
                'saran'      => self::SARAN[array_rand(self::SARAN)],
                'tgl_saran'  => date('Y-m-d H:i:s', $sekarang - mt_rand(0, 300) * 86400 - mt_rand(0, 86399)),
            ]);
        }
    }

    private function jejakStatus(int $umurHari): array
    {
        $alur  = [StatusLaporan::Diverifikasi, StatusLaporan::Valid, StatusLaporan::Diteruskan, StatusLaporan::Selesai];
        $acak  = mt_rand(1, 100);
        $tahap = match (true) {
            $umurHari < 3  => $acak <= 70 ? 0 : 1,
            $umurHari < 14 => $acak <= 25 ? 0 : ($acak <= 60 ? 1 : ($acak <= 85 ? 2 : 3)),
            $umurHari < 60 => $acak <= 5 ? 0 : ($acak <= 15 ? 1 : ($acak <= 35 ? 2 : ($acak <= 75 ? 3 : 4))),
            default        => $acak <= 2 ? 1 : ($acak <= 6 ? 2 : ($acak <= 40 ? 3 : 4)),
        };

        if ($tahap > 0 && $tahap < 3 && mt_rand(1, 100) <= 30) {
            return $tahap === 1 ? [StatusLaporan::TidakValid] : [StatusLaporan::Diverifikasi, StatusLaporan::TidakValid];
        }

        return array_slice($alur, 0, $tahap);
    }

    private function isiLaporan(string $kategori): array
    {
        $instansi = self::INSTANSI[array_rand(self::INSTANSI)];
        $pihak    = self::PIHAK[array_rand(self::PIHAK)];
        $kerugian = mt_rand(1, 100) <= 70 ? mt_rand(1, 50) * 50000 : mt_rand(2, 400) * 5000000;
        $templat  = self::TEMPLAT[$kategori][array_rand(self::TEMPLAT[$kategori])];

        $teks = strtr($templat, [
            '{instansi}' => $instansi,
            '{pihak}'    => mb_strtolower($pihak),
            '{nominal}'  => number_format($kerugian, 0, ',', '.'),
            '{urusan}'   => self::URUSAN[array_rand(self::URUSAN)],
            '{hadiah}'   => self::HADIAH[array_rand(self::HADIAH)],
            '{barang}'   => self::BARANG[array_rand(self::BARANG)],
            '{dana}'     => self::DANA[array_rand(self::DANA)],
        ]);

        return [
            'teks'     => ucfirst($teks),
            'instansi' => $instansi,
            'pihak'    => $pihak,
            'kerugian' => $kerugian,
        ];
    }

    private function ringkasan(string $kategori, array $isi): string
    {
        return sprintf('Dugaan %s di lingkungan %s yang melibatkan %s.', mb_strtolower($kategori === 'Lainnya' ? 'penyimpangan' : $kategori), $isi['instansi'], mb_strtolower($isi['pihak']));
    }

    private function telepon(): string
    {
        return '08' . mt_rand(11, 59) . str_pad((string) mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);
    }
}
