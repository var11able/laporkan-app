<?php

namespace App\Models;

use App\Entities\LaporanKorupsi;
use App\Enums\StatusLaporan;
use CodeIgniter\Model;

class LaporanModel extends Model
{
    protected $table         = 'laporan';
    protected $primaryKey    = 'id_laporan';
    protected $returnType    = LaporanKorupsi::class;
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'tgl_laporan';
    protected $updatedField  = 'updated_at';
    protected $allowedFields = [
        'nomor_laporan', 'isi_laporan', 'tgl_laporan', 'foto', 'status_laporan',
        'id_warga', 'id_kabupaten_kota', 'id_kategori', 'detail_lokasi',
        'instansi_terlapor', 'pihak_terlapor', 'waktu_kejadian', 'perkiraan_kerugian', 'rahasia', 'ringkasan_publik',
    ];
    protected $validationRules = [
        'isi_laporan'        => 'required|min_length[10]|max_length[5000]',
        'id_warga'           => 'required|is_natural_no_zero',
        'id_kabupaten_kota'  => 'permit_empty|is_natural_no_zero',
        'id_kategori'        => 'permit_empty|is_natural_no_zero',
        'instansi_terlapor'  => 'permit_empty|max_length[255]',
        'pihak_terlapor'     => 'permit_empty|max_length[255]',
        'waktu_kejadian'     => 'permit_empty|valid_date[Y-m-d]',
        'perkiraan_kerugian' => 'permit_empty|is_natural',
        'status_laporan'     => 'permit_empty|in_list[belum_ditanggapi,proses,valid,pengerjaan,selesai,tidak_valid]',
    ];

    public function denganRelasi(): static
    {
        $this->select('laporan.*, warga.nama AS nama_pelapor, warga.no_telepon AS telepon_pelapor,
                       kabupaten_kota.kabupaten_kota, provinsi.id_provinsi, provinsi.provinsi, kategori.kategori,
                       (SELECT MAX(t.tgl_tanggapan) FROM tanggapan t WHERE t.id_laporan = laporan.id_laporan) AS tanggapan_terakhir', false)
            ->join('warga', 'warga.id_warga = laporan.id_warga')
            ->join('kabupaten_kota', 'kabupaten_kota.id_kabupaten_kota = laporan.id_kabupaten_kota', 'left')
            ->join('provinsi', 'provinsi.id_provinsi = kabupaten_kota.id_provinsi', 'left')
            ->join('kategori', 'kategori.id_kategori = laporan.id_kategori', 'left');

        return $this;
    }

    public function milikWarga(int $idWarga): static
    {
        $this->where('laporan.id_warga', $idWarga);

        return $this;
    }

    public function publik(): static
    {
        $this->whereIn('laporan.status_laporan', array_map(static fn (StatusLaporan $s) => $s->value, StatusLaporan::bisaPublik()))
            ->where('laporan.ringkasan_publik IS NOT NULL')
            ->where("laporan.ringkasan_publik != ''");

        return $this;
    }

    public function filter(array $filter): static
    {
        if (! empty($filter['statusIn'])) {
            $this->whereIn('laporan.status_laporan', $filter['statusIn']);
        }

        $status = $filter['status'] ?? null;
        if ($status !== null && $status !== '' && StatusLaporan::tryFrom($status) !== null) {
            $this->where('laporan.status_laporan', $status);
        }

        if (! empty($filter['provinsi'])) {
            $this->where('kabupaten_kota.id_provinsi', (int) $filter['provinsi']);
        }

        if (! empty($filter['kabupaten_kota'])) {
            $this->where('laporan.id_kabupaten_kota', (int) $filter['kabupaten_kota']);
        }

        if (! empty($filter['kategori'])) {
            $this->where('laporan.id_kategori', (int) $filter['kategori']);
        }

        if (! empty($filter['dari']) && ($dari = strtotime($filter['dari'])) !== false) {
            $this->where('laporan.tgl_laporan >=', date('Y-m-d 00:00:00', $dari));
        }

        if (! empty($filter['sampai']) && ($sampai = strtotime($filter['sampai'])) !== false) {
            $this->where('laporan.tgl_laporan <=', date('Y-m-d 23:59:59', $sampai));
        }

        if (! empty($filter['q'])) {
            $this->groupStart()
                ->like('laporan.isi_laporan', $filter['q'])
                ->orLike('laporan.instansi_terlapor', $filter['q'])
                ->orLike('laporan.nomor_laporan', $filter['q'])
                ->groupEnd();
        }

        if (! empty($filter['q_publik'])) {
            $this->groupStart()
                ->like('laporan.ringkasan_publik', $filter['q_publik'])
                ->orLike('laporan.nomor_laporan', $filter['q_publik'])
                ->orLike('kategori.kategori', $filter['q_publik'])
                ->groupEnd();
        }

        return $this;
    }

    public function terbaru(): static
    {
        $this->orderBy('laporan.tgl_laporan', 'DESC')->orderBy('laporan.id_laporan', 'DESC');

        return $this;
    }

    public function cariDenganRelasi(int $id): ?LaporanKorupsi
    {
        return $this->denganRelasi()->where('laporan.id_laporan', $id)->first();
    }

    public function cariNomor(string $nomor): ?LaporanKorupsi
    {
        return $this->denganRelasi()->where('laporan.nomor_laporan', strtoupper(trim($nomor)))->first();
    }

    public function rataRataHariSelesai(?string $sejak = null): ?float
    {
        $builder = $this->db->table('laporan p')
            ->select('AVG(TIMESTAMPDIFF(HOUR, p.tgl_laporan, t.tgl_tanggapan)) / 24 AS rata', false)
            ->join('tanggapan t', "t.id_laporan = p.id_laporan AND t.status_tanggapan = 'selesai'");

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
        $rows = $this->db->table('laporan')
            ->select('DATE_SUB(DATE(tgl_laporan), INTERVAL WEEKDAY(tgl_laporan) DAY) AS mulai, COUNT(*) AS jumlah', false)
            ->where('tgl_laporan >=', $awal . ' 00:00:00')
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
        $rows = $this->db->table('laporan p')
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
        $rows = $this->db->table('laporan p')
            ->select("COALESCE(pr.provinsi, 'Tanpa wilayah') AS label, COUNT(*) AS jumlah", false)
            ->join('kabupaten_kota kk', 'kk.id_kabupaten_kota = p.id_kabupaten_kota', 'left')
            ->join('provinsi pr', 'pr.id_provinsi = kk.id_provinsi', 'left')
            ->groupBy('label')
            ->orderBy('jumlah', 'DESC')
            ->limit($batas)
            ->get()->getResultArray();

        return array_map(static fn (array $r) => ['label' => $r['label'], 'jumlah' => (int) $r['jumlah']], $rows);
    }

    public function jumlahPerStatus(?int $idWarga = null): array
    {
        $builder = $this->builder()->select('status_laporan, COUNT(*) AS jumlah')->groupBy('status_laporan');

        if ($idWarga !== null) {
            $builder->where('id_warga', $idWarga);
        }

        $hasil = array_fill_keys(array_map(static fn (StatusLaporan $s) => $s->value, StatusLaporan::cases()), 0);

        foreach ($builder->get()->getResultArray() as $row) {
            $hasil[$row['status_laporan']] = (int) $row['jumlah'];
        }

        return $hasil;
    }
}
