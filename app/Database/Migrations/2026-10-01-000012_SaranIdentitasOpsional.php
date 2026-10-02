<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SaranIdentitasOpsional extends Migration
{
    public function up(): void
    {
        $this->forge->modifyColumn('saran', [
            'nama'       => ['name' => 'nama', 'type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'no_telepon' => ['name' => 'no_telepon', 'type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'alamat'     => ['name' => 'alamat', 'type' => 'TEXT', 'null' => true],
        ]);
    }

    public function down(): void
    {
        $this->db->query("UPDATE `saran` SET `nama` = COALESCE(`nama`, ''), `no_telepon` = COALESCE(`no_telepon`, ''), `alamat` = COALESCE(`alamat`, '')");
        $this->forge->modifyColumn('saran', [
            'nama'       => ['name' => 'nama', 'type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'no_telepon' => ['name' => 'no_telepon', 'type' => 'VARCHAR', 'constraint' => 20, 'null' => false],
            'alamat'     => ['name' => 'alamat', 'type' => 'TEXT', 'null' => false],
        ]);
    }
}
