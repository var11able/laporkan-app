<?php

namespace App\Controllers\Warga;

use App\Controllers\BaseController;
use App\Models\MasyarakatModel;
use CodeIgniter\HTTP\RedirectResponse;

class Profil extends BaseController
{
    public function index(): string
    {
        return $this->tampil('warga/profil', ['judul' => 'Profil', 'menu' => 'profil', 'lebar' => 'sempit']);
    }

    public function update(): RedirectResponse
    {
        $aturan = [
            'nama'       => ['label' => 'Nama lengkap', 'rules' => 'required|max_length[100]'],
            'no_telepon' => ['label' => 'Nomor telepon', 'rules' => 'required|regex_match[/^[0-9+ ]{8,20}$/]', 'errors' => [
                'regex_match' => 'Masukkan nomor telepon yang benar, contoh 081234567890.',
            ]],
            'alamat' => ['label' => 'Alamat', 'rules' => 'required|max_length[500]'],
        ];

        if (! $this->validasi($aturan)) {
            return $this->kembaliDenganError();
        }

        (new MasyarakatModel())->update($this->auth->warga()->getId(), $this->validator->getValidated());
        $this->auth->segarkan();

        return redirect()->to(url_to('warga.profil'))->with('sukses', 'Profil sudah diperbarui.');
    }

    public function password(): string
    {
        return $this->tampil('warga/password', ['judul' => 'Ganti Kata Sandi', 'menu' => 'profil', 'lebar' => 'sempit']);
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

        $warga = $this->auth->warga();
        $data  = $this->validator->getValidated();

        if (! $this->auth->cekPassword($warga, $data['password_lama'])) {
            return redirect()->back()->with('_ci_validation_errors', ['password_lama' => 'Kata sandi saat ini salah.']);
        }

        (new MasyarakatModel())->update($warga->getId(), ['password' => password_hash($data['password_baru'], PASSWORD_DEFAULT)]);

        return redirect()->to(url_to('warga.profil'))->with('sukses', 'Kata sandi sudah diganti.');
    }
}
