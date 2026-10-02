<?php

namespace Tests\Feature;

use CodeIgniter\Test\StreamFilterTrait;
use Tests\Support\DatabaseTestCase;

final class MigrasiFotoTest extends DatabaseTestCase
{
    use StreamFilterTrait;

    public function testMenyalinFotoYangDirujukDanMelaporkanYangHilang(): void
    {
        $sumber = sys_get_temp_dir() . '/laporkan-legacy-' . getmypid();
        @mkdir($sumber . '/img_pengaduan', 0775, true);
        @mkdir($sumber . '/img_tanggapan', 0775, true);

        $gambar = imagecreatetruecolor(900, 600);
        imagejpeg($gambar, $sumber . '/img_pengaduan/jalan.jpg');

        $warga = $this->buatWarga();
        $this->db->table('pengaduan')->insert([
            'nomor_laporan' => 'LP-2024-000001', 'isi_laporan' => 'Laporan lama', 'tgl_pengaduan' => '2024-05-01 10:00:00',
            'foto'          => 'jalan.jpg', 'status_pengaduan' => 'belum_ditanggapi', 'id_masyarakat' => $warga->getId(),
        ]);
        $this->db->table('pengaduan')->insert([
            'nomor_laporan' => 'LP-2024-000002', 'isi_laporan' => 'Laporan lama tanpa file', 'tgl_pengaduan' => '2024-05-02 10:00:00',
            'foto'          => 'hilang.jpg', 'status_pengaduan' => 'belum_ditanggapi', 'id_masyarakat' => $warga->getId(),
        ]);

        command('laporkan:migrasi-foto --sumber ' . $sumber);

        $this->assertFileExists($this->uploadDir . '/pengaduan/jalan.jpg');
        $this->assertFileExists($this->uploadDir . '/pengaduan/thumb/jalan.jpg');
        $this->assertSame(480, getimagesize($this->uploadDir . '/pengaduan/thumb/jalan.jpg')[0]);
        $this->assertStringContainsString('img_pengaduan/hilang.jpg', $this->getStreamFilterBuffer());

        helper('filesystem');
        delete_files($sumber, true);
    }
}
