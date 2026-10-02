<?php

namespace App\Controllers\Warga;

use App\Controllers\BaseController;
use App\Entities\Pengaduan;
use App\Enums\StatusPengaduan;
use App\Exceptions\AturanBisnisException;
use App\Models\BuktiModel;
use App\Models\KabupatenKotaModel;
use App\Models\KategoriModel;
use App\Models\PengaduanModel;
use App\Models\ProvinsiModel;
use App\Models\TanggapanModel;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;

class Laporan extends BaseController
{
    public function index(): string
    {
        $idWarga   = $this->auth->warga()->getId();
        $grup      = StatusPengaduan::kelompokWarga();
        $kelompok  = (string) $this->request->getGet('kelompok');
        $kelompok  = array_key_exists($kelompok, $grup) ? $kelompok : '';
        $cari      = trim((string) $this->request->getGet('q'));
        $model     = new PengaduanModel();
        $perStatus = (new PengaduanModel())->jumlahPerStatus($idWarga);

        $laporan = $model->denganRelasi()
            ->milikWarga($idWarga)
            ->filter([
                'statusIn' => $kelompok === '' ? [] : array_map(static fn (StatusPengaduan $s) => $s->value, $grup[$kelompok]),
                'q'        => $cari,
            ])
            ->terbaru()
            ->paginate(10);

        $jumlah = [];

        foreach ($grup as $kunci => $daftar) {
            $jumlah[$kunci] = array_sum(array_map(static fn (StatusPengaduan $s) => $perStatus[$s->value], $daftar));
        }

        return $this->tampil('warga/laporan_index', [
            'judul'    => 'Laporan Saya',
            'menu'     => 'laporan-saya',
            'laporan'  => $laporan,
            'pager'    => $model->pager,
            'kelompok' => $kelompok,
            'cari'     => $cari,
            'jumlah'   => $jumlah,
            'total'    => array_sum($perStatus),
            'adaDraf'  => BuatLaporan::adaDraf(),
        ]);
    }

    public function show(int $id): string
    {
        $pengaduan = $this->laporanSaya($id);

        return $this->tampil('warga/laporan_detail', [
            'judul'     => 'Laporan ' . $pengaduan->nomor_laporan,
            'menu'      => 'laporan-saya',
            'pengaduan' => $pengaduan,
            'tanggapan' => (new TanggapanModel())->untukPengaduan($pengaduan->id_pengaduan),
            'bukti'     => (new BuktiModel())->untukPengaduan($pengaduan->id_pengaduan),
        ]);
    }

    public function edit(int $id): RedirectResponse|string
    {
        $pengaduan = $this->laporanSaya($id);

        if (! $pengaduan->status()->bisaDiubahWarga()) {
            return redirect()->to(url_to('warga.laporan.detail', $id))
                ->with('error', 'Laporan tidak dapat diubah karena sudah diperiksa admin.');
        }

        $idProvinsi = old('id_provinsi', (string) ($pengaduan->id_provinsi ?? ''));

        return $this->tampil('warga/laporan_ubah', [
            'judul'         => 'Ubah Laporan',
            'menu'          => 'laporan-saya',
            'lebar'         => 'sempit',
            'pengaduan'     => $pengaduan,
            'bukti'         => (new BuktiModel())->untukPengaduan($pengaduan->id_pengaduan),
            'opsiKategori'  => (new KategoriModel())->opsi(),
            'opsiProvinsi'  => (new ProvinsiModel())->opsi(),
            'opsiKabupaten' => $idProvinsi !== '' ? (new KabupatenKotaModel())->opsiUntukProvinsi((int) $idProvinsi) : [],
        ]);
    }

    public function update(int $id): RedirectResponse
    {
        $pengaduan = $this->laporanSaya($id);

        if (! $this->validasi(self::aturanIsi())) {
            return $this->kembaliDenganError();
        }

        $data            = $this->validator->getValidated();
        $data['rahasia'] = $this->request->getPost('rahasia') !== null;

        try {
            service('pengaduan')->ubahOlehWarga(
                $pengaduan,
                $this->auth->warga(),
                $data,
                $this->files('bukti'),
                array_map('intval', (array) $this->request->getPost('hapus_bukti')),
            );
        } catch (AturanBisnisException $e) {
            return $this->kembaliDenganError($e->getMessage());
        }

        return redirect()->to(url_to('warga.laporan.detail', $id))->with('sukses', 'Laporan sudah diperbarui.');
    }

    public function delete(int $id): RedirectResponse
    {
        $pengaduan = $this->laporanSaya($id);

        try {
            service('pengaduan')->hapusOlehWarga($pengaduan, $this->auth->warga());
        } catch (AturanBisnisException $e) {
            return redirect()->to(url_to('warga.laporan.detail', $id))->with('error', $e->getMessage());
        }

        return redirect()->to(url_to('warga.beranda'))->with('sukses', 'Laporan ' . $pengaduan->nomor_laporan . ' sudah dihapus.');
    }

    public function bukti(int $id, int $idBukti): ResponseInterface
    {
        $pengaduan = $this->laporanSaya($id);
        $bukti     = (new BuktiModel())->where('id_pengaduan', $pengaduan->id_pengaduan)->find($idBukti);
        $path      = $bukti === null ? null : service('upload')->pathBukti($bukti['nama_file']);

        if ($path === null) {
            throw $this->tidakDitemukan('Bukti tidak ditemukan.');
        }

        return kirim_berkas($this->response, $path, $bukti['mime'], $bukti['nama_asli']);
    }

    public static function aturanIsi(): array
    {
        return [
            'id_kategori'        => ['label' => 'Jenis dugaan korupsi', 'rules' => 'required|is_not_unique[kategori.id_kategori]'],
            'isi_laporan'        => ['label' => 'Kronologi', 'rules' => 'required|min_length[10]|max_length[5000]'],
            'waktu_kejadian'     => ['label' => 'Waktu kejadian', 'rules' => 'permit_empty|valid_date[Y-m-d]'],
            'perkiraan_kerugian' => ['label' => 'Perkiraan nilai', 'rules' => 'permit_empty|max_length[30]|regex_match[/^(Rp\.?\s*)?[0-9.,\s]+$/i]'],
            'instansi_terlapor'  => ['label' => 'Instansi', 'rules' => 'required|max_length[255]'],
            'pihak_terlapor'     => ['label' => 'Pihak yang terlibat', 'rules' => 'permit_empty|max_length[255]'],
            'id_kabupaten_kota'  => ['label' => 'Kabupaten/kota', 'rules' => 'required|is_not_unique[kabupaten_kota.id_kabupaten_kota]', 'errors' => ['required' => 'Pilih kabupaten atau kota.']],
        ];
    }

    private function laporanSaya(int $id): Pengaduan
    {
        $pengaduan = (new PengaduanModel())->denganRelasi()
            ->milikWarga($this->auth->warga()->getId())
            ->where('pengaduan.id_pengaduan', $id)
            ->first();

        if ($pengaduan === null) {
            throw $this->tidakDitemukan('Laporan tidak ditemukan.');
        }

        return $pengaduan;
    }
}
