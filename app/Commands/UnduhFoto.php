<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\FotoStok;
use Config\Services;
use RuntimeException;
use Throwable;

class UnduhFoto extends BaseCommand
{
    protected $group       = 'LaporKan';
    protected $name        = 'foto:unduh';
    protected $description = 'Mengunduh foto ilustrasi Unsplash ke public/assets/foto agar disajikan dari server sendiri.';
    protected $usage       = 'foto:unduh [--ulang]';
    protected $options     = ['--ulang' => 'Unduh ulang meskipun berkas sudah ada.'];

    public function run(array $params): int
    {
        helper('tampilan');
        $config = config(FotoStok::class);
        $folder = FCPATH . 'assets/foto/';
        $ulang  = CLI::getOption('ulang') !== null;
        $gagal  = 0;

        if (! is_dir($folder)) {
            mkdir($folder, 0755, true);
        }

        foreach ($config->ukuran as $kunci => $daftar) {
            foreach ($daftar as [$lebar, $tinggi]) {
                $tujuan = FCPATH . 'assets/' . FotoStok::namaBerkas($kunci, $lebar, $tinggi);

                if (is_file($tujuan) && ! $ulang) {
                    CLI::write('Lewati ' . basename($tujuan));

                    continue;
                }

                try {
                    $respons = Services::curlrequest(['timeout' => 30, 'http_errors' => false])
                        ->get($config->urlSumber($kunci, $lebar, $tinggi, $kunci === 'hero' ? 45 : 65) . '&fm=webp');

                    if ($respons->getStatusCode() !== 200 || ! str_starts_with($respons->getHeaderLine('Content-Type'), 'image/webp')) {
                        throw new RuntimeException('HTTP ' . $respons->getStatusCode() . ' ' . $respons->getHeaderLine('Content-Type'));
                    }

                    file_put_contents($tujuan, $respons->getBody());
                    CLI::write('Unduh ' . basename($tujuan) . ' (' . ukuran_berkas(strlen($respons->getBody())) . ')', 'green');
                } catch (Throwable $e) {
                    $gagal++;
                    CLI::error('Gagal ' . basename($tujuan) . ': ' . $e->getMessage());
                }
            }
        }

        return $gagal === 0 ? EXIT_SUCCESS : EXIT_ERROR;
    }
}
