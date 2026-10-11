<?php

namespace App\Controllers\Petugas;

use App\Models\LaporanModel;

class Dasbor extends PetugasController
{
    public function index(): string
    {
        $model     = new LaporanModel();
        $status    = $model->jumlahPerStatus();
        $awalBulan = date('Y-m-01 00:00:00');

        return $this->tampil('petugas/dasbor', [
            'judul' => 'Dasbor',
            'menu'  => 'dasbor',
            'angka' => [
                'baru'            => $status['belum_ditanggapi'],
                'diproses'        => $status['proses'] + $status['valid'] + $status['pengerjaan'],
                'selesaiBulanIni' => $model->jumlahSelesaiSejak($awalBulan),
                'rataHari'        => $model->rataRataHariSelesai(),
            ],
            'perMinggu'   => $model->jumlahPerMinggu(8),
            'perKategori' => $model->jumlahPerKategori(),
            'perProvinsi' => $model->jumlahPerProvinsi(),
            'menunggu'    => (new LaporanModel())->denganRelasi()
                ->where('laporan.status_laporan', 'belum_ditanggapi')
                ->orderBy('laporan.tgl_laporan', 'ASC')
                ->findAll(5),
        ]);
    }
}
