<?php

namespace App\Models;

use App\Entities\Pengaduan;
use App\Enums\StatusPengaduan;
use CodeIgniter\Model;

class PengaduanModel extends Model
{
    protected $table         = 'pengaduan';
    protected $primaryKey    = 'id_pengaduan';
    protected $returnType    = Pengaduan::class;
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'tgl_pengaduan';
    protected $updatedField  = 'updated_at';
    protected $allowedFields = [
        'nomor_laporan', 'isi_laporan', 'tgl_pengaduan', 'foto', 'status_pengaduan',
        'id_masyarakat', 'id_kabupaten_kota', 'id_kategori', 'detail_lokasi',
        'instansi_terlapor', 'pihak_terlapor', 'waktu_kejadian', 'perkiraan_kerugian', 'rahasia', 'ringkasan_publik',
    ];
    protected $validationRules = [
        'isi_laporan'        => 'required|min_length[10]|max_length[5000]',
        'id_masyarakat'      => 'required|is_natural_no_zero',
        'id_kabupaten_kota'  => 'permit_empty|is_natural_no_zero',
        'id_kategori'        => 'permit_empty|is_natural_no_zero',
        'instansi_terlapor'  => 'permit_empty|max_length[255]',
        'pihak_terlapor'     => 'permit_empty|max_length[255]',
        'waktu_kejadian'     => 'permit_empty|valid_date[Y-m-d]',
        'perkiraan_kerugian' => 'permit_empty|is_natural',
        'status_pengaduan'   => 'permit_empty|in_list[belum_ditanggapi,proses,valid,pengerjaan,selesai,tidak_valid]',
    ];

    public function denganRelasi(): static
    {
        $this->select('pengaduan.*, masyarakat.nama AS nama_pelapor, masyarakat.no_telepon AS telepon_pelapor,
                       kabupaten_kota.kabupaten_kota, provinsi.id_provinsi, provinsi.provinsi, kategori.kategori,
                       (SELECT MAX(t.tgl_tanggapan) FROM tanggapan t WHERE t.id_pengaduan = pengaduan.id_pengaduan) AS tanggapan_terakhir', false)
            ->join('masyarakat', 'masyarakat.id_masyarakat = pengaduan.id_masyarakat')
            ->join('kabupaten_kota', 'kabupaten_kota.id_kabupaten_kota = pengaduan.id_kabupaten_kota', 'left')
            ->join('provinsi', 'provinsi.id_provinsi = kabupaten_kota.id_provinsi', 'left')
            ->join('kategori', 'kategori.id_kategori = pengaduan.id_kategori', 'left');

        return $this;
    }

    public function milikWarga(int $idMasyarakat): static
    {
        $this->where('pengaduan.id_masyarakat', $idMasyarakat);

        return $this;
    }

    public function publik(): static
    {
        $this->whereIn('pengaduan.status_pengaduan', array_map(static fn (StatusPengaduan $s) => $s->value, StatusPengaduan::bisaPublik()))
            ->where('pengaduan.ringkasan_publik IS NOT NULL')
            ->where("pengaduan.ringkasan_publik != ''");

        return $this;
    }

    public function filter(array $filter): static
    {
        if (! empty($filter['statusIn'])) {
            $this->whereIn('pengaduan.status_pengaduan', $filter['statusIn']);
        }

        $status = $filter['status'] ?? null;
        if ($status !== null && $status !== '' && StatusPengaduan::tryFrom($status) !== null) {
            $this->where('pengaduan.status_pengaduan', $status);
        }

        if (! empty($filter['provinsi'])) {
            $this->where('kabupaten_kota.id_provinsi', (int) $filter['provinsi']);
        }

        if (! empty($filter['kabupaten_kota'])) {
            $this->where('pengaduan.id_kabupaten_kota', (int) $filter['kabupaten_kota']);
        }

        if (! empty($filter['kategori'])) {
            $this->where('pengaduan.id_kategori', (int) $filter['kategori']);
        }

        if (! empty($filter['dari']) && ($dari = strtotime($filter['dari'])) !== false) {
            $this->where('pengaduan.tgl_pengaduan >=', date('Y-m-d 00:00:00', $dari));
        }

        if (! empty($filter['sampai']) && ($sampai = strtotime($filter['sampai'])) !== false) {
            $this->where('pengaduan.tgl_pengaduan <=', date('Y-m-d 23:59:59', $sampai));
        }

        if (! empty($filter['q'])) {
            $this->groupStart()
                ->like('pengaduan.isi_laporan', $filter['q'])
                ->orLike('pengaduan.instansi_terlapor', $filter['q'])
                ->orLike('pengaduan.nomor_laporan', $filter['q'])
                ->groupEnd();
        }

        if (! empty($filter['q_publik'])) {
            $this->groupStart()
                ->like('pengaduan.ringkasan_publik', $filter['q_publik'])
                ->orLike('pengaduan.nomor_laporan', $filter['q_publik'])
                ->orLike('kategori.kategori', $filter['q_publik'])
                ->groupEnd();
        }

        return $this;
    }

    public function terbaru(): static
    {
        $this->orderBy('pengaduan.tgl_pengaduan', 'DESC')->orderBy('pengaduan.id_pengaduan', 'DESC');

        return $this;
    }

    public function cariDenganRelasi(int $id): ?Pengaduan
    {
        return $this->denganRelasi()->where('pengaduan.id_pengaduan', $id)->first();
    }

    public function cariNomor(string $nomor): ?Pengaduan
    {
        return $this->denganRelasi()->where('pengaduan.nomor_laporan', strtoupper(trim($nomor)))->first();
    }

    public function rataRataHariSelesai(?string $sejak = null): ?float
    {
        $builder = $this->db->table('pengaduan p')
            ->select('AVG(TIMESTAMPDIFF(HOUR, p.tgl_pengaduan, t.tgl_tanggapan)) / 24 AS rata', false)
            ->join('tanggapan t', "t.id_pengaduan = p.id_pengaduan AND t.status_tanggapan = 'selesai'");

        if ($sejak !== null) {
            $builder->where('t.tgl_tanggapan >=', $sejak);
        }

        $rata = $builder->get()->getRow()->rata ?? null;

        return $rata === null ? null : round((float) $rata, 1);
    }

    public function jumlahSelesaiSejak(string $sejak): int
    {
        return $this->db->table('tanggapan')
            ->where('status_tanggapan', 'selesai')
            ->where('tgl_tanggapan >=', $sejak)
            ->countAllResults();
    }

    public function jumlahPerMinggu(int $minggu = 8): array
    {
        $awal = date('Y-m-d', strtotime('monday this week -' . ($minggu - 1) . ' weeks'));
        $rows = $this->db->table('pengaduan')
            ->select('DATE_SUB(DATE(tgl_pengaduan), INTERVAL WEEKDAY(tgl_pengaduan) DAY) AS mulai, COUNT(*) AS jumlah', false)
            ->where('tgl_pengaduan >=', $awal . ' 00:00:00')
            ->groupBy('mulai')
            ->get()->getResultArray();
        $perMinggu = array_column($rows, 'jumlah', 'mulai');

        $hasil = [];

        for ($i = 0; $i < $minggu; $i++) {
            $mulai   = date('Y-m-d', strtotime($awal . ' +' . $i . ' weeks'));
            $hasil[] = ['mulai' => $mulai, 'jumlah' => (int) ($perMinggu[$mulai] ?? 0)];
        }

        return $hasil;
    }

    public function jumlahPerKategori(int $batas = 8): array
    {
        $rows = $this->db->table('pengaduan p')
            ->select("COALESCE(k.kategori, 'Tanpa jenis') AS label, COUNT(*) AS jumlah", false)
            ->join('kategori k', 'k.id_kategori = p.id_kategori', 'left')
            ->groupBy('label')
            ->orderBy('jumlah', 'DESC')
            ->limit($batas)
            ->get()->getResultArray();

        return array_map(static fn (array $r) => ['label' => $r['label'], 'jumlah' => (int) $r['jumlah']], $rows);
    }

    public function jumlahPerProvinsi(int $batas = 8): array
    {
        $rows = $this->db->table('pengaduan p')
            ->select("COALESCE(pr.provinsi, 'Tanpa wilayah') AS label, COUNT(*) AS jumlah", false)
            ->join('kabupaten_kota kk', 'kk.id_kabupaten_kota = p.id_kabupaten_kota', 'left')
            ->join('provinsi pr', 'pr.id_provinsi = kk.id_provinsi', 'left')
            ->groupBy('label')
            ->orderBy('jumlah', 'DESC')
            ->limit($batas)
            ->get()->getResultArray();

        return array_map(static fn (array $r) => ['label' => $r['label'], 'jumlah' => (int) $r['jumlah']], $rows);
    }

    public function jumlahPerStatus(?int $idMasyarakat = null): array
    {
        $builder = $this->builder()->select('status_pengaduan, COUNT(*) AS jumlah')->groupBy('status_pengaduan');

        if ($idMasyarakat !== null) {
            $builder->where('id_masyarakat', $idMasyarakat);
        }

        $hasil = array_fill_keys(array_map(static fn (StatusPengaduan $s) => $s->value, StatusPengaduan::cases()), 0);

        foreach ($builder->get()->getResultArray() as $row) {
            $hasil[$row['status_pengaduan']] = (int) $row['jumlah'];
        }

        return $hasil;
    }
}
