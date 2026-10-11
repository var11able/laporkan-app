<?php

namespace App\Controllers\Publik;

use App\Controllers\BaseController;
use App\Models\LaporanModel;
use App\Models\TanggapanModel;
use CodeIgniter\HTTP\RedirectResponse;

class Lacak extends BaseController
{
    public function index(): RedirectResponse|string
    {
        $nomor          = strtoupper(trim((string) $this->request->getGet('nomor')));
        $tidakDitemukan = false;
        $laporan        = null;

        if ($nomor !== '') {
            $laporan = (new LaporanModel())->cariNomor($nomor);

            if ($laporan !== null && $laporan->tampilPublik()) {
                return redirect()->to(url_to('laporan.detail', $laporan->nomor_laporan));
            }

            $tidakDitemukan = $laporan === null;
        }

        return $this->tampil('publik/lacak', [
            'judul'          => 'Lacak Laporan',
            'menu'           => 'lacak',
            'nomor'          => $nomor,
            'tidakDitemukan' => $tidakDitemukan,
            'laporan'        => $laporan,
            'tanggapan'      => $laporan === null ? [] : (new TanggapanModel())->untukLaporan($laporan->id_laporan),
            'lebar'          => 'sempit',
        ]);
    }
}
