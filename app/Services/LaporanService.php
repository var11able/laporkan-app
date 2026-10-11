<?php

namespace App\Services;

use App\Entities\LaporanKorupsi;
use App\Entities\User;
use App\Entities\Warga;
use App\Exceptions\AturanBisnisException;
use App\Models\BuktiModel;
use App\Models\LaporanModel;
use App\Models\TanggapanModel;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\HTTP\Files\UploadedFile;
use Config\Database;
use RuntimeException;
use Throwable;

class LaporanService
{
    private const FIELD_ISI = ['isi_laporan', 'id_kabupaten_kota', 'id_kategori', 'instansi_terlapor', 'pihak_terlapor', 'waktu_kejadian', 'perkiraan_kerugian'];

    private readonly BaseConnection $db;

    public function __construct(
        private readonly LaporanModel $laporanModel = new LaporanModel(),
        private readonly TanggapanModel $tanggapanModel = new TanggapanModel(),
        private readonly UploadService $upload = new UploadService(),
        private readonly AuditLog $auditLog = new AuditLog(),
        private readonly BuktiModel $buktiModel = new BuktiModel(),
    ) {
        $this->db = Database::connect();
    }

    public static function nomorLaporan(int $id, ?int $tahun = null): string
    {
        return sprintf('LP-%d-%06d', $tahun ?? (int) date('Y'), $id);
    }

    public static function angkaRupiah(mixed $nilai): ?int
    {
        $angka = preg_replace('/\D+/', '', (string) $nilai) ?? '';

        return $angka === '' ? null : (int) $angka;
    }

    public function buat(array $data, int $idWarga, array $bukti = [], ?User $olehPetugas = null): LaporanKorupsi
    {
        if (count($bukti) > UploadService::MAKS_BUKTI) {
            throw new AturanBisnisException('Bukti paling banyak ' . UploadService::MAKS_BUKTI . ' berkas.');
        }

        $tersimpan = [];

        try {
            foreach ($bukti as $b) {
                if ($b instanceof UploadedFile) {
                    $tersimpan[] = $this->upload->simpanBukti($b);
                } elseif ($this->upload->ambilBuktiSementara($b['nama'])) {
                    $tersimpan[] = $b;
                }
            }

            $this->db->transException(true)->transStart();

            $id = $this->laporanModel->insert($this->isian($data) + [
                'status_laporan' => 'belum_ditanggapi',
                'id_warga'       => $idWarga,
                'rahasia'        => ! empty($data['rahasia']) ? 1 : 0,
            ]);

            if ($id === false) {
                throw new AturanBisnisException(implode(' ', $this->laporanModel->errors()));
            }

            $this->laporanModel->update($id, ['nomor_laporan' => self::nomorLaporan((int) $id)]);
            $this->simpanBaris((int) $id, $tersimpan);

            if ($olehPetugas !== null) {
                $this->auditLog->catat('Menambahkan laporan ' . self::nomorLaporan((int) $id) . ' atas nama pelapor #' . $idWarga, $olehPetugas->getId());
            }

            $this->db->transComplete();
        } catch (Throwable $e) {
            $this->db->transRollback();

            foreach ($tersimpan as $b) {
                $this->upload->hapusBukti($b['nama']);
            }

            throw $e;
        }

        return $this->laporanModel->cariDenganRelasi((int) $id) ?? throw new RuntimeException('Laporan tidak ditemukan setelah disimpan.');
    }

    public function ubahOlehWarga(LaporanKorupsi $laporan, Warga $warga, array $data, array $buktiBaru = [], array $hapusBukti = []): void
    {
        if (! $laporan->milik($warga->getId())) {
            throw new AturanBisnisException('Laporan ini bukan milik Anda.');
        }

        if (! $laporan->status()->bisaDiubahWarga()) {
            throw new AturanBisnisException('Laporan tidak dapat diubah karena sudah diperiksa admin.');
        }

        $this->ubah($laporan, $data, $buktiBaru, $hapusBukti);
    }

    public function ubahOlehPetugas(LaporanKorupsi $laporan, User $petugas, array $data, array $buktiBaru = [], array $hapusBukti = []): void
    {
        $this->ubah($laporan, $data, $buktiBaru, $hapusBukti);
        $this->auditLog->catat('Mengubah laporan ' . $laporan->nomor_laporan, $petugas->getId());
    }

    public function hapusOlehWarga(LaporanKorupsi $laporan, Warga $warga): void
    {
        if (! $laporan->milik($warga->getId())) {
            throw new AturanBisnisException('Laporan ini bukan milik Anda.');
        }

        if (! $laporan->status()->bisaDiubahWarga()) {
            throw new AturanBisnisException('Laporan tidak dapat dihapus karena sudah diperiksa admin.');
        }

        $this->hapus($laporan);
    }

    public function hapusOlehPetugas(LaporanKorupsi $laporan, User $petugas): void
    {
        if (! $petugas->isAdmin()) {
            $this->auditLog->catat('Ditolak: mencoba menghapus laporan ' . $laporan->nomor_laporan, $petugas->getId());

            throw new AturanBisnisException('Hanya administrator yang dapat menghapus laporan.');
        }

        $this->hapus($laporan);
        $this->auditLog->catat('Menghapus laporan ' . $laporan->nomor_laporan, $petugas->getId());
    }

    public function aturRingkasanPublik(LaporanKorupsi $laporan, User $petugas, ?string $ringkasan): void
    {
        $ringkasan = $this->bersihkan($ringkasan);

        if ($ringkasan !== null && mb_strlen($ringkasan) < 20) {
            throw new AturanBisnisException('Ringkasan publik terlalu pendek (minimal 20 karakter).');
        }

        $this->laporanModel->update($laporan->id_laporan, ['ringkasan_publik' => $ringkasan]);
        $this->auditLog->catat(
            ($ringkasan === null ? 'Menyembunyikan laporan ' : 'Mengatur ringkasan publik laporan ') . $laporan->nomor_laporan,
            $petugas->getId(),
        );
    }

    public function bukaIdentitas(LaporanKorupsi $laporan, User $petugas): void
    {
        if (! $petugas->isAdmin()) {
            $this->auditLog->catat('Ditolak: mencoba membuka identitas pelapor laporan ' . $laporan->nomor_laporan, $petugas->getId());

            throw new AturanBisnisException('Hanya administrator yang dapat membuka identitas pelapor yang dirahasiakan.');
        }

        $this->auditLog->catat('Membuka identitas pelapor laporan ' . $laporan->nomor_laporan, $petugas->getId());
    }

    private function ubah(LaporanKorupsi $laporan, array $data, array $buktiBaru, array $hapusBukti): void
    {
        $lama    = $this->buktiModel->untukLaporan($laporan->id_laporan);
        $dihapus = array_values(array_filter($lama, static fn (array $b) => in_array((int) $b['id_bukti'], $hapusBukti, true)));
        $sisa    = count($lama) - count($dihapus);

        if ($sisa + count($buktiBaru) > UploadService::MAKS_BUKTI) {
            throw new AturanBisnisException('Bukti paling banyak ' . UploadService::MAKS_BUKTI . ' berkas. Hapus salah satu bukti lama terlebih dahulu.');
        }

        $tersimpan = [];

        try {
            foreach ($buktiBaru as $file) {
                $tersimpan[] = $this->upload->simpanBukti($file);
            }

            $perubahan = $this->isian($data) + ['id_laporan' => $laporan->id_laporan];

            if (array_key_exists('rahasia', $data)) {
                $perubahan['rahasia'] = ! empty($data['rahasia']) ? 1 : 0;
            }

            if (! empty($data['id_warga'])) {
                $perubahan['id_warga'] = (int) $data['id_warga'];
            }

            $this->db->transException(true)->transStart();

            if (! $this->laporanModel->update($laporan->id_laporan, $perubahan)) {
                throw new AturanBisnisException(implode(' ', $this->laporanModel->errors()));
            }

            $this->simpanBaris($laporan->id_laporan, $tersimpan);

            if ($dihapus !== []) {
                $this->buktiModel->delete(array_map(static fn (array $b) => (int) $b['id_bukti'], $dihapus));
            }

            $this->db->transComplete();
        } catch (Throwable $e) {
            $this->db->transRollback();

            foreach ($tersimpan as $b) {
                $this->upload->hapusBukti($b['nama']);
            }

            throw $e;
        }

        foreach ($dihapus as $b) {
            $this->upload->hapusBukti($b['nama_file']);
        }
    }

    private function isian(array $data): array
    {
        $isian = [];

        foreach (self::FIELD_ISI as $field) {
            if (! array_key_exists($field, $data)) {
                continue;
            }

            $isian[$field] = match ($field) {
                'isi_laporan'                      => trim((string) $data[$field]),
                'id_kabupaten_kota', 'id_kategori' => ((int) $data[$field]) ?: null,
                'perkiraan_kerugian'               => self::angkaRupiah($data[$field]),
                default                            => $this->bersihkan($data[$field] === null ? null : (string) $data[$field]),
            };
        }

        return $isian;
    }

    private function simpanBaris(int $idLaporan, array $bukti): void
    {
        foreach ($bukti as $b) {
            $this->buktiModel->insert([
                'id_laporan' => $idLaporan,
                'nama_file'  => $b['nama'],
                'nama_asli'  => $b['asli'],
                'mime'       => $b['mime'],
                'ukuran'     => $b['ukuran'],
            ]);
        }
    }

    private function hapus(LaporanKorupsi $laporan): void
    {
        $fotoTanggapan = array_map(
            static fn ($t) => $t->foto_tanggapan,
            $this->tanggapanModel->where('id_laporan', $laporan->id_laporan)->findAll(),
        );
        $bukti = array_column($this->buktiModel->untukLaporan($laporan->id_laporan), 'nama_file');

        $this->db->transException(true)->transStart();
        $this->laporanModel->delete($laporan->id_laporan);
        $this->db->transComplete();

        $this->upload->hapus(UploadService::FOLDER_LAPORAN, $laporan->foto);

        foreach ($fotoTanggapan as $foto) {
            $this->upload->hapus(UploadService::FOLDER_TANGGAPAN, $foto);
        }

        foreach ($bukti as $nama) {
            $this->upload->hapusBukti($nama);
        }
    }

    private function bersihkan(?string $teks): ?string
    {
        $teks = trim((string) $teks);

        return $teks === '' ? null : $teks;
    }
}
