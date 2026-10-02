<?php

namespace App\Controllers\Publik;

use App\Controllers\BaseController;
use App\Models\PengaduanModel;
use App\Models\TanggapanModel;
use CodeIgniter\HTTP\RedirectResponse;

class Lacak extends BaseController
{
    public function index(): RedirectResponse|string
    {
        $nomor          = strtoupper(trim((string) $this->request->getGet('nomor')));
        $tidakDitemukan = false;
        $pengaduan      = null;

        if ($nomor !== '') {
            $pengaduan = (new PengaduanModel())->cariNomor($nomor);

            if ($pengaduan !== null && $pengaduan->tampilPublik()) {
                return redirect()->to(url_to('laporan.detail', $pengaduan->nomor_laporan));
            }

            $tidakDitemukan = $pengaduan === null;
        }

        return $this->tampil('publik/lacak', [
            'judul'          => 'Lacak Laporan',
            'menu'           => 'lacak',
            'nomor'          => $nomor,
            'tidakDitemukan' => $tidakDitemukan,
            'pengaduan'      => $pengaduan,
            'tanggapan'      => $pengaduan === null ? [] : (new TanggapanModel())->untukPengaduan($pengaduan->id_pengaduan),
            'lebar'          => 'sempit',
        ]);
    }
}
