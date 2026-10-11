<?php

namespace App\Controllers\Publik;

use App\Controllers\BaseController;
use App\Enums\StatusLaporan;
use App\Models\KategoriModel;
use App\Models\LaporanModel;
use App\Models\ProvinsiModel;
use App\Models\TanggapanModel;
use App\Services\SebaranPeta;
use CodeIgniter\HTTP\RedirectResponse;

class Laporan extends BaseController
{
    private const PER_HALAMAN = 12;

    public function index(): string
    {
        $filter = [
            'status'   => (string) $this->request->getGet('status'),
            'provinsi' => (string) $this->request->getGet('provinsi'),
            'kategori' => (string) $this->request->getGet('kategori'),
            'q_publik' => trim((string) $this->request->getGet('q')),
        ];

        $model   = new LaporanModel();
        $laporan = $model->denganRelasi()->publik()->filter($filter)->terbaru()->paginate(self::PER_HALAMAN);

        $opsiStatus = [];

        foreach (StatusLaporan::bisaPublik() as $s) {
            $opsiStatus[$s->value] = $s->labelWarga();
        }

        return $this->tampil('publik/laporan_index', [
            'judul'        => 'Daftar Laporan',
            'deskripsi'    => 'Ringkasan laporan dugaan korupsi yang sudah diverifikasi admin LaporKan.',
            'menu'         => 'laporan',
            'laporan'      => $laporan,
            'pager'        => $model->pager,
            'filter'       => $filter,
            'opsiStatus'   => $opsiStatus,
            'opsiProvinsi' => (new ProvinsiModel())->opsi(),
            'opsiKategori' => (new KategoriModel())->opsi(),
            'peta'         => (new SebaranPeta())->publik(),
        ]);
    }

    public function show(string $nomor): RedirectResponse|string
    {
        $laporan = (new LaporanModel())->cariNomor($nomor);

        if ($laporan === null) {
            throw $this->tidakDitemukan('Laporan dengan nomor tersebut tidak ditemukan.');
        }

        if (! $laporan->tampilPublik()) {
            $warga = $this->auth->warga();

            if ($warga !== null && $laporan->milik($warga->getId())) {
                return redirect()->to(url_to('warga.laporan.detail', $laporan->id_laporan));
            }

            return redirect()->to(url_to('lacak') . '?nomor=' . rawurlencode((string) $laporan->nomor_laporan));
        }

        return $this->tampil('publik/laporan_detail', [
            'judul'     => 'Laporan ' . $laporan->nomor_laporan,
            'deskripsi' => penggalan((string) $laporan->ringkasan_publik, 150),
            'menu'      => 'laporan',
            'laporan'   => $laporan,
            'tanggapan' => (new TanggapanModel())->untukLaporan($laporan->id_laporan),
        ]);
    }
}
