<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RedirectResponse;

class Keluar extends BaseController
{
    public function index(): RedirectResponse
    {
        $wasPetugas = $this->auth->petugas() !== null;

        $this->auth->logout();

        return redirect()->to($wasPetugas ? url_to('petugas.masuk') : url_to('beranda'))
            ->with('sukses', 'Anda sudah keluar.');
    }
}
