<?php

namespace App\Controllers\Warga;

use App\Controllers\BaseController;
use App\Exceptions\AturanBisnisException;
use App\Models\KabupatenKotaModel;
use App\Models\KategoriModel;
use App\Models\PengaduanModel;
use App\Models\ProvinsiModel;
use App\Services\PengaduanService;
use App\Services\UploadService;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;

class BuatLaporan extends BaseController
{
    private const SESI = 'laporan_baru';

    private const LANGKAH = [
        'jenis'     => 'Jenis dugaan korupsi',
        'kronologi' => 'Ceritakan kejadiannya',
        'instansi'  => 'Di mana terjadinya?',
        'bukti'     => 'Tambahkan bukti',
        'periksa'   => 'Periksa laporan Anda',
    ];

    public function mulai(): RedirectResponse
    {
        if ($this->request->getGet('ulang') !== null) {
            $this->buangDraf();
        }

        return redirect()->to(url_to('warga.laporan.langkah', $this->langkahBelumSelesai('periksa') ?? 'periksa'));
    }

    public static function adaDraf(): bool
    {
        $draf = session(self::SESI);

        return is_array($draf) && $draf !== [];
    }

    public function langkah(string $slug): RedirectResponse|string
    {
        if (! array_key_exists($slug, self::LANGKAH)) {
            throw $this->tidakDitemukan();
        }

        if (($belum = $this->langkahBelumSelesai($slug)) !== null) {
            return redirect()->to(url_to('warga.laporan.langkah', $belum));
        }

        $draf  = $this->draf();
        $nomor = array_search($slug, array_keys(self::LANGKAH), true) + 1;
        $data  = [
            'judul'   => self::LANGKAH[$slug] . ' · Buat Laporan',
            'lebar'   => 'sempit',
            'slug'    => $slug,
            'nomor'   => $nomor,
            'total'   => count(self::LANGKAH),
            'draf'    => $draf,
            'bukti'   => $draf['bukti'] ?? [],
            'kembali' => $nomor > 1 ? url_to('warga.laporan.langkah', array_keys(self::LANGKAH)[$nomor - 2]) : url_to('warga.beranda'),
        ];

        if ($slug === 'jenis') {
            $data['kategori'] = (new KategoriModel())->semua();
        }

        if ($slug === 'instansi') {
            $idProvinsi            = old('id_provinsi', (string) ($draf['id_provinsi'] ?? ''));
            $data['opsiProvinsi']  = (new ProvinsiModel())->opsi();
            $data['idProvinsi']    = $idProvinsi;
            $data['opsiKabupaten'] = $idProvinsi !== '' ? (new KabupatenKotaModel())->opsiUntukProvinsi((int) $idProvinsi) : [];
        }

        if ($slug === 'periksa') {
            $kabupaten        = (new KabupatenKotaModel())->cariDenganProvinsi((int) $draf['id_kabupaten_kota']);
            $data['lokasi']   = $kabupaten !== null ? $kabupaten['kabupaten_kota'] . ', ' . $kabupaten['provinsi'] : '–';
            $data['kategori'] = (new KategoriModel())->find((int) $draf['id_kategori'])['kategori'] ?? '–';
        }

        return $this->tampil('warga/buat_' . $slug, $data);
    }

    public function simpanLangkah(string $slug): RedirectResponse
    {
        return match ($slug) {
            'jenis'     => $this->simpanJenis(),
            'kronologi' => $this->simpanKronologi(),
            'instansi'  => $this->simpanInstansi(),
            'bukti'     => $this->simpanBukti(),
            'periksa'   => $this->kirim(),
            default     => throw $this->tidakDitemukan(),
        };
    }

    public function lihatBukti(string $nama): ResponseInterface
    {
        $bukti = array_values(array_filter($this->draf()['bukti'] ?? [], static fn (array $b) => $b['nama'] === $nama));
        $path  = $bukti === [] ? null : service('upload')->pathBukti($nama, true);

        if ($path === null) {
            throw $this->tidakDitemukan();
        }

        return kirim_berkas($this->response, $path, $bukti[0]['mime'], $bukti[0]['asli']);
    }

    public function terkirim(int $id): string
    {
        $pengaduan = (new PengaduanModel())->milikWarga($this->auth->warga()->getId())->find($id);

        if ($pengaduan === null) {
            throw $this->tidakDitemukan();
        }

        return $this->tampil('warga/terkirim', ['judul' => 'Laporan terkirim', 'lebar' => 'sempit', 'pengaduan' => $pengaduan]);
    }

    private function simpanJenis(): RedirectResponse
    {
        $aturan = ['id_kategori' => ['label' => 'Jenis dugaan korupsi', 'rules' => 'required|is_not_unique[kategori.id_kategori]', 'errors' => [
            'required' => 'Pilih jenis dugaan korupsi. Jika ragu, pilih "Lainnya".',
        ]]];

        if (! $this->validasi($aturan)) {
            return $this->kembaliDenganError();
        }

        $this->perbaruiDraf(['id_kategori' => (int) $this->validator->getValidated()['id_kategori']]);

        return $this->lanjut('jenis');
    }

    private function simpanKronologi(): RedirectResponse
    {
        $aturan = [
            'isi_laporan' => ['label' => 'Kronologi', 'rules' => 'required|min_length[10]|max_length[5000]', 'errors' => [
                'required'   => 'Ceritakan kejadian dugaan korupsi yang ingin Anda laporkan.',
                'min_length' => 'Ceritakan kejadiannya sedikit lebih lengkap (minimal 10 karakter).',
            ]],
            'waktu_kejadian' => ['label' => 'Waktu kejadian', 'rules' => 'permit_empty|valid_date[Y-m-d]', 'errors' => [
                'valid_date' => 'Masukkan tanggal yang benar, contoh 2026-08-17.',
            ]],
            'perkiraan_kerugian' => ['label' => 'Perkiraan kerugian', 'rules' => 'permit_empty|max_length[30]|regex_match[/^(Rp\.?\s*)?[0-9.,\s]+$/i]', 'errors' => [
                'regex_match' => 'Tulis dalam angka saja, contoh 15000000.',
            ]],
        ];

        if (! $this->validasi($aturan)) {
            return $this->kembaliDenganError();
        }

        $data = $this->validator->getValidated();

        if (! empty($data['waktu_kejadian']) && $data['waktu_kejadian'] > date('Y-m-d')) {
            return redirect()->back()->withInput()->with('_ci_validation_errors', ['waktu_kejadian' => 'Waktu kejadian tidak boleh setelah hari ini.']);
        }

        $this->perbaruiDraf([
            'isi_laporan'        => trim($data['isi_laporan']),
            'waktu_kejadian'     => ($data['waktu_kejadian'] ?? '') ?: null,
            'perkiraan_kerugian' => PengaduanService::angkaRupiah($data['perkiraan_kerugian'] ?? null),
        ]);

        return $this->lanjut('kronologi');
    }

    private function simpanInstansi(): RedirectResponse
    {
        $aturan = [
            'instansi_terlapor' => ['label' => 'Instansi', 'rules' => 'required|max_length[255]', 'errors' => [
                'required' => 'Sebutkan instansi atau kantor tempat kejadian.',
            ]],
            'pihak_terlapor'    => ['label' => 'Pihak yang terlibat', 'rules' => 'permit_empty|max_length[255]'],
            'id_provinsi'       => ['label' => 'Provinsi', 'rules' => 'required|is_not_unique[provinsi.id_provinsi]', 'errors' => ['required' => 'Pilih provinsi.']],
            'id_kabupaten_kota' => ['label' => 'Kabupaten/kota', 'rules' => 'required|is_not_unique[kabupaten_kota.id_kabupaten_kota]', 'errors' => ['required' => 'Pilih kabupaten atau kota.']],
        ];

        if (! $this->validasi($aturan)) {
            return $this->kembaliDenganError();
        }

        $data      = $this->validator->getValidated();
        $kabupaten = (new KabupatenKotaModel())->find((int) $data['id_kabupaten_kota']);

        if ((int) $kabupaten['id_provinsi'] !== (int) $data['id_provinsi']) {
            return redirect()->back()->withInput()->with('_ci_validation_errors', ['id_kabupaten_kota' => 'Kabupaten/kota tidak berada di provinsi yang dipilih.']);
        }

        $this->perbaruiDraf([
            'instansi_terlapor' => trim($data['instansi_terlapor']),
            'pihak_terlapor'    => trim((string) ($data['pihak_terlapor'] ?? '')) ?: null,
            'id_provinsi'       => (int) $data['id_provinsi'],
            'id_kabupaten_kota' => (int) $data['id_kabupaten_kota'],
        ]);

        return $this->lanjut('instansi');
    }

    private function simpanBukti(): RedirectResponse
    {
        $bukti = $this->draf()['bukti'] ?? [];
        $hapus = (string) $this->request->getPost('hapus_bukti');

        if ($hapus !== '') {
            service('upload')->hapusBukti($hapus, true);
            $this->perbaruiDraf(['bukti' => array_values(array_filter($bukti, static fn (array $b) => $b['nama'] !== $hapus))]);

            return redirect()->to(url_to('warga.laporan.langkah', 'bukti'))->with('info', 'Bukti dihapus.');
        }

        $files = $this->files('bukti');

        if (count($bukti) + count($files) > UploadService::MAKS_BUKTI) {
            return redirect()->back()->with('_ci_validation_errors', ['bukti' => 'Bukti paling banyak ' . UploadService::MAKS_BUKTI . ' berkas.']);
        }

        foreach ($files as $file) {
            try {
                $bukti[] = service('upload')->simpanBukti($file, true);
            } catch (AturanBisnisException $e) {
                $this->perbaruiDraf(['bukti' => $bukti]);

                return redirect()->back()->with('_ci_validation_errors', ['bukti' => $e->getMessage()]);
            }
        }

        $this->perbaruiDraf(['bukti' => $bukti]);

        if ($this->request->getPost('aksi') === 'unggah') {
            return redirect()->to(url_to('warga.laporan.langkah', 'bukti'))->with('sukses', $files === [] ? 'Pilih berkas terlebih dahulu.' : 'Bukti ditambahkan.');
        }

        $this->perbaruiDraf(['bukti_selesai' => true]);

        return $this->lanjut('bukti');
    }

    private function kirim(): RedirectResponse
    {
        if (($belum = $this->langkahBelumSelesai('periksa')) !== null) {
            return redirect()->to(url_to('warga.laporan.langkah', $belum));
        }

        $draf = $this->draf();

        try {
            $pengaduan = service('pengaduan')->buat(
                [
                    'id_kategori'        => $draf['id_kategori'],
                    'isi_laporan'        => $draf['isi_laporan'],
                    'waktu_kejadian'     => $draf['waktu_kejadian'] ?? null,
                    'perkiraan_kerugian' => $draf['perkiraan_kerugian'] ?? null,
                    'instansi_terlapor'  => $draf['instansi_terlapor'],
                    'pihak_terlapor'     => $draf['pihak_terlapor'] ?? null,
                    'id_kabupaten_kota'  => $draf['id_kabupaten_kota'],
                    'rahasia'            => $this->request->getPost('rahasia') !== null,
                ],
                $this->auth->warga()->getId(),
                $draf['bukti'] ?? [],
            );
        } catch (AturanBisnisException $e) {
            return redirect()->to(url_to('warga.laporan.langkah', 'periksa'))->with('error', $e->getMessage());
        }

        session()->remove(self::SESI);

        return redirect()->to(url_to('warga.laporan.terkirim', $pengaduan->id_pengaduan));
    }

    private function lanjut(string $slug): RedirectResponse
    {
        $slugs = array_keys(self::LANGKAH);
        $index = array_search($slug, $slugs, true);

        if ($this->request->getPost('kembali_ke_periksa') !== null && $this->langkahBelumSelesai('periksa') === null) {
            return redirect()->to(url_to('warga.laporan.langkah', 'periksa'));
        }

        return redirect()->to(url_to('warga.laporan.langkah', $slugs[$index + 1]));
    }

    private function langkahBelumSelesai(string $slug): ?string
    {
        $draf   = $this->draf();
        $syarat = [
            'jenis'     => ! empty($draf['id_kategori']),
            'kronologi' => ! empty($draf['isi_laporan']),
            'instansi'  => ! empty($draf['id_kabupaten_kota']),
            'bukti'     => ! empty($draf['bukti_selesai']),
        ];

        foreach ($syarat as $langkah => $selesai) {
            if ($langkah === $slug) {
                return null;
            }

            if (! $selesai) {
                return $langkah;
            }
        }

        return null;
    }

    private function buangDraf(): void
    {
        foreach ($this->draf()['bukti'] ?? [] as $b) {
            service('upload')->hapusBukti($b['nama'], true);
        }

        session()->remove(self::SESI);
    }

    private function draf(): array
    {
        $draf = session(self::SESI);

        return is_array($draf) ? $draf : [];
    }

    private function perbaruiDraf(array $data): void
    {
        session()->set(self::SESI, array_merge($this->draf(), $data));
    }
}
