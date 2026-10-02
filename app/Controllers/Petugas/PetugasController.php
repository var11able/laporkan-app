<?php

namespace App\Controllers\Petugas;

use App\Controllers\BaseController;
use App\Entities\User;
use App\Models\PengaduanModel;

abstract class PetugasController extends BaseController
{
    protected function petugas(): User
    {
        return $this->auth->petugas();
    }

    protected function catat(string $pesan): void
    {
        service('auditLog')->catat($pesan, $this->petugas()->getId());
    }

    protected function tampil(string $view, array $data = []): string
    {
        $data['jumlahBaru'] ??= (new PengaduanModel())->where('status_pengaduan', 'belum_ditanggapi')->countAllResults();

        return parent::tampil($view, $data);
    }
}
