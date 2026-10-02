<?php

namespace Tests\Support;

use CodeIgniter\HTTP\Files\UploadedFile;

class FakeUploadedFile extends UploadedFile
{
    public static function dari(string $path, string $namaAsli, int $error = UPLOAD_ERR_OK): self
    {
        $salinan = tempnam(sys_get_temp_dir(), 'upl');
        copy($path, $salinan);

        return new self($salinan, $namaAsli, mime_content_type($salinan) ?: null, filesize($salinan) ?: 0, $error);
    }

    public static function gambar(int $lebar = 100, int $tinggi = 80, string $nama = 'foto.png'): self
    {
        $path  = tempnam(sys_get_temp_dir(), 'img') . '.png';
        $image = imagecreatetruecolor($lebar, $tinggi);
        imagefill($image, 0, 0, imagecolorallocate($image, 20, 90, 140));
        imagepng($image, $path);

        return self::dari($path, $nama);
    }

    public static function jpegDenganExif(int $lebar, int $tinggi, int $orientasi = 1, string $nama = 'foto.jpg'): self
    {
        $image = imagecreatetruecolor($lebar, $tinggi);
        imagefill($image, 0, 0, imagecolorallocate($image, 20, 90, 140));
        ob_start();
        imagejpeg($image);
        $jpeg = (string) ob_get_clean();

        $ifd = pack('v', 2)
            . pack('vvVvv', 0x0112, 3, 1, $orientasi, 0)
            . pack('vvVV', 0x8825, 4, 1, 38)
            . pack('V', 0)
            . pack('vV', 0, 0);
        $tiff    = 'II' . pack('v', 42) . pack('V', 8) . $ifd;
        $payload = "Exif\0\0" . $tiff;
        $app1    = "\xFF\xE1" . pack('n', strlen($payload) + 2) . $payload;

        $path = tempnam(sys_get_temp_dir(), 'img') . '.jpg';
        file_put_contents($path, substr($jpeg, 0, 2) . $app1 . substr($jpeg, 2));

        return self::dari($path, $nama);
    }

    public function isValid(): bool
    {
        return $this->error === UPLOAD_ERR_OK && is_file($this->path);
    }

    public function move(string $targetPath, ?string $name = null, bool $overwrite = false)
    {
        $targetPath = rtrim($targetPath, '/') . '/';
        $name ??= $this->getName();

        if (! is_dir($targetPath)) {
            mkdir($targetPath, 0775, true);
        }

        copy($this->path, $targetPath . $name);
        $this->hasMoved = true;
        $this->path     = $targetPath . $name;
        $this->name     = $name;

        return true;
    }
}
