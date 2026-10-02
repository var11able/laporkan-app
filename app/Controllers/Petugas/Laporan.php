<?php

namespace App\Controllers\Petugas;

use App\Controllers\Warga\Laporan as LaporanWarga;
use App\Entities\Pengaduan;
use App\Exceptions\AturanBisnisException;
use App\Models\BuktiModel;
use App\Models\KabupatenKotaModel;
use App\Models\KategoriModel;
use App\Models\MasyarakatModel;
use App\Models\PengaduanModel;
use App\Models\ProvinsiModel;
use App\Models\TanggapanModel;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;

class Laporan extends PetugasController
{
    private const PER_HALAMAN = 20;

    private const SESI_IDENTITAS = 'identitas_dibuka';

    public function index(): string
    {
        $filter = [
            'status'   => (string) $this->request->getGet('status'),
            'provinsi' => (string) $this->request->getGet('provinsi'),
            'kategori' => (string) $this->request->getGet('kategori'),
            'dari'     => (string) $this->request->getGet('dari'),
            'sampai'   => (string) $this->request->getGet('sampai'),
            'q'        => trim((string) $this->request->getGet('q')),
        ];

        $model   = new PengaduanModel();
        $laporan = $model->denganRelasi()->filter($filter)->terbaru()->paginate(self::PER_HALAMAN);

        return $this->tampil('petugas/laporan/index', [
            'judul'        => 'Kotak Masuk Laporan',
            'menu'         => 'laporan',
            'laporan'      => $laporan,
            'pager'        => $model->pager,
            'filter'       => $filter,
            'jumlah'       => (new PengaduanModel())->jumlahPerStatus(),
            'opsiProvinsi' => (new ProvinsiModel())->opsi(),
            'opsiKategori' => (new KategoriModel())->opsi(),
        ]);
    }

    public function show(int $id): string
    {
        $pengaduan = $this->cari($id);
        $tanggapan = (new TanggapanModel())->untukPengaduan($id);

        return $this->tampil('petugas/laporan/show', [
            'judul'            => 'Laporan ' . $pengaduan->nomor_laporan,
            'menu'             => 'laporan',
            'pengaduan'        => $pengaduan,
            'tanggapan'        => $tanggapan,
            'terakhir'         => $tanggapan === [] ? null : end($tanggapan),
            'bukti'            => (new BuktiModel())->untukPengaduan($id),
            'identitasTerbuka' => $this->identitasTerbuka($pengaduan),
        ]);
    }

    public function new(): string
    {
        return $this->tampil('petugas/laporan/form', $this->dataForm(null) + ['judul' => 'Buat Laporan atas Nama Pelapor']);
    }

    public function create(): RedirectResponse
    {
        if (! $this->validasi($this->aturan(true))) {
            return $this->kembaliDenganError();
        }

        $data            = $this->validator->getValidated();
        $data['rahasia'] = $this->request->getPost('rahasia') !== null;

        try {
            $pengaduan = service('pengaduan')->buat($data, (int) $data['id_masyarakat'], $this->files('bukti'), $this->petugas());
        } catch (AturanBisnisException $e) {
            return $this->kembaliDenganError($e->getMessage());
        }

        return redirect()->to(url_to('petugas.laporan.detail', $pengaduan->id_pengaduan))
            ->with('sukses', 'Laporan ' . $pengaduan->nomor_laporan . ' sudah dibuat.');
    }

    public function edit(int $id): string
    {
        $pengaduan = $this->cari($id);

        return $this->tampil('petugas/laporan/form', $this->dataForm($pengaduan) + ['judul' => 'Ubah Laporan ' . $pengaduan->nomor_laporan]);
    }

    public function update(int $id): RedirectResponse
    {
        $pengaduan = $this->cari($id);

        if (! $this->validasi($this->aturan(false))) {
            return $this->kembaliDenganError();
        }

        $data            = $this->validator->getValidated();
        $data['rahasia'] = $this->request->getPost('rahasia') !== null;

        try {
            service('pengaduan')->ubahOlehPetugas(
                $pengaduan,
                $this->petugas(),
                $data,
                $this->files('bukti'),
                array_map('intval', (array) $this->request->getPost('hapus_bukti')),
            );
        } catch (AturanBisnisException $e) {
            return $this->kembaliDenganError($e->getMessage());
        }

        return redirect()->to(url_to('petugas.laporan.detail', $id))->with('sukses', 'Laporan sudah diperbarui.');
    }

    public function delete(int $id): RedirectResponse
    {
        $pengaduan = $this->cari($id);

        try {
            service('pengaduan')->hapusOlehPetugas($pengaduan, $this->petugas());
        } catch (AturanBisnisException $e) {
            return redirect()->to(url_to('petugas.laporan.detail', $id))->with('error', $e->getMessage());
        }

        return redirect()->to(url_to('petugas.laporan'))->with('sukses', 'Laporan ' . $pengaduan->nomor_laporan . ' sudah dihapus.');
    }

    public function publik(int $id): RedirectResponse
    {
        $pengaduan = $this->cari($id);
        $ringkasan = $this->request->getPost('hapus') !== null ? null : (string) $this->request->getPost('ringkasan_publik');

        try {
            service('pengaduan')->aturRingkasanPublik($pengaduan, $this->petugas(), $ringkasan);
        } catch (AturanBisnisException $e) {
            return redirect()->back()->withInput()->with('_ci_validation_errors', ['ringkasan_publik' => $e->getMessage()]);
        }

        $pesan = match (true) {
            $ringkasan === null || trim($ringkasan) === '' => 'Laporan disembunyikan dari halaman publik.',
            $pengaduan->status()->bisaTampilPublik()       => 'Ringkasan publik disimpan dan sudah tampil di halaman publik.',
            default                                        => 'Ringkasan publik disimpan. Laporan tampil di halaman publik setelah berstatus Valid.',
        };

        return redirect()->to(url_to('petugas.laporan.detail', $id))->with('sukses', $pesan);
    }

    public function identitas(int $id): RedirectResponse
    {
        $pengaduan = $this->cari($id);

        try {
            service('pengaduan')->bukaIdentitas($pengaduan, $this->petugas());
        } catch (AturanBisnisException $e) {
            return redirect()->to(url_to('petugas.laporan.detail', $id))->with('error', $e->getMessage());
        }

        $dibuka   = (array) session(self::SESI_IDENTITAS);
        $dibuka[] = $id;
        session()->set(self::SESI_IDENTITAS, array_values(array_unique(array_map('intval', $dibuka))));

        return redirect()->to(url_to('petugas.laporan.detail', $id))->with('info', 'Identitas pelapor dibuka. Tindakan ini tercatat di log aktivitas.');
    }

    public function bukti(int $id, int $idBukti): ResponseInterface
    {
        $bukti = (new BuktiModel())->where('id_pengaduan', $id)->find($idBukti);
        $path  = $bukti === null ? null : service('upload')->pathBukti($bukti['nama_file']);

        if ($path === null) {
            throw $this->tidakDitemukan('Bukti tidak ditemukan.');
        }

        return kirim_berkas($this->response, $path, $bukti['mime'], $bukti['nama_asli']);
    }

    private function identitasTerbuka(Pengaduan $pengaduan): bool
    {
        return ! $pengaduan->rahasia || in_array($pengaduan->id_pengaduan, array_map('intval', (array) session(self::SESI_IDENTITAS)), true);
    }

    private function cari(int $id): Pengaduan
    {
        return (new PengaduanModel())->cariDenganRelasi($id) ?? throw $this->tidakDitemukan('Laporan tidak ditemukan.');
    }

    private function aturan(bool $baru): array
    {
        return LaporanWarga::aturanIsi() + [
            'id_masyarakat' => ['label' => 'Pelapor', 'rules' => ($baru ? 'required' : 'permit_empty') . '|is_not_unique[masyarakat.id_masyarakat]'],
        ];
    }

    private function dataForm(?Pengaduan $pengaduan): array
    {
        $idProvinsi = old('id_provinsi', $pengaduan === null ? '' : (string) $pengaduan->id_provinsi);
        $warga      = (new MasyarakatModel())->select('id_masyarakat, nama, username')->orderBy('nama')->findAll();

        return [
            'menu'             => 'laporan',
            'pengaduan'        => $pengaduan,
            'identitasTerbuka' => $pengaduan === null || $this->identitasTerbuka($pengaduan),
            'bukti'            => $pengaduan === null ? [] : (new BuktiModel())->untukPengaduan($pengaduan->id_pengaduan),
            'opsiWarga'        => array_column(array_map(static fn ($w) => ['id' => $w->id_masyarakat, 'label' => $w->nama . ' (' . $w->username . ')'], $warga), 'label', 'id'),
            'opsiKategori'     => (new KategoriModel())->opsi(),
            'opsiProvinsi'     => (new ProvinsiModel())->opsi(),
            'opsiKabupaten'    => $idProvinsi !== '' ? (new KabupatenKotaModel())->opsiUntukProvinsi((int) $idProvinsi) : [],
        ];
    }
}
