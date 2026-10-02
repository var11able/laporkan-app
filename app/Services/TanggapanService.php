<?php

namespace App\Services;

use App\Entities\Pengaduan;
use App\Entities\Tanggapan;
use App\Entities\User;
use App\Enums\StatusPengaduan;
use App\Exceptions\AturanBisnisException;
use App\Models\PengaduanModel;
use App\Models\TanggapanModel;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\HTTP\Files\UploadedFile;
use Config\Database;
use Throwable;

class TanggapanService
{
    private readonly BaseConnection $db;

    public function __construct(
        private readonly TanggapanModel $tanggapanModel = new TanggapanModel(),
        private readonly PengaduanModel $pengaduanModel = new PengaduanModel(),
        private readonly UploadService $upload = new UploadService(),
        private readonly AuditLog $auditLog = new AuditLog(),
    ) {
        $this->db = Database::connect();
    }

    public function tambah(Pengaduan $pengaduan, User $petugas, StatusPengaduan $statusBaru, string $isi, ?UploadedFile $foto = null, ?string $instansiTujuan = null): Tanggapan
    {
        $instansiTujuan = trim((string) $instansiTujuan);

        if ($statusBaru === StatusPengaduan::Diteruskan && $instansiTujuan === '') {
            throw new AturanBisnisException('Sebutkan instansi tujuan laporan ini diteruskan.');
        }

        $statusLama = $pengaduan->status();

        if (! $statusLama->bisaBerubahKe($statusBaru)) {
            throw new AturanBisnisException(sprintf(
                'Status tidak dapat diubah dari "%s" ke "%s".',
                $statusLama->labelPetugas(),
                $statusBaru->labelPetugas(),
            ));
        }

        $isi = trim($isi);
        if ($isi === '') {
            throw new AturanBisnisException($statusBaru === StatusPengaduan::TidakValid
                ? 'Tuliskan alasan mengapa laporan tidak dapat ditindaklanjuti.'
                : 'Isi tanggapan wajib diisi.');
        }

        $namaFoto = $this->upload->simpanFoto($foto, UploadService::FOLDER_TANGGAPAN);

        $this->db->transException(true)->transStart();

        try {
            $id = $this->tanggapanModel->insert([
                'isi_tanggapan'    => $isi,
                'status_tanggapan' => $statusBaru->value,
                'instansi_tujuan'  => $statusBaru === StatusPengaduan::Diteruskan ? mb_substr($instansiTujuan, 0, 255) : null,
                'foto_tanggapan'   => $namaFoto,
                'id_pengaduan'     => $pengaduan->id_pengaduan,
                'id_user'          => $petugas->getId(),
            ]);

            if ($id === false) {
                throw new AturanBisnisException(implode(' ', $this->tanggapanModel->errors()));
            }

            $this->pengaduanModel->update($pengaduan->id_pengaduan, ['status_pengaduan' => $statusBaru->value]);

            $this->auditLog->catat(sprintf(
                'Menanggapi laporan %s: %s → %s%s',
                $pengaduan->nomor_laporan,
                $statusLama->labelPetugas(),
                $statusBaru->labelPetugas(),
                $statusBaru === StatusPengaduan::Diteruskan ? ' (' . $instansiTujuan . ')' : '',
            ), $petugas->getId());

            $this->db->transComplete();
        } catch (Throwable $e) {
            $this->db->transRollback();
            $this->upload->hapus(UploadService::FOLDER_TANGGAPAN, $namaFoto);

            throw $e;
        }

        return $this->tanggapanModel->find($id);
    }

    public function ubah(Tanggapan $tanggapan, User $petugas, string $isi, ?UploadedFile $foto = null): void
    {
        if ($tanggapan->id_user !== $petugas->getId() && ! $petugas->isAdmin()) {
            throw new AturanBisnisException('Anda hanya dapat mengubah tanggapan yang Anda tulis sendiri.');
        }

        $isi = trim($isi);
        if ($isi === '') {
            throw new AturanBisnisException('Isi tanggapan wajib diisi.');
        }

        $namaFoto  = $this->upload->simpanFoto($foto, UploadService::FOLDER_TANGGAPAN);
        $perubahan = ['isi_tanggapan' => $isi];

        if ($namaFoto !== null) {
            $perubahan['foto_tanggapan'] = $namaFoto;
        }

        $this->tanggapanModel->update($tanggapan->id_tanggapan, $perubahan);

        if ($namaFoto !== null) {
            $this->upload->hapus(UploadService::FOLDER_TANGGAPAN, $tanggapan->foto_tanggapan);
        }

        $this->auditLog->catat('Mengubah tanggapan #' . $tanggapan->id_tanggapan, $petugas->getId());
    }

    public function hapusTerakhir(Tanggapan $tanggapan, User $petugas): StatusPengaduan
    {
        if (! $petugas->isAdmin()) {
            $this->auditLog->catat('Ditolak: mencoba menghapus tanggapan #' . $tanggapan->id_tanggapan, $petugas->getId());

            throw new AturanBisnisException('Hanya administrator yang dapat menghapus tanggapan.');
        }

        $terakhir = $this->tanggapanModel->terakhirUntuk($tanggapan->id_pengaduan);

        if ($terakhir === null || $terakhir->id_tanggapan !== $tanggapan->id_tanggapan) {
            throw new AturanBisnisException('Hanya tanggapan terakhir yang dapat dihapus.');
        }

        $this->db->transException(true)->transStart();

        $this->tanggapanModel->delete($tanggapan->id_tanggapan);

        $sebelumnya = $this->tanggapanModel->terakhirUntuk($tanggapan->id_pengaduan);
        $statusKini = $sebelumnya?->status() ?? StatusPengaduan::Baru;

        $this->pengaduanModel->update($tanggapan->id_pengaduan, ['status_pengaduan' => $statusKini->value]);

        $this->auditLog->catat(sprintf(
            'Menghapus tanggapan #%d (%s); status laporan kembali ke %s',
            $tanggapan->id_tanggapan,
            $tanggapan->status()->labelPetugas(),
            $statusKini->labelPetugas(),
        ), $petugas->getId());

        $this->db->transComplete();

        $this->upload->hapus(UploadService::FOLDER_TANGGAPAN, $tanggapan->foto_tanggapan);

        return $statusKini;
    }

    public function ubahStatusMassal(array $idPengaduan, User $petugas, StatusPengaduan $statusBaru, string $isi, ?string $instansiTujuan = null): array
    {
        if ($statusBaru === StatusPengaduan::Diteruskan && trim((string) $instansiTujuan) === '') {
            throw new AturanBisnisException('Sebutkan instansi tujuan laporan ini diteruskan.');
        }

        $hasil = ['berhasil' => 0, 'dilewati' => []];

        foreach (array_unique($idPengaduan) as $id) {
            $pengaduan = $this->pengaduanModel->find((int) $id);

            if ($pengaduan === null) {
                continue;
            }

            if (! $pengaduan->status()->bisaBerubahKe($statusBaru)) {
                $hasil['dilewati'][] = (string) $pengaduan->nomor_laporan;

                continue;
            }

            $this->tambah($pengaduan, $petugas, $statusBaru, $isi, null, $instansiTujuan);
            $hasil['berhasil']++;
        }

        return $hasil;
    }
}
