<?php

namespace App\Controllers\Petugas;

use App\Models\ProvinsiModel;
use CodeIgniter\HTTP\RedirectResponse;

class Provinsi extends PetugasController
{
    public function index(): string
    {
        return $this->tampil('petugas/wilayah/provinsi', [
            'judul'    => 'Provinsi',
            'menu'     => 'provinsi',
            'provinsi' => (new ProvinsiModel())->semuaDenganJumlah(),
        ]);
    }

    public function new(): string
    {
        return $this->tampil('petugas/wilayah/provinsi_form', ['judul' => 'Tambah Provinsi', 'menu' => 'provinsi', 'provinsi' => null]);
    }

    public function create(): RedirectResponse
    {
        $model = new ProvinsiModel();

        if ($model->insert(['provinsi' => trim((string) $this->request->getPost('provinsi'))]) === false) {
            return redirect()->back()->withInput()->with('_ci_validation_errors', $model->errors());
        }

        $this->catat('Menambahkan provinsi ' . $this->request->getPost('provinsi'));

        return redirect()->to(url_to('petugas.provinsi'))->with('sukses', 'Provinsi sudah ditambahkan.');
    }

    public function edit(int $id): string
    {
        return $this->tampil('petugas/wilayah/provinsi_form', [
            'judul'    => 'Ubah Provinsi',
            'menu'     => 'provinsi',
            'provinsi' => (new ProvinsiModel())->find($id) ?? throw $this->tidakDitemukan(),
        ]);
    }

    public function update(int $id): RedirectResponse
    {
        $model = new ProvinsiModel();
        $lama  = $model->find($id) ?? throw $this->tidakDitemukan();

        if (! $model->update($id, ['id_provinsi' => $id, 'provinsi' => trim((string) $this->request->getPost('provinsi'))])) {
            return redirect()->back()->withInput()->with('_ci_validation_errors', $model->errors());
        }

        $this->catat('Mengubah provinsi ' . $lama['provinsi'] . ' menjadi ' . $this->request->getPost('provinsi'));

        return redirect()->to(url_to('petugas.provinsi'))->with('sukses', 'Provinsi sudah diperbarui.');
    }

    public function delete(int $id): RedirectResponse
    {
        $model    = new ProvinsiModel();
        $provinsi = $model->find($id) ?? throw $this->tidakDitemukan();

        if (db_connect()->table('kabupaten_kota')->where('id_provinsi', $id)->countAllResults() > 0) {
            return redirect()->to(url_to('petugas.provinsi'))
                ->with('error', 'Provinsi ' . $provinsi['provinsi'] . ' masih memiliki kabupaten/kota. Hapus atau pindahkan kabupaten/kotanya terlebih dahulu.');
        }

        $model->delete($id);
        $this->catat('Menghapus provinsi ' . $provinsi['provinsi']);

        return redirect()->to(url_to('petugas.provinsi'))->with('sukses', 'Provinsi ' . $provinsi['provinsi'] . ' sudah dihapus.');
    }
}
