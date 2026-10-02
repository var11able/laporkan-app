<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLaporanKorupsiFields extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('pengaduan', [
            'instansi_terlapor'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'isi_laporan'],
            'pihak_terlapor'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'instansi_terlapor'],
            'waktu_kejadian'     => ['type' => 'DATE', 'null' => true, 'after' => 'pihak_terlapor'],
            'perkiraan_kerugian' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true, 'after' => 'waktu_kejadian'],
            'rahasia'            => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0, 'after' => 'perkiraan_kerugian'],
            'ringkasan_publik'   => ['type' => 'TEXT', 'null' => true, 'after' => 'rahasia'],
        ]);

        $this->forge->addColumn('tanggapan', [
            'instansi_tujuan' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'status_tanggapan'],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('tanggapan', 'instansi_tujuan');
        $this->forge->dropColumn('pengaduan', ['instansi_terlapor', 'pihak_terlapor', 'waktu_kejadian', 'perkiraan_kerugian', 'rahasia', 'ringkasan_publik']);
    }
}
