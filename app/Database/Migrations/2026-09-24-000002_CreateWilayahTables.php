<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWilayahTables extends Migration
{
    private const ATTRIBUTES = ['ENGINE' => 'InnoDB', 'DEFAULT CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_general_ci'];

    public function up(): void
    {
        $this->forge->addField([
            'id_kecamatan' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'kecamatan'    => ['type' => 'VARCHAR', 'constraint' => 100],
        ]);
        $this->forge->addPrimaryKey('id_kecamatan');
        $this->forge->addUniqueKey('kecamatan', 'uq_kecamatan');
        $this->forge->createTable('kecamatan', true, self::ATTRIBUTES);

        $this->forge->addField([
            'id_kelurahan' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'kelurahan'    => ['type' => 'VARCHAR', 'constraint' => 100],
            'id_kecamatan' => ['type' => 'INT', 'constraint' => 11],
        ]);
        $this->forge->addPrimaryKey('id_kelurahan');
        $this->forge->addUniqueKey(['id_kecamatan', 'kelurahan'], 'uq_kelurahan_per_kecamatan');
        $this->forge->addForeignKey('id_kecamatan', 'kecamatan', 'id_kecamatan', 'CASCADE', 'RESTRICT', 'fk_kelurahan_kecamatan');
        $this->forge->createTable('kelurahan', true, self::ATTRIBUTES);
    }

    public function down(): void
    {
        $this->forge->dropTable('kelurahan', true);
        $this->forge->dropTable('kecamatan', true);
    }
}
