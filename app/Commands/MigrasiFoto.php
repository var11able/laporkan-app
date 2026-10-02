<?php

namespace App\Commands;

use App\Services\UploadService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class MigrasiFoto extends BaseCommand
{
    protected $group       = 'Laporkan';
    protected $name        = 'laporkan:migrasi-foto';
    protected $description = 'Menyalin foto laporan dan tanggapan dari aplikasi CI3 ke public/uploads.';
    protected $usage       = 'laporkan:migrasi-foto [--sumber <folder>]';
    protected $options     = [
        '--sumber' => 'Folder img lama (bawaan: legacy/assets/img)',
    ];

    public function run(array $params)
    {
        $sumber = rtrim((string) ($params['sumber'] ?? CLI::getOption('sumber') ?? ROOTPATH . 'legacy/assets/img'), '/\\');

        if (! is_dir($sumber)) {
            CLI::error('Folder sumber tidak ditemukan: ' . $sumber);

            return EXIT_ERROR;
        }

        $upload    = service('upload');
        $db        = db_connect();
        $pekerjaan = [
            [UploadService::FOLDER_PENGADUAN, 'img_pengaduan', $db->table('pengaduan')->select('foto AS nama')->where('foto IS NOT NULL')],
            [UploadService::FOLDER_TANGGAPAN, 'img_tanggapan', $db->table('tanggapan')->select('foto_tanggapan AS nama')->where('foto_tanggapan IS NOT NULL')],
        ];

        $total = ['disalin' => 0, 'sudah_ada' => 0, 'hilang' => []];

        foreach ($pekerjaan as [$folder, $folderLama, $builder]) {
            foreach (array_unique(array_column($builder->get()->getResultArray(), 'nama')) as $nama) {
                $nama = basename((string) $nama);

                if ($upload->ada($folder, $nama)) {
                    $total['sudah_ada']++;

                    continue;
                }

                $file = $sumber . DIRECTORY_SEPARATOR . $folderLama . DIRECTORY_SEPARATOR . $nama;

                if (! is_file($file)) {
                    $total['hilang'][] = $folderLama . '/' . $nama;

                    continue;
                }

                $upload->impor($file, $folder, $nama);
                $total['disalin']++;
            }
        }

        CLI::write('Disalin: ' . $total['disalin'], 'green');
        CLI::write('Sudah ada sebelumnya: ' . $total['sudah_ada']);

        if ($total['hilang'] !== []) {
            CLI::write('Tidak ditemukan di folder lama (' . count($total['hilang']) . '):', 'yellow');

            foreach ($total['hilang'] as $hilang) {
                CLI::write('  - ' . $hilang, 'yellow');
            }

            return EXIT_ERROR;
        }

        return EXIT_SUCCESS;
    }
}
