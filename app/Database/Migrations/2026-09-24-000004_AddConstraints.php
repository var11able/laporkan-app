<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use RuntimeException;

class AddConstraints extends Migration
{
    public function up(): void
    {
        $this->assertNoProblems();

        $this->db->query('ALTER TABLE `user` ADD UNIQUE KEY `uq_user_username` (`username`)');
        $this->db->query('ALTER TABLE `masyarakat` ADD UNIQUE KEY `uq_masyarakat_username` (`username`)');

        $this->db->query('ALTER TABLE `pengaduan` MODIFY `id_kelurahan` INT(11) NULL DEFAULT NULL');
        $this->db->query(
            'UPDATE `pengaduan` p LEFT JOIN `kelurahan` k ON k.id_kelurahan = p.id_kelurahan
             SET p.id_kelurahan = NULL WHERE k.id_kelurahan IS NULL',
        );

        $this->db->query('ALTER TABLE `log` MODIFY `id_user` INT(11) NULL DEFAULT NULL');
        $this->db->query(
            'UPDATE `log` l LEFT JOIN `user` u ON u.id_user = l.id_user
             SET l.id_user = NULL WHERE u.id_user IS NULL',
        );

        $this->db->query(
            'ALTER TABLE `pengaduan`
             ADD CONSTRAINT `fk_pengaduan_masyarakat` FOREIGN KEY (`id_masyarakat`) REFERENCES `masyarakat` (`id_masyarakat`) ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_pengaduan_kelurahan` FOREIGN KEY (`id_kelurahan`) REFERENCES `kelurahan` (`id_kelurahan`) ON DELETE SET NULL ON UPDATE CASCADE',
        );
        $this->db->query(
            'ALTER TABLE `tanggapan`
             ADD CONSTRAINT `fk_tanggapan_pengaduan` FOREIGN KEY (`id_pengaduan`) REFERENCES `pengaduan` (`id_pengaduan`) ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_tanggapan_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE RESTRICT ON UPDATE CASCADE',
        );
        $this->db->query(
            'ALTER TABLE `log`
             ADD CONSTRAINT `fk_log_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE',
        );
    }

    public function down(): void
    {
        $this->db->query('ALTER TABLE `log` DROP FOREIGN KEY `fk_log_user`');
        $this->db->query('ALTER TABLE `tanggapan` DROP FOREIGN KEY `fk_tanggapan_pengaduan`, DROP FOREIGN KEY `fk_tanggapan_user`');
        $this->db->query('ALTER TABLE `pengaduan` DROP FOREIGN KEY `fk_pengaduan_masyarakat`, DROP FOREIGN KEY `fk_pengaduan_kelurahan`');

        $this->db->query('UPDATE `log` SET `id_user` = 0 WHERE `id_user` IS NULL');
        $this->db->query('ALTER TABLE `log` MODIFY `id_user` INT(11) NOT NULL');
        $this->db->query('UPDATE `pengaduan` SET `id_kelurahan` = 0 WHERE `id_kelurahan` IS NULL');
        $this->db->query('ALTER TABLE `pengaduan` MODIFY `id_kelurahan` INT(11) NOT NULL');

        $this->db->query('ALTER TABLE `masyarakat` DROP INDEX `uq_masyarakat_username`');
        $this->db->query('ALTER TABLE `user` DROP INDEX `uq_user_username`');
    }

    private function assertNoProblems(): void
    {
        $checks = [
            'Username petugas ganda'                   => 'SELECT username AS item FROM `user` GROUP BY username HAVING COUNT(*) > 1',
            'Username warga ganda'                     => 'SELECT username AS item FROM `masyarakat` GROUP BY username HAVING COUNT(*) > 1',
            'Pengaduan tanpa warga (id_pengaduan)'     => 'SELECT p.id_pengaduan AS item FROM `pengaduan` p LEFT JOIN `masyarakat` m ON m.id_masyarakat = p.id_masyarakat WHERE m.id_masyarakat IS NULL',
            'Tanggapan tanpa pengaduan (id_tanggapan)' => 'SELECT t.id_tanggapan AS item FROM `tanggapan` t LEFT JOIN `pengaduan` p ON p.id_pengaduan = t.id_pengaduan WHERE p.id_pengaduan IS NULL',
            'Tanggapan tanpa petugas (id_tanggapan)'   => 'SELECT t.id_tanggapan AS item FROM `tanggapan` t LEFT JOIN `user` u ON u.id_user = t.id_user WHERE u.id_user IS NULL',
        ];

        $problems = [];

        foreach ($checks as $label => $sql) {
            $items = array_column($this->db->query($sql)->getResultArray(), 'item');
            if ($items !== []) {
                $problems[] = $label . ': ' . implode(', ', $items);
            }
        }

        if ($problems !== []) {
            throw new RuntimeException("Perbaiki data berikut sebelum menjalankan migration:\n- " . implode("\n- ", $problems));
        }
    }
}
