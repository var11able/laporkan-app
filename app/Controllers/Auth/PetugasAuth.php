<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RedirectResponse;

class PetugasAuth extends BaseController
{
    use MembatasiPercobaan;

    public function masuk(): string
    {
        return $this->tampil('auth/masuk_petugas', ['judul' => 'Masuk Admin']);
    }

    public function prosesMasuk(): RedirectResponse
    {
        if (! $this->validasi(['username' => 'required', 'password' => 'required'])) {
            return $this->kembaliDenganError();
        }

        $username = (string) $this->request->getPost('username');

        if (! $this->bolehMencoba('petugas', $username)) {
            return $this->kembaliDenganError(self::PESAN_TERLALU_BANYAK);
        }

        if (! $this->auth->loginPetugas($username, (string) $this->request->getPost('password'))) {
            return $this->kembaliDenganError(self::PESAN_GAGAL);
        }

        service('auditLog')->catat('Masuk ke panel admin', $this->auth->petugas()->getId());

        return redirect()->to($this->tujuanSetelahMasuk(url_to('petugas.dasbor')));
    }
}
