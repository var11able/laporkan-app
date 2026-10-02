<?php

namespace App\Controllers\Petugas;

use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;

class Profil extends PetugasController
{
    public function index(): string
    {
        return $this->tampil('petugas/profil', ['judul' => 'Profil', 'menu' => '']);
    }

    public function update(): RedirectResponse
    {
        $aturan = [
            'nama'       => ['label' => 'Nama lengkap', 'rules' => 'required|max_length[100]'],
            'no_telepon' => ['label' => 'Nomor telepon', 'rules' => 'required|max_length[20]'],
        ];

        if (! $this->validasi($aturan)) {
            return $this->kembaliDenganError();
        }

        (new UserModel())->update($this->petugas()->getId(), $this->validator->getValidated());
        $this->catat('Mengubah profil sendiri');
        $this->auth->segarkan();

        return redirect()->to(url_to('petugas.profil'))->with('sukses', 'Profil sudah diperbarui.');
    }

    public function password(): string
    {
        return $this->tampil('petugas/password', ['judul' => 'Ganti Kata Sandi', 'menu' => '']);
    }

    public function gantiPassword(): RedirectResponse
    {
        $aturan = [
            'password_lama'       => ['label' => 'Kata sandi saat ini', 'rules' => 'required'],
            'password_baru'       => ['label' => 'Kata sandi baru', 'rules' => 'required|min_length[8]|max_length[72]'],
            'password_konfirmasi' => ['label' => 'Ulangi kata sandi baru', 'rules' => 'required|matches[password_baru]', 'errors' => [
                'matches' => 'Kata sandi baru yang Anda ulangi tidak sama.',
            ]],
        ];

        if (! $this->validasi($aturan)) {
            return $this->kembaliDenganError();
        }

        $data = $this->validator->getValidated();

        if (! $this->auth->cekPassword($this->petugas(), $data['password_lama'])) {
            $this->catat('Gagal mengganti kata sandi: kata sandi lama salah');

            return redirect()->back()->with('_ci_validation_errors', ['password_lama' => 'Kata sandi saat ini salah.']);
        }

        (new UserModel())->update($this->petugas()->getId(), ['password' => password_hash($data['password_baru'], PASSWORD_DEFAULT)]);
        $this->catat('Mengganti kata sandi');

        return redirect()->to(url_to('petugas.profil'))->with('sukses', 'Kata sandi sudah diganti.');
    }
}
