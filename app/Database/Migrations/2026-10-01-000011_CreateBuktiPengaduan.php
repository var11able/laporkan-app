<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBuktiPengaduan extends Migration
{
    private const ATTRIBUTES = ['ENGINE' => 'InnoDB', 'DEFAULT CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_general_ci'];

    public function up(): void
    {
        $this->forge->addField([
            'id_bukti'     => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'id_pengaduan' => ['type' => 'INT', 'constraint' => 11],
            'nama_file'    => ['type' => 'VARCHAR', 'constraint' => 100],
            'nama_asli'    => ['type' => 'VARCHAR', 'constraint' => 255],
            'mime'         => ['type' => 'VARCHAR', 'constraint' => 100],
            'ukuran'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id_bukti');
        $this->forge->addKey('id_pengaduan', false, false, 'idx_bukti_pengaduan');
        $this->forge->addForeignKey('id_pengaduan', 'pengaduan', 'id_pengaduan', 'CASCADE', 'CASCADE', 'fk_bukti_pengaduan');
        $this->forge->createTable('bukti_pengaduan', true, self::ATTRIBUTES);
    }

    public function down(): void
    {
        $this->forge->dropTable('bukti_pengaduan', true);
    }
}
