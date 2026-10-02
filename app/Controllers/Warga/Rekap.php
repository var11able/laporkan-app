<?php

namespace App\Controllers\Warga;

use App\Controllers\BaseController;
use App\Controllers\RekapFilter;
use App\Entities\Pengaduan;
use App\Models\PengaduanModel;

class Rekap extends BaseController
{
    use RekapFilter;

    public function index(): string
    {
        $filter = $this->filterRekap();

        return $this->tampil('warga/rekap', [
            'judul'   => 'Rekap Laporan Saya',
            'menu'    => 'rekap',
            'filter'  => $filter,
            'laporan' => $this->laporan($filter),
        ]);
    }

    public function cetak(): string
    {
        $filter = $this->filterRekap();

        return view('cetak/rekap', [
            'judul'   => 'Rekap Laporan ' . $this->auth->warga()->nama,
            'filter'  => $filter,
            'laporan' => $this->laporan($filter),
            'kembali' => url_to('warga.rekap') . '?' . http_build_query($filter),
            'pelapor' => $this->auth->warga()->nama,
        ]);
    }

    private function laporan(array $filter): array
    {
        return (new PengaduanModel())->denganRelasi()
            ->milikWarga($this->auth->warga()->getId())
            ->filter($filter)
            ->terbaru()
            ->findAll();
    }
}
