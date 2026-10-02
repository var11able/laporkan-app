<?php

namespace App\Controllers\Publik;

use App\Controllers\BaseController;

class Halaman extends BaseController
{
    public function privasi(): string
    {
        return $this->tampil('publik/privasi', ['judul' => 'Kebijakan Privasi', 'lebar' => 'sempit']);
    }

    public function syarat(): string
    {
        return $this->tampil('publik/syarat', ['judul' => 'Syarat & Ketentuan', 'lebar' => 'sempit']);
    }

    public function tentangKorupsi(): string
    {
        return $this->tampil('publik/tentang_korupsi', [
            'judul'     => 'Tentang Korupsi',
            'deskripsi' => 'Mengenal tujuh kelompok tindak pidana korupsi, peran masyarakat, dan cara melaporkannya.',
            'menu'      => 'tentang',
            'lebar'     => 'sempit',
        ]);
    }
}
