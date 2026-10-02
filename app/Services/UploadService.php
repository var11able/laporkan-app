<?php

namespace App\Services;

use App\Exceptions\AturanBisnisException;
use CodeIgniter\HTTP\Files\UploadedFile;
use Config\Services;

class UploadService
{
    public const FOLDER_PENGADUAN = 'pengaduan';
    public const FOLDER_TANGGAPAN = 'tanggapan';
    public const MAX_KB           = 5120;
    public const MAKS_BUKTI       = 3;
    public const MIME_DIIZINKAN   = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    public const MIME_BUKTI       = [...self::MIME_DIIZINKAN, 'application/pdf'];
    private const LEBAR_MAKS      = 1600;
    private const LEBAR_THUMB     = 480;

    private readonly string $basePath;
    private readonly string $sementaraPath;
    private readonly string $buktiPath;

    public function __construct(?string $basePath = null, ?string $sementaraPath = null, ?string $buktiPath = null)
    {
        $this->basePath      = rtrim($basePath ?? FCPATH . 'uploads', '/\\') . DIRECTORY_SEPARATOR;
        $this->sementaraPath = rtrim($sementaraPath ?? ($basePath !== null ? $basePath . '/sementara' : WRITEPATH . 'uploads/sementara'), '/\\');
        $this->buktiPath     = rtrim($buktiPath ?? ($basePath !== null ? $basePath . '/bukti' : WRITEPATH . 'uploads/bukti'), '/\\');
    }

    public static function aturanValidasi(string $field, bool $wajib = false): string
    {
        $aturan = 'is_image[' . $field . ']|mime_in[' . $field . ',' . implode(',', self::MIME_DIIZINKAN) . ']|max_size[' . $field . ',' . self::MAX_KB . ']';

        return ($wajib ? 'uploaded[' . $field . ']|' : '') . $aturan;
    }

    public function simpanFoto(?UploadedFile $file, string $folder): ?string
    {
        if ($file === null || $file->getError() === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if (! $file->isValid() || $file->hasMoved()) {
            throw new AturanBisnisException('Foto gagal diunggah. Coba pilih foto lagi.');
        }

        if (! in_array($file->getMimeType(), self::MIME_DIIZINKAN, true) || $file->getSizeByUnit('kb') > self::MAX_KB) {
            throw new AturanBisnisException('Foto harus berupa JPG, PNG, WebP, atau GIF dan tidak lebih dari 5 MB.');
        }

        $dir = $this->basePath . $folder . DIRECTORY_SEPARATOR;
        $this->pastikanFolder($dir . 'thumb');

        $nama = $file->getRandomName();
        $file->move($dir, $nama);

        $this->ubahUkuran($dir . $nama, $dir . $nama, self::LEBAR_MAKS);
        $this->ubahUkuran($dir . $nama, $dir . 'thumb' . DIRECTORY_SEPARATOR . $nama, self::LEBAR_THUMB);

        return $nama;
    }

    public function simpanBukti(UploadedFile $file, bool $sementara = false): array
    {
        if (! $file->isValid() || $file->hasMoved()) {
            throw new AturanBisnisException('Berkas gagal diunggah. Coba pilih berkasnya lagi.');
        }

        $mime = (string) $file->getMimeType();

        if (! in_array($mime, self::MIME_BUKTI, true) || $file->getSizeByUnit('kb') > self::MAX_KB) {
            throw new AturanBisnisException('Bukti harus berupa foto (JPG, PNG, WebP, GIF) atau dokumen PDF, dan tidak lebih dari 5 MB.');
        }

        if ($mime === 'application/pdf' && ! str_starts_with((string) file_get_contents($file->getTempName(), false, null, 0, 5), '%PDF-')) {
            throw new AturanBisnisException('Dokumen PDF tidak dapat dibaca. Coba simpan ulang dokumennya lalu unggah lagi.');
        }

        $dir  = $sementara ? $this->folderSementara() : $this->folderBukti();
        $nama = bin2hex(random_bytes(16)) . '.' . ($mime === 'application/pdf' ? 'pdf' : ($file->guessExtension() ?: 'jpg'));
        $asli = mb_substr(basename((string) $file->getClientName()), 0, 255) ?: $nama;

        $this->pastikanFolder($dir);
        $file->move($dir, $nama);

        if ($mime !== 'application/pdf') {
            $this->ubahUkuran($dir . $nama, $dir . $nama, self::LEBAR_MAKS);
        }

        return ['nama' => $nama, 'asli' => $asli, 'mime' => $mime, 'ukuran' => (int) filesize($dir . $nama)];
    }

    public function ambilBuktiSementara(string $nama): bool
    {
        $sumber = $this->folderSementara() . basename($nama);

        if (! is_file($sumber)) {
            return false;
        }

        $this->pastikanFolder($this->folderBukti());

        return rename($sumber, $this->folderBukti() . basename($nama));
    }

    public function hapusBukti(?string $nama, bool $sementara = false): void
    {
        if ($nama === null || $nama === '' || basename($nama) !== $nama) {
            return;
        }

        $path = ($sementara ? $this->folderSementara() : $this->folderBukti()) . $nama;

        if (is_file($path)) {
            unlink($path);
        }
    }

    public function pathBukti(string $nama, bool $sementara = false): ?string
    {
        if (basename($nama) !== $nama) {
            return null;
        }

        $path = ($sementara ? $this->folderSementara() : $this->folderBukti()) . $nama;

        return is_file($path) ? $path : null;
    }

    private function folderSementara(): string
    {
        return $this->sementaraPath . DIRECTORY_SEPARATOR;
    }

    private function folderBukti(): string
    {
        return $this->buktiPath . DIRECTORY_SEPARATOR;
    }

    public function impor(string $sumber, string $folder, string $nama): void
    {
        $dir = $this->basePath . $folder . DIRECTORY_SEPARATOR;
        $this->pastikanFolder($dir . 'thumb');

        copy($sumber, $dir . $nama);
        $this->ubahUkuran($dir . $nama, $dir . $nama, self::LEBAR_MAKS);
        $this->ubahUkuran($dir . $nama, $dir . 'thumb' . DIRECTORY_SEPARATOR . $nama, self::LEBAR_THUMB);
    }

    public function ada(string $folder, string $nama): bool
    {
        return is_file($this->basePath . $folder . DIRECTORY_SEPARATOR . $nama);
    }

    public function hapus(string $folder, ?string $nama): void
    {
        if ($nama === null || $nama === '' || basename($nama) !== $nama) {
            return;
        }

        foreach ([$folder . DIRECTORY_SEPARATOR . $nama, $folder . DIRECTORY_SEPARATOR . 'thumb' . DIRECTORY_SEPARATOR . $nama] as $relatif) {
            $path = $this->basePath . $relatif;
            if (is_file($path)) {
                unlink($path);
            }
        }
    }

    public static function url(string $folder, ?string $nama): ?string
    {
        return $nama === null || $nama === '' ? null : base_url('uploads/' . $folder . '/' . rawurlencode($nama));
    }

    public static function thumbUrl(string $folder, ?string $nama): ?string
    {
        return $nama === null || $nama === '' ? null : base_url('uploads/' . $folder . '/thumb/' . rawurlencode($nama));
    }

    private function ubahUkuran(string $sumber, string $tujuan, int $lebarMaks): void
    {
        if (@getimagesize($sumber) === false) {
            return;
        }

        $gambar = Services::image('gd', null, false)->withFile($sumber)->reorient(true);

        if ($gambar->getWidth() > $lebarMaks) {
            $gambar->resize($lebarMaks, $lebarMaks, true, 'width');
        }

        $gambar->save($tujuan, 82);
    }

    private function pastikanFolder(string $dir): void
    {
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
    }
}
