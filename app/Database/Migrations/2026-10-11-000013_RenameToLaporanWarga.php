<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenameToLaporanWarga extends Migration
{
    private const FOREIGN_KEYS = [
        'pengaduan'       => ['fk_pengaduan_masyarakat', 'fk_pengaduan_kelurahan', 'fk_pengaduan_kabupaten_kota', 'fk_pengaduan_kategori'],
        'tanggapan'       => ['fk_tanggapan_pengaduan'],
        'bukti_pengaduan' => ['fk_bukti_pengaduan'],
    ];

    public function up(): void
    {
        $this->dropForeignKeys(self::FOREIGN_KEYS);

        $this->db->query('ALTER TABLE `pengaduan` DROP COLUMN `id_kelurahan`');
        $this->db->query('DROP TABLE IF EXISTS `kelurahan`');
        $this->db->query('DROP TABLE IF EXISTS `kecamatan`');

        $this->db->query('RENAME TABLE `pengaduan` TO `laporan`, `masyarakat` TO `warga`, `bukti_pengaduan` TO `bukti_laporan`');

        $this->db->query('ALTER TABLE `laporan` RENAME COLUMN `id_pengaduan` TO `id_laporan`');
        $this->db->query('ALTER TABLE `laporan` RENAME COLUMN `tgl_pengaduan` TO `tgl_laporan`');
        $this->db->query('ALTER TABLE `laporan` RENAME COLUMN `status_pengaduan` TO `status_laporan`');
        $this->db->query('ALTER TABLE `laporan` RENAME COLUMN `id_masyarakat` TO `id_warga`');
        $this->db->query('ALTER TABLE `warga` RENAME COLUMN `id_masyarakat` TO `id_warga`');
        $this->db->query('ALTER TABLE `tanggapan` RENAME COLUMN `id_pengaduan` TO `id_laporan`');
        $this->db->query('ALTER TABLE `bukti_laporan` RENAME COLUMN `id_pengaduan` TO `id_laporan`');

        $this->renameIndexes('laporan', ['id_masyarakat' => 'id_warga', 'uq_pengaduan_nomor' => 'uq_laporan_nomor', 'idx_pengaduan_status_tgl' => 'idx_laporan_status_tgl']);
        $this->renameIndexes('warga', ['uq_masyarakat_username' => 'uq_warga_username']);
        $this->renameIndexes('tanggapan', ['id_pengaduan' => 'id_laporan', 'idx_tanggapan_pengaduan' => 'idx_tanggapan_laporan']);
        $this->renameIndexes('bukti_laporan', ['idx_bukti_pengaduan' => 'idx_bukti_laporan']);

        $this->db->query(
            'ALTER TABLE `laporan`
             ADD CONSTRAINT `fk_laporan_warga` FOREIGN KEY (`id_warga`) REFERENCES `warga` (`id_warga`) ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_laporan_kabupaten_kota` FOREIGN KEY (`id_kabupaten_kota`) REFERENCES `kabupaten_kota` (`id_kabupaten_kota`) ON DELETE SET NULL ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_laporan_kategori` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON DELETE SET NULL ON UPDATE CASCADE',
        );
        $this->db->query(
            'ALTER TABLE `tanggapan`
             ADD CONSTRAINT `fk_tanggapan_laporan` FOREIGN KEY (`id_laporan`) REFERENCES `laporan` (`id_laporan`) ON DELETE CASCADE ON UPDATE CASCADE',
        );
        $this->db->query(
            'ALTER TABLE `bukti_laporan`
             ADD CONSTRAINT `fk_bukti_laporan` FOREIGN KEY (`id_laporan`) REFERENCES `laporan` (`id_laporan`) ON DELETE CASCADE ON UPDATE CASCADE',
        );
    }

    public function down(): void
    {
        $this->dropForeignKeys([
            'laporan'       => ['fk_laporan_warga', 'fk_laporan_kabupaten_kota', 'fk_laporan_kategori'],
            'tanggapan'     => ['fk_tanggapan_laporan'],
            'bukti_laporan' => ['fk_bukti_laporan'],
        ]);

        $this->renameIndexes('bukti_laporan', ['idx_bukti_laporan' => 'idx_bukti_pengaduan']);
        $this->renameIndexes('tanggapan', ['id_laporan' => 'id_pengaduan', 'idx_tanggapan_laporan' => 'idx_tanggapan_pengaduan']);
        $this->renameIndexes('warga', ['uq_warga_username' => 'uq_masyarakat_username']);
        $this->renameIndexes('laporan', ['id_warga' => 'id_masyarakat', 'uq_laporan_nomor' => 'uq_pengaduan_nomor', 'idx_laporan_status_tgl' => 'idx_pengaduan_status_tgl']);

        $this->db->query('ALTER TABLE `bukti_laporan` RENAME COLUMN `id_laporan` TO `id_pengaduan`');
        $this->db->query('ALTER TABLE `tanggapan` RENAME COLUMN `id_laporan` TO `id_pengaduan`');
        $this->db->query('ALTER TABLE `warga` RENAME COLUMN `id_warga` TO `id_masyarakat`');
        $this->db->query('ALTER TABLE `laporan` RENAME COLUMN `id_warga` TO `id_masyarakat`');
        $this->db->query('ALTER TABLE `laporan` RENAME COLUMN `status_laporan` TO `status_pengaduan`');
        $this->db->query('ALTER TABLE `laporan` RENAME COLUMN `tgl_laporan` TO `tgl_pengaduan`');
        $this->db->query('ALTER TABLE `laporan` RENAME COLUMN `id_laporan` TO `id_pengaduan`');

        $this->db->query('RENAME TABLE `laporan` TO `pengaduan`, `warga` TO `masyarakat`, `bukti_laporan` TO `bukti_pengaduan`');

        $this->db->query(
            'CREATE TABLE `kecamatan` (
                `id_kecamatan` INT(11) NOT NULL AUTO_INCREMENT,
                `kecamatan` VARCHAR(100) NOT NULL,
                PRIMARY KEY (`id_kecamatan`),
                UNIQUE KEY `uq_kecamatan` (`kecamatan`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci',
        );
        $this->db->query(
            'CREATE TABLE `kelurahan` (
                `id_kelurahan` INT(11) NOT NULL AUTO_INCREMENT,
                `kelurahan` VARCHAR(100) NOT NULL,
                `id_kecamatan` INT(11) NOT NULL,
                PRIMARY KEY (`id_kelurahan`),
                UNIQUE KEY `uq_kelurahan_per_kecamatan` (`id_kecamatan`, `kelurahan`),
                CONSTRAINT `fk_kelurahan_kecamatan` FOREIGN KEY (`id_kecamatan`) REFERENCES `kecamatan` (`id_kecamatan`) ON DELETE CASCADE ON UPDATE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci',
        );
        $this->db->query('ALTER TABLE `pengaduan` ADD COLUMN `id_kelurahan` INT(11) NULL DEFAULT NULL AFTER `id_masyarakat`, ADD KEY `id_kelurahan` (`id_kelurahan`)');

        $this->db->query(
            'ALTER TABLE `pengaduan`
             ADD CONSTRAINT `fk_pengaduan_kelurahan` FOREIGN KEY (`id_kelurahan`) REFERENCES `kelurahan` (`id_kelurahan`) ON DELETE SET NULL ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_pengaduan_masyarakat` FOREIGN KEY (`id_masyarakat`) REFERENCES `masyarakat` (`id_masyarakat`) ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_pengaduan_kabupaten_kota` FOREIGN KEY (`id_kabupaten_kota`) REFERENCES `kabupaten_kota` (`id_kabupaten_kota`) ON DELETE SET NULL ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_pengaduan_kategori` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON DELETE SET NULL ON UPDATE CASCADE',
        );
        $this->db->query(
            'ALTER TABLE `tanggapan`
             ADD CONSTRAINT `fk_tanggapan_pengaduan` FOREIGN KEY (`id_pengaduan`) REFERENCES `pengaduan` (`id_pengaduan`) ON DELETE CASCADE ON UPDATE CASCADE',
        );
        $this->db->query(
            'ALTER TABLE `bukti_pengaduan`
             ADD CONSTRAINT `fk_bukti_pengaduan` FOREIGN KEY (`id_pengaduan`) REFERENCES `pengaduan` (`id_pengaduan`) ON DELETE CASCADE ON UPDATE CASCADE',
        );
    }

    private function dropForeignKeys(array $perTabel): void
    {
        foreach ($perTabel as $tabel => $constraints) {
            $ada = array_column($this->db->query(
                'SELECT CONSTRAINT_NAME AS nama FROM information_schema.TABLE_CONSTRAINTS
                 WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_TYPE = \'FOREIGN KEY\'',
                [$tabel],
            )->getResultArray(), 'nama');

            foreach (array_intersect($constraints, $ada) as $constraint) {
                $this->db->query("ALTER TABLE `{$tabel}` DROP FOREIGN KEY `{$constraint}`");
            }
        }
    }

    private function renameIndexes(string $tabel, array $peta): void
    {
        $ada = array_column($this->db->query("SHOW INDEX FROM `{$tabel}`")->getResultArray(), 'Key_name');

        foreach ($peta as $lama => $baru) {
            if (in_array($lama, $ada, true) && ! in_array($baru, $ada, true)) {
                $this->db->query("ALTER TABLE `{$tabel}` RENAME INDEX `{$lama}` TO `{$baru}`");
            }
        }
    }
}
