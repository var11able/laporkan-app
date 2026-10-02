<?php

namespace App\Controllers\Petugas;

use App\Models\SaranModel;

class Saran extends PetugasController
{
    public function index(): string
    {
        $model = new SaranModel();

        return $this->tampil('petugas/saran', [
            'judul' => 'Kritik & Saran',
            'menu'  => 'saran',
            'saran' => $model->orderBy('tgl_saran', 'DESC')->paginate(20),
            'pager' => $model->pager,
        ]);
    }
}
