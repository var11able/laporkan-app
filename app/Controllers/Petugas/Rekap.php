<?php

namespace App\Controllers\Petugas;

use App\Controllers\RekapFilter;
use App\Entities\Pengaduan;
use App\Models\KategoriModel;
use App\Models\PengaduanModel;
use App\Models\ProvinsiModel;

class Rekap extends PetugasController
{
    use RekapFilter;

    public function index(): string
    {
        $filter = $this->filterRekap();

        return $this->tampil('petugas/rekap', [
            'judul'        => 'Rekap Laporan',
            'menu'         => 'rekap',
            'filter'       => $filter,
            'laporan'      => $this->laporan($filter),
            'opsiProvinsi' => (new ProvinsiModel())->opsi(),
            'opsiKategori' => (new KategoriModel())->opsi(),
        ]);
    }

    public function cetak(): string
    {
        $filter = $this->filterRekap();
        $this->catat('Mencetak rekap ' . $filter['dari'] . ' s.d. ' . $filter['sampai']);

        return view('cetak/rekap', [
            'judul'        => 'Rekap Laporan ' . $filter['dari'] . ' s.d. ' . $filter['sampai'],
            'filter'       => $filter,
            'laporan'      => $this->laporan($filter),
            'petugas'      => $this->petugas(),
            'namaProvinsi' => $filter['provinsi'] !== '' ? ((new ProvinsiModel())->opsi()[(int) $filter['provinsi']] ?? null) : null,
            'namaKategori' => $filter['kategori'] !== '' ? ((new KategoriModel())->opsi()[(int) $filter['kategori']] ?? null) : null,
            'kembali'      => url_to('petugas.rekap') . '?' . http_build_query($filter),
        ]);
    }

    private function laporan(array $filter): array
    {
        return (new PengaduanModel())->denganRelasi()->filter($filter)->terbaru()->findAll();
    }
}
