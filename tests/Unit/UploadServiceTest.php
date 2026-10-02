<?php

namespace Tests\Unit;

use App\Services\UploadService;
use CodeIgniter\Test\CIUnitTestCase;
use Tests\Support\FakeUploadedFile;

final class UploadServiceTest extends CIUnitTestCase
{
    private string $dir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dir = sys_get_temp_dir() . '/laporkan-upload-' . getmypid() . '-' . bin2hex(random_bytes(4));
    }

    protected function tearDown(): void
    {
        helper('filesystem');
        delete_files($this->dir, true);
        @rmdir($this->dir);
        parent::tearDown();
    }

    public function testExifDihapusDariFotoKecilDanThumbnail(): void
    {
        $asli = FakeUploadedFile::jpegDenganExif(300, 200);
        $this->assertStringContainsString("Exif\0\0", (string) file_get_contents($asli->getTempName()));

        $nama = (new UploadService($this->dir))->simpanFoto($asli, UploadService::FOLDER_PENGADUAN);

        foreach (["pengaduan/{$nama}", "pengaduan/thumb/{$nama}"] as $relatif) {
            $this->assertStringNotContainsString("Exif\0\0", (string) file_get_contents("{$this->dir}/{$relatif}"), $relatif);
        }
    }

    public function testExifDihapusDariBuktiLewatUnggahanSementara(): void
    {
        $upload = new UploadService($this->dir);
        $bukti  = $upload->simpanBukti(FakeUploadedFile::jpegDenganExif(300, 200), true);
        $this->assertTrue($upload->ambilBuktiSementara($bukti['nama']));

        $this->assertStringNotContainsString("Exif\0\0", (string) file_get_contents("{$this->dir}/bukti/{$bukti['nama']}"));
        $this->assertNull($upload->pathBukti($bukti['nama'], true));
    }

    public function testPathBuktiMenolakNamaBerkasDenganFolder(): void
    {
        $this->assertNull((new UploadService($this->dir))->pathBukti('../../app/Config/App.php'));
    }

    public function testFotoPonselDiputarTegakSebelumExifDihapus(): void
    {
        if (! extension_loaded('exif')) {
            $this->markTestSkipped('Needs the exif extension to read the Orientation tag.');
        }

        $nama = (new UploadService($this->dir))->simpanFoto(FakeUploadedFile::jpegDenganExif(300, 200, 6), UploadService::FOLDER_PENGADUAN);

        [$lebar, $tinggi] = getimagesize("{$this->dir}/pengaduan/{$nama}");
        $this->assertSame([200, 300], [$lebar, $tinggi]);
    }
}
