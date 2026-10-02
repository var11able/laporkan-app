<?php

namespace App\Controllers\Publik;

use App\Controllers\BaseController;
use App\Enums\StatusPengaduan;
use App\Models\KategoriModel;
use App\Models\PengaduanModel;
use App\Models\ProvinsiModel;
use App\Models\TanggapanModel;
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

        $model   = new PengaduanModel();
        $laporan = $model->denganRelasi()->publik()->filter($filter)->terbaru()->paginate(self::PER_HALAMAN);

        $opsiStatus = [];

        foreach (StatusPengaduan::bisaPublik() as $s) {
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
        ]);
    }

    public function show(string $nomor): RedirectResponse|string
    {
        $pengaduan = (new PengaduanModel())->cariNomor($nomor);

        if ($pengaduan === null) {
            throw $this->tidakDitemukan('Laporan dengan nomor tersebut tidak ditemukan.');
        }

        if (! $pengaduan->tampilPublik()) {
            $warga = $this->auth->warga();

            if ($warga !== null && $pengaduan->milik($warga->getId())) {
                return redirect()->to(url_to('warga.laporan.detail', $pengaduan->id_pengaduan));
            }

            return redirect()->to(url_to('lacak') . '?nomor=' . rawurlencode((string) $pengaduan->nomor_laporan));
        }

        return $this->tampil('publik/laporan_detail', [
            'judul'     => 'Laporan ' . $pengaduan->nomor_laporan,
            'deskripsi' => penggalan((string) $pengaduan->ringkasan_publik, 150),
            'menu'      => 'laporan',
            'pengaduan' => $pengaduan,
            'tanggapan' => (new TanggapanModel())->untukPengaduan($pengaduan->id_pengaduan),
        ]);
    }

    public function legacy(int $id): RedirectResponse
    {
        $pengaduan = (new PengaduanModel())->find($id);

        if ($pengaduan === null) {
            throw $this->tidakDitemukan();
        }

        return redirect()->to(url_to('laporan.detail', $pengaduan->nomor_laporan), 301);
    }
}
