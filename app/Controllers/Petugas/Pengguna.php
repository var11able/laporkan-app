<?php

namespace App\Controllers\Petugas;

use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;

class Pengguna extends PetugasController
{
    public function index(): string
    {
        return $this->tampil('petugas/pengguna/index', [
            'judul'    => 'Akun Admin',
            'menu'     => 'pengguna',
            'pengguna' => (new UserModel())->orderBy('jabatan')->orderBy('nama')->findAll(),
        ]);
    }

    public function new(): string
    {
        return $this->tampil('petugas/pengguna/form', ['judul' => 'Tambah Admin', 'menu' => 'pengguna', 'akun' => null]);
    }

    public function create(): RedirectResponse
    {
        $aturan = $this->aturan() + [
            'username' => ['label' => 'Username', 'rules' => 'required|min_length[3]|max_length[100]'],
            'password' => ['label' => 'Kata sandi', 'rules' => 'required|min_length[8]|max_length[72]'],
        ];

        if (! $this->validasi($aturan)) {
            return $this->kembaliDenganError();
        }

        $data             = $this->validator->getValidated();
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $model            = new UserModel();

        if ($model->insert($data) === false) {
            return redirect()->back()->withInput()->with('_ci_validation_errors', $model->errors());
        }

        $this->catat('Menambahkan admin ' . $data['username'] . ' sebagai ' . $data['jabatan']);

        return redirect()->to(url_to('petugas.pengguna'))->with('sukses', 'Akun admin ' . $data['username'] . ' sudah dibuat.');
    }

    public function edit(int $id): string
    {
        return $this->tampil('petugas/pengguna/form', [
            'judul' => 'Ubah Admin',
            'menu'  => 'pengguna',
            'akun'  => (new UserModel())->find($id) ?? throw $this->tidakDitemukan(),
        ]);
    }

    public function update(int $id): RedirectResponse
    {
        $model = new UserModel();
        $akun  = $model->find($id) ?? throw $this->tidakDitemukan();

        $aturan = $this->aturan() + ['password' => ['label' => 'Kata sandi baru', 'rules' => 'permit_empty|min_length[8]|max_length[72]']];

        if (! $this->validasi($aturan)) {
            return $this->kembaliDenganError();
        }

        $data = $this->validator->getValidated();

        if ($akun->isAdmin() && $data['jabatan'] !== 'administrator' && $model->jumlahAdmin() <= 1) {
            return $this->kembaliDenganError('Harus ada minimal satu administrator. Tambahkan administrator lain sebelum mengubah jabatan akun ini.');
        }

        if (($data['password'] ?? '') !== '') {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['password']);
        }

        $model->update($id, $data);
        $this->catat('Mengubah akun admin ' . $akun->username);

        return redirect()->to(url_to('petugas.pengguna'))->with('sukses', 'Akun admin ' . $akun->username . ' sudah diperbarui.');
    }

    public function delete(int $id): RedirectResponse
    {
        $model = new UserModel();
        $akun  = $model->find($id) ?? throw $this->tidakDitemukan();

        if ($akun->getId() === $this->petugas()->getId()) {
            return redirect()->to(url_to('petugas.pengguna'))->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if (db_connect()->table('tanggapan')->where('id_user', $id)->countAllResults() > 0) {
            return redirect()->to(url_to('petugas.pengguna'))
                ->with('error', 'Akun ' . $akun->username . ' sudah menulis tanggapan, jadi tidak dapat dihapus agar riwayat laporan tetap utuh.');
        }

        $model->delete($id);
        $this->catat('Menghapus akun admin ' . $akun->username);

        return redirect()->to(url_to('petugas.pengguna'))->with('sukses', 'Akun admin ' . $akun->username . ' sudah dihapus.');
    }

    private function aturan(): array
    {
        return [
            'nama'       => ['label' => 'Nama lengkap', 'rules' => 'required|max_length[100]'],
            'no_telepon' => ['label' => 'Nomor telepon', 'rules' => 'required|max_length[20]'],
            'jabatan'    => ['label' => 'Jabatan', 'rules' => 'required|in_list[administrator,operator]'],
        ];
    }
}
