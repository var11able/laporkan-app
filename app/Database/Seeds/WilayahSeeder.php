<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use RuntimeException;

class WilayahSeeder extends Seeder
{
    public function run(): void
    {
        $file   = __DIR__ . '/data/wilayah.csv';
        $handle = fopen($file, 'rb');

        if ($handle === false) {
            throw new RuntimeException('Tidak dapat membaca ' . $file);
        }

        fgetcsv($handle, escape: '');

        $provinsi  = [];
        $kabupaten = [];

        while (($row = fgetcsv($handle, escape: '')) !== false) {
            if (count($row) < 4 || trim((string) $row[0]) === '') {
                continue;
            }

            [$kodeProvinsi, $namaProvinsi, $kode, $nama] = array_map('trim', $row);
            $provinsi[$kodeProvinsi]                     = $namaProvinsi;
            $kabupaten[$kode]                            = ['kode_provinsi' => $kodeProvinsi, 'kabupaten_kota' => $nama];
        }

        fclose($handle);

        $idProvinsi = $this->simpanProvinsi($provinsi);
        $ada        = array_flip(array_column($this->db->table('kabupaten_kota')->select('kode')->where('kode IS NOT NULL')->get()->getResultArray(), 'kode'));
        $baru       = [];

        foreach ($kabupaten as $kode => $k) {
            if (! isset($ada[$kode])) {
                $baru[] = ['kode' => $kode, 'kabupaten_kota' => $k['kabupaten_kota'], 'id_provinsi' => $idProvinsi[$k['kode_provinsi']]];
            }
        }

        if ($baru !== []) {
            $this->db->table('kabupaten_kota')->insertBatch($baru);
        }
    }

    private function simpanProvinsi(array $provinsi): array
    {
        $ada  = array_column($this->db->table('provinsi')->select('id_provinsi, kode')->where('kode IS NOT NULL')->get()->getResultArray(), 'id_provinsi', 'kode');
        $baru = [];

        foreach ($provinsi as $kode => $nama) {
            if (! isset($ada[$kode])) {
                $baru[] = ['kode' => $kode, 'provinsi' => $nama];
            }
        }

        if ($baru !== []) {
            $this->db->table('provinsi')->insertBatch($baru);
        }

        return array_map('intval', array_column($this->db->table('provinsi')->select('id_provinsi, kode')->where('kode IS NOT NULL')->get()->getResultArray(), 'id_provinsi', 'kode'));
    }
}
