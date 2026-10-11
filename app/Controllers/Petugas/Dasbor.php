<?php

namespace App\Controllers\Petugas;

use App\Enums\StatusLaporan;
use App\Models\LaporanModel;
use App\Services\SebaranPeta;

class Dasbor extends PetugasController
{
    public function index(): string
    {
        $model      = new LaporanModel();
        $status     = $model->jumlahPerStatus();
        $awalBulan  = date('Y-m-01 00:00:00');
        $statusPeta = StatusLaporan::tryFrom((string) $this->request->getGet('peta'));

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
            'peta'        => (new SebaranPeta())->petugas($statusPeta),
            'statusPeta'  => $statusPeta,
            'menunggu'    => (new LaporanModel())->denganRelasi()
                ->where('laporan.status_laporan', 'belum_ditanggapi')
                ->orderBy('laporan.tgl_laporan', 'ASC')
                ->findAll(5),
        ]);
    }
}
