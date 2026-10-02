<?php

namespace App\Controllers\Petugas;

use App\Models\KabupatenKotaModel;
use App\Models\ProvinsiModel;
use CodeIgniter\HTTP\RedirectResponse;

class KabupatenKota extends PetugasController
{
    public function index(): string
    {
        $idProvinsi = (int) $this->request->getGet('provinsi');

        return $this->tampil('petugas/wilayah/kabupaten_kota', [
            'judul'        => 'Kabupaten/Kota',
            'menu'         => 'kabupaten_kota',
            'idProvinsi'   => $idProvinsi,
            'opsiProvinsi' => (new ProvinsiModel())->opsi(),
            'kabupaten'    => (new KabupatenKotaModel())->semuaDenganProvinsi($idProvinsi ?: null),
        ]);
    }

    public function new(): string
    {
        return $this->tampil('petugas/wilayah/kabupaten_kota_form', [
            'judul'        => 'Tambah Kabupaten/Kota',
            'menu'         => 'kabupaten_kota',
            'kabupaten'    => null,
            'opsiProvinsi' => (new ProvinsiModel())->opsi(),
        ]);
    }

    public function create(): RedirectResponse
    {
        return $this->simpan(null);
    }

    public function edit(int $id): string
    {
        return $this->tampil('petugas/wilayah/kabupaten_kota_form', [
            'judul'        => 'Ubah Kabupaten/Kota',
            'menu'         => 'kabupaten_kota',
            'kabupaten'    => (new KabupatenKotaModel())->find($id) ?? throw $this->tidakDitemukan(),
            'opsiProvinsi' => (new ProvinsiModel())->opsi(),
        ]);
    }

    public function update(int $id): RedirectResponse
    {
        (new KabupatenKotaModel())->find($id) ?? throw $this->tidakDitemukan();

        return $this->simpan($id);
    }

    public function delete(int $id): RedirectResponse
    {
        $model     = new KabupatenKotaModel();
        $kabupaten = $model->find($id) ?? throw $this->tidakDitemukan();

        $model->delete($id);
        $this->catat('Menghapus kabupaten/kota ' . $kabupaten['kabupaten_kota']);

        return redirect()->to(url_to('petugas.kabupaten_kota'))->with('sukses', $kabupaten['kabupaten_kota'] . ' sudah dihapus.');
    }

    private function simpan(?int $id): RedirectResponse
    {
        $model = new KabupatenKotaModel();
        $data  = [
            'kabupaten_kota' => trim((string) $this->request->getPost('kabupaten_kota')),
            'id_provinsi'    => (string) $this->request->getPost('id_provinsi'),
        ];

        if (! $model->validate($data)) {
            return redirect()->back()->withInput()->with('_ci_validation_errors', $model->errors());
        }

        if ($model->adaDiProvinsi($data['kabupaten_kota'], (int) $data['id_provinsi'], $id)) {
            return redirect()->back()->withInput()->with('_ci_validation_errors', ['kabupaten_kota' => 'Kabupaten/kota ini sudah ada di provinsi tersebut.']);
        }

        if ($id === null) {
            $model->insert($data);
            $this->catat('Menambahkan kabupaten/kota ' . $data['kabupaten_kota']);
        } else {
            $model->update($id, $data);
            $this->catat('Mengubah kabupaten/kota #' . $id . ' menjadi ' . $data['kabupaten_kota']);
        }

        return redirect()->to(url_to('petugas.kabupaten_kota') . '?provinsi=' . (int) $data['id_provinsi'])->with('sukses', 'Kabupaten/kota sudah disimpan.');
    }
}
