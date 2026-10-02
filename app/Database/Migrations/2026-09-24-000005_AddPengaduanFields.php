<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPengaduanFields extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('pengaduan', [
            'nomor_laporan' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true, 'after' => 'id_pengaduan'],
            'detail_lokasi' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'id_kelurahan'],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true, 'after' => 'tgl_pengaduan'],
        ]);

        $this->db->query(
            "UPDATE `pengaduan`
             SET `nomor_laporan` = CONCAT('LP-', YEAR(`tgl_pengaduan`), '-', LPAD(`id_pengaduan`, 6, '0'))
             WHERE `nomor_laporan` IS NULL",
        );

        $this->db->query('ALTER TABLE `pengaduan` ADD UNIQUE KEY `uq_pengaduan_nomor` (`nomor_laporan`)');
    }

    public function down(): void
    {
        $this->forge->dropColumn('pengaduan', ['nomor_laporan', 'detail_lokasi', 'updated_at']);
    }
}
