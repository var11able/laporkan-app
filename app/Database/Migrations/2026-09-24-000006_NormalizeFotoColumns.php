<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class NormalizeFotoColumns extends Migration
{
    public function up(): void
    {
        $this->db->query('ALTER TABLE `pengaduan` MODIFY `foto` VARCHAR(500) NULL DEFAULT NULL');
        $this->db->query("UPDATE `pengaduan` SET `foto` = NULL WHERE `foto` IN ('default.png', '')");

        $this->db->query('ALTER TABLE `tanggapan` MODIFY `foto_tanggapan` VARCHAR(500) NULL DEFAULT NULL');
        $this->db->query("UPDATE `tanggapan` SET `foto_tanggapan` = NULL WHERE `foto_tanggapan` IN ('default.png', '')");
    }

    public function down(): void
    {
        $this->db->query("UPDATE `pengaduan` SET `foto` = 'default.png' WHERE `foto` IS NULL");
        $this->db->query("ALTER TABLE `pengaduan` MODIFY `foto` VARCHAR(500) NULL DEFAULT 'default.png'");

        $this->db->query("UPDATE `tanggapan` SET `foto_tanggapan` = 'default.png' WHERE `foto_tanggapan` IS NULL");
        $this->db->query("ALTER TABLE `tanggapan` MODIFY `foto_tanggapan` VARCHAR(500) NOT NULL DEFAULT 'default.png'");
    }
}
