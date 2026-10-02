<?php

namespace App\Controllers\Publik;

use App\Controllers\BaseController;
use App\Models\KategoriModel;
use App\Models\PengaduanModel;

class Beranda extends BaseController
{
    public function index(): string
    {
        $model  = new PengaduanModel();
        $status = $model->jumlahPerStatus();

        return $this->tampil('publik/beranda', [
            'penuh'     => true,
            'menu'      => 'beranda',
            'statistik' => [
                'bulanIni'   => (new PengaduanModel())->where('tgl_pengaduan >=', date('Y-m-01 00:00:00'))->countAllResults(),
                'diterima'   => array_sum($status),
                'diteruskan' => $status['pengerjaan'] + $status['selesai'],
                'selesai'    => $status['selesai'],
            ],
            'terbaru'  => $model->denganRelasi()->publik()->terbaru()->findAll(6),
            'kategori' => (new KategoriModel())->semua(),
        ]);
    }
}
