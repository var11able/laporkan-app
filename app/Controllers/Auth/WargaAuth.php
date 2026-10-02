<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\MasyarakatModel;
use CodeIgniter\HTTP\RedirectResponse;

class WargaAuth extends BaseController
{
    use MembatasiPercobaan;

    public function masuk(): string
    {
        return $this->tampil('auth/masuk', ['judul' => 'Masuk']);
    }

    public function prosesMasuk(): RedirectResponse
    {
        if (! $this->validasi(['username' => 'required', 'password' => 'required'])) {
            return $this->kembaliDenganError();
        }

        $username = (string) $this->request->getPost('username');

        if (! $this->bolehMencoba('warga', $username)) {
            return $this->kembaliDenganError(self::PESAN_TERLALU_BANYAK);
        }

        if (! $this->auth->loginWarga($username, (string) $this->request->getPost('password'))) {
            return $this->kembaliDenganError(self::PESAN_GAGAL);
        }

        return redirect()->to($this->tujuanSetelahMasuk(url_to('warga.beranda')))
            ->with('sukses', 'Selamat datang, ' . $this->auth->warga()->nama . '.');
    }

    public function daftar(): string
    {
        return $this->tampil('auth/daftar', ['judul' => 'Daftar Akun']);
    }

    public function prosesDaftar(): RedirectResponse
    {
        $aturan = [
            'nama'       => ['label' => 'Nama lengkap', 'rules' => 'required|max_length[100]'],
            'username'   => ['label' => 'Username', 'rules' => 'required|min_length[3]|max_length[100]'],
            'no_telepon' => ['label' => 'Nomor telepon', 'rules' => 'required|regex_match[/^[0-9+ ]{8,20}$/]', 'errors' => [
                'regex_match' => 'Masukkan nomor telepon yang benar, contoh 081234567890.',
            ]],
            'alamat'              => ['label' => 'Alamat', 'rules' => 'required|max_length[500]'],
            'password'            => ['label' => 'Kata sandi', 'rules' => 'required|min_length[8]|max_length[72]'],
            'password_konfirmasi' => ['label' => 'Ulangi kata sandi', 'rules' => 'required|matches[password]', 'errors' => [
                'matches' => 'Kata sandi yang Anda ulangi tidak sama.',
            ]],
        ];

        if (! $this->validasi($aturan)) {
            return $this->kembaliDenganError();
        }

        $data  = $this->validator->getValidated();
        $model = new MasyarakatModel();

        $simpan = $model->insert([
            'nama'       => $data['nama'],
            'username'   => trim($data['username']),
            'password'   => password_hash($data['password'], PASSWORD_DEFAULT),
            'no_telepon' => $data['no_telepon'],
            'alamat'     => $data['alamat'],
        ]);

        if ($simpan === false) {
            return redirect()->back()->withInput()->with('_ci_validation_errors', $model->errors());
        }

        $this->auth->loginWarga(trim($data['username']), $data['password']);

        return redirect()->to(url_to('warga.beranda'))
            ->with('sukses', 'Akun Anda sudah dibuat. Sekarang Anda bisa membuat laporan.');
    }
}
