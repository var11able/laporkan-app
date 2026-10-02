<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWilayahNasional extends Migration
{
    private const ATTRIBUTES = ['ENGINE' => 'InnoDB', 'DEFAULT CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_general_ci'];

    public function up(): void
    {
        $this->forge->addField([
            'id_provinsi' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'kode'        => ['type' => 'VARCHAR', 'constraint' => 2, 'null' => true],
            'provinsi'    => ['type' => 'VARCHAR', 'constraint' => 100],
        ]);
        $this->forge->addPrimaryKey('id_provinsi');
        $this->forge->addUniqueKey('provinsi', 'uq_provinsi');
        $this->forge->addUniqueKey('kode', 'uq_provinsi_kode');
        $this->forge->createTable('provinsi', true, self::ATTRIBUTES);

        $this->forge->addField([
            'id_kabupaten_kota' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'kode'              => ['type' => 'VARCHAR', 'constraint' => 5, 'null' => true],
            'kabupaten_kota'    => ['type' => 'VARCHAR', 'constraint' => 100],
            'id_provinsi'       => ['type' => 'INT', 'constraint' => 11],
        ]);
        $this->forge->addPrimaryKey('id_kabupaten_kota');
        $this->forge->addUniqueKey(['id_provinsi', 'kabupaten_kota'], 'uq_kabupaten_kota_per_provinsi');
        $this->forge->addUniqueKey('kode', 'uq_kabupaten_kota_kode');
        $this->forge->addForeignKey('id_provinsi', 'provinsi', 'id_provinsi', 'CASCADE', 'RESTRICT', 'fk_kabupaten_kota_provinsi');
        $this->forge->createTable('kabupaten_kota', true, self::ATTRIBUTES);

        $this->forge->addColumn('pengaduan', [
            'id_kabupaten_kota' => ['type' => 'INT', 'constraint' => 11, 'null' => true, 'after' => 'id_kelurahan'],
        ]);
        $this->db->query('ALTER TABLE `pengaduan` ADD CONSTRAINT `fk_pengaduan_kabupaten_kota`
            FOREIGN KEY (`id_kabupaten_kota`) REFERENCES `kabupaten_kota` (`id_kabupaten_kota`) ON UPDATE CASCADE ON DELETE SET NULL');
    }

    public function down(): void
    {
        $this->forge->dropForeignKey('pengaduan', 'fk_pengaduan_kabupaten_kota');
        $this->forge->dropColumn('pengaduan', 'id_kabupaten_kota');
        $this->forge->dropTable('kabupaten_kota', true);
        $this->forge->dropTable('provinsi', true);
    }
}
