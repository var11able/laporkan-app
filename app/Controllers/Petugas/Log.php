<?php

namespace App\Controllers\Petugas;

use App\Models\LogModel;

class Log extends PetugasController
{
    public function index(): string
    {
        $model = new LogModel();

        return $this->tampil('petugas/log', [
            'judul' => 'Log Aktivitas',
            'menu'  => 'log',
            'log'   => $model->denganPetugas()->paginate(50),
            'pager' => $model->pager,
        ]);
    }
}
