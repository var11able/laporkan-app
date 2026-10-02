<?php

namespace App\Controllers\Petugas;

use App\Entities\Tanggapan as TanggapanEntity;
use App\Enums\StatusPengaduan;
use App\Exceptions\AturanBisnisException;
use App\Models\PengaduanModel;
use App\Models\TanggapanModel;
use App\Services\UploadService;
use CodeIgniter\HTTP\RedirectResponse;

class Tanggapan extends PetugasController
{
    public function create(int $idPengaduan): RedirectResponse
    {
        $pengaduan = (new PengaduanModel())->find($idPengaduan) ?? throw $this->tidakDitemukan('Laporan tidak ditemukan.');

        $aturan = [
            'status_tanggapan' => ['label' => 'Status baru', 'rules' => 'required|in_list[proses,valid,pengerjaan,selesai,tidak_valid]', 'errors' => [
                'required' => 'Pilih status baru untuk laporan ini.',
            ]],
            'isi_tanggapan' => ['label' => 'Isi tanggapan', 'rules' => 'required|max_length[5000]', 'errors' => [
                'required' => 'Tulis tanggapan yang akan dibaca pelapor.',
            ]],
            'instansi_tujuan' => ['label' => 'Instansi tujuan', 'rules' => 'permit_empty|max_length[255]'],
        ];

        if ($this->file('foto_tanggapan') !== null) {
            $aturan['foto_tanggapan'] = ['label' => 'Foto bukti', 'rules' => UploadService::aturanValidasi('foto_tanggapan')];
        }

        if (! $this->validasi($aturan)) {
            return $this->kembaliDenganError();
        }

        $data = $this->validator->getValidated();

        try {
            service('tanggapan')->tambah(
                $pengaduan,
                $this->petugas(),
                StatusPengaduan::from($data['status_tanggapan']),
                $data['isi_tanggapan'],
                $this->file('foto_tanggapan'),
                $data['instansi_tujuan'] ?? null,
            );
        } catch (AturanBisnisException $e) {
            return $this->kembaliDenganError($e->getMessage());
        }

        return redirect()->to(url_to('petugas.laporan.detail', $idPengaduan))
            ->with('sukses', 'Tanggapan tersimpan. Status laporan sekarang: ' . StatusPengaduan::from($data['status_tanggapan'])->labelPetugas() . '.');
    }

    public function edit(int $id): string
    {
        $tanggapan = $this->cari($id);

        return $this->tampil('petugas/laporan/tanggapan_ubah', [
            'judul'     => 'Ubah Tanggapan',
            'menu'      => 'laporan',
            'tanggapan' => $tanggapan,
            'pengaduan' => (new PengaduanModel())->find($tanggapan->id_pengaduan),
        ]);
    }

    public function update(int $id): RedirectResponse
    {
        $tanggapan = $this->cari($id);
        $aturan    = ['isi_tanggapan' => ['label' => 'Isi tanggapan', 'rules' => 'required|max_length[5000]']];

        if ($this->file('foto_tanggapan') !== null) {
            $aturan['foto_tanggapan'] = ['label' => 'Foto bukti', 'rules' => UploadService::aturanValidasi('foto_tanggapan')];
        }

        if (! $this->validasi($aturan)) {
            return $this->kembaliDenganError();
        }

        try {
            service('tanggapan')->ubah($tanggapan, $this->petugas(), $this->validator->getValidated()['isi_tanggapan'], $this->file('foto_tanggapan'));
        } catch (AturanBisnisException $e) {
            return $this->kembaliDenganError($e->getMessage());
        }

        return redirect()->to(url_to('petugas.laporan.detail', $tanggapan->id_pengaduan))->with('sukses', 'Tanggapan sudah diperbarui.');
    }

    public function delete(int $id): RedirectResponse
    {
        $tanggapan = $this->cari($id);

        try {
            $status = service('tanggapan')->hapusTerakhir($tanggapan, $this->petugas());
        } catch (AturanBisnisException $e) {
            return redirect()->to(url_to('petugas.laporan.detail', $tanggapan->id_pengaduan))->with('error', $e->getMessage());
        }

        return redirect()->to(url_to('petugas.laporan.detail', $tanggapan->id_pengaduan))
            ->with('sukses', 'Tanggapan dihapus. Status laporan kembali ke ' . $status->labelPetugas() . '.');
    }

    public function massal(): RedirectResponse
    {
        $aturan = [
            'id'               => ['label' => 'Laporan', 'rules' => 'required', 'errors' => ['required' => 'Pilih minimal satu laporan.']],
            'id.*'             => ['label' => 'Laporan', 'rules' => 'is_natural_no_zero'],
            'status_tanggapan' => ['label' => 'Status baru', 'rules' => 'required|in_list[proses,valid,pengerjaan,selesai,tidak_valid]'],
            'isi_tanggapan'    => ['label' => 'Isi tanggapan', 'rules' => 'required|max_length[5000]'],
            'instansi_tujuan'  => ['label' => 'Instansi tujuan', 'rules' => 'permit_empty|max_length[255]'],
        ];

        if (! $this->validasi($aturan)) {
            $pesan = implode(' ', $this->validator->getErrors());

            return redirect()->back()->with('error', $pesan);
        }

        $data = $this->validator->getValidated();

        try {
            $hasil = service('tanggapan')->ubahStatusMassal(
                array_map('intval', (array) $data['id']),
                $this->petugas(),
                StatusPengaduan::from($data['status_tanggapan']),
                $data['isi_tanggapan'],
                $data['instansi_tujuan'] ?? null,
            );
        } catch (AturanBisnisException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        $pesan = $hasil['berhasil'] . ' laporan diperbarui.';
        if ($hasil['dilewati'] !== []) {
            $pesan .= ' Dilewati karena statusnya tidak sesuai: ' . implode(', ', $hasil['dilewati']) . '.';
        }

        return redirect()->back()->with($hasil['berhasil'] > 0 ? 'sukses' : 'error', $pesan);
    }

    private function cari(int $id): TanggapanEntity
    {
        return (new TanggapanModel())->find($id) ?? throw $this->tidakDitemukan('Tanggapan tidak ditemukan.');
    }
}
