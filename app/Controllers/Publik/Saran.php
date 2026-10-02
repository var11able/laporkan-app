<?php

namespace App\Controllers\Publik;

use App\Controllers\BaseController;
use App\Models\SaranModel;
use CodeIgniter\HTTP\RedirectResponse;

class Saran extends BaseController
{
    public function index(): string
    {
        return $this->tampil('publik/saran', ['judul' => 'Kritik & Saran', 'lebar' => 'sempit']);
    }

    public function kirim(): RedirectResponse
    {
        $aturan = [
            'nama'       => ['label' => 'Nama', 'rules' => 'permit_empty|max_length[100]'],
            'no_telepon' => ['label' => 'Nomor telepon', 'rules' => 'permit_empty|regex_match[/^[0-9+ ]{8,20}$/]', 'errors' => [
                'regex_match' => 'Masukkan nomor telepon yang benar, contoh 081234567890.',
            ]],
            'alamat' => ['label' => 'Alamat', 'rules' => 'permit_empty|max_length[500]'],
            'saran'  => ['label' => 'Kritik atau saran', 'rules' => 'required|min_length[10]|max_length[5000]'],
        ];

        if (! $this->validasi($aturan)) {
            return $this->kembaliDenganError();
        }

        $data = $this->validator->getValidated();

        foreach (['nama', 'no_telepon', 'alamat'] as $field) {
            $data[$field] = trim((string) ($data[$field] ?? '')) ?: null;
        }

        if ($data['nama'] !== null) {
            $data['nama'] = mb_convert_case($data['nama'], MB_CASE_TITLE);
        }
        (new SaranModel())->insert($data);

        return redirect()->to(url_to('saran'))
            ->with('sukses', 'Terima kasih. Kritik dan saran Anda sudah kami terima dan akan dibaca oleh admin.');
    }
}
