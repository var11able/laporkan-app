<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIndexes extends Migration
{
    public function up(): void
    {
        $this->db->query('ALTER TABLE `pengaduan` ADD KEY `idx_pengaduan_status_tgl` (`status_pengaduan`, `tgl_pengaduan`)');
        $this->db->query('ALTER TABLE `tanggapan` ADD KEY `idx_tanggapan_pengaduan` (`id_pengaduan`, `id_tanggapan`)');
    }

    public function down(): void
    {
        $this->db->query('ALTER TABLE `tanggapan` DROP INDEX `idx_tanggapan_pengaduan`');
        $this->db->query('ALTER TABLE `pengaduan` DROP INDEX `idx_pengaduan_status_tgl`');
    }
}
