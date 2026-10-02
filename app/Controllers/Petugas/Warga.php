<?php

namespace App\Controllers\Petugas;

use App\Models\MasyarakatModel;
use App\Models\PengaduanModel;
use CodeIgniter\HTTP\RedirectResponse;

class Warga extends PetugasController
{
    public function index(): string
    {
        $q     = trim((string) $this->request->getGet('q'));
        $model = new MasyarakatModel();

        if ($q !== '') {
            $model->groupStart()->like('nama', $q)->orLike('username', $q)->orLike('no_telepon', $q)->groupEnd();
        }

        $warga = $model->select('masyarakat.*, (SELECT COUNT(*) FROM pengaduan p WHERE p.id_masyarakat = masyarakat.id_masyarakat) AS jumlah_laporan', false)
            ->orderBy('nama')
            ->paginate(25);

        return $this->tampil('petugas/warga/index', [
            'judul' => 'Masyarakat',
            'menu'  => 'warga',
            'warga' => $warga,
            'pager' => $model->pager,
            'q'     => $q,
        ]);
    }

    public function new(): string
    {
        return $this->tampil('petugas/warga/form', ['judul' => 'Tambah Akun Masyarakat', 'menu' => 'warga', 'akun' => null]);
    }

    public function create(): RedirectResponse
    {
        $aturan = $this->aturan() + [
            'password' => ['label' => 'Kata sandi', 'rules' => 'required|min_length[8]|max_length[72]'],
        ];

        if (! $this->validasi($aturan)) {
            return $this->kembaliDenganError();
        }

        $data             = $this->validator->getValidated();
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $model            = new MasyarakatModel();

        if ($model->insert($data) === false) {
            return redirect()->back()->withInput()->with('_ci_validation_errors', $model->errors());
        }

        $this->catat('Menambahkan akun masyarakat ' . $data['username']);

        return redirect()->to(url_to('petugas.warga'))->with('sukses', 'Akun masyarakat ' . $data['username'] . ' sudah dibuat.');
    }

    public function edit(int $id): string
    {
        return $this->tampil('petugas/warga/form', [
            'judul' => 'Ubah Akun Masyarakat',
            'menu'  => 'warga',
            'akun'  => (new MasyarakatModel())->find($id) ?? throw $this->tidakDitemukan(),
        ]);
    }

    public function update(int $id): RedirectResponse
    {
        $model = new MasyarakatModel();
        $akun  = $model->find($id) ?? throw $this->tidakDitemukan();

        $aturan = $this->aturan() + [
            'password' => ['label' => 'Kata sandi baru', 'rules' => 'permit_empty|min_length[8]|max_length[72]'],
        ];
        unset($aturan['username']);

        if (! $this->validasi($aturan)) {
            return $this->kembaliDenganError();
        }

        $data = $this->validator->getValidated();
        if (($data['password'] ?? '') !== '') {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['password']);
        }

        $model->update($id, $data);
        $this->catat('Mengubah akun masyarakat ' . $akun->username . (isset($data['password']) ? ' (termasuk kata sandi)' : ''));

        return redirect()->to(url_to('petugas.warga'))->with('sukses', 'Akun masyarakat ' . $akun->username . ' sudah diperbarui.');
    }

    public function delete(int $id): RedirectResponse
    {
        $akun = (new MasyarakatModel())->find($id) ?? throw $this->tidakDitemukan();

        $laporan = (new PengaduanModel())->milikWarga($id)->findAll();

        foreach ($laporan as $p) {
            service('pengaduan')->hapusOlehPetugas($p, $this->petugas());
        }

        (new MasyarakatModel())->delete($id);
        $this->catat('Menghapus akun masyarakat ' . $akun->username . ' beserta ' . count($laporan) . ' laporan');

        return redirect()->to(url_to('petugas.warga'))->with('sukses', 'Akun masyarakat ' . $akun->username . ' sudah dihapus.');
    }

    private function aturan(): array
    {
        return [
            'nama'       => ['label' => 'Nama lengkap', 'rules' => 'required|max_length[100]'],
            'username'   => ['label' => 'Username', 'rules' => 'required|min_length[3]|max_length[100]'],
            'no_telepon' => ['label' => 'Nomor telepon', 'rules' => 'required|regex_match[/^[0-9+ ]{8,20}$/]', 'errors' => [
                'regex_match' => 'Masukkan nomor telepon yang benar, contoh 081234567890.',
            ]],
            'alamat' => ['label' => 'Alamat', 'rules' => 'required|max_length[500]'],
        ];
    }
}
