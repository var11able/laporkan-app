<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBaselineTables extends Migration
{
    private const ATTRIBUTES = ['ENGINE' => 'InnoDB', 'DEFAULT CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_general_ci'];

    public function up(): void
    {
        $this->forge->addField([
            'id_user'    => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'nama'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'username'   => ['type' => 'VARCHAR', 'constraint' => 100],
            'password'   => ['type' => 'VARCHAR', 'constraint' => 255],
            'no_telepon' => ['type' => 'VARCHAR', 'constraint' => 20],
            'jabatan'    => ['type' => 'ENUM', 'constraint' => ['administrator', 'operator']],
        ]);
        $this->forge->addPrimaryKey('id_user');
        $this->forge->createTable('user', true, self::ATTRIBUTES);

        $this->forge->addField([
            'id_masyarakat' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'nama'          => ['type' => 'VARCHAR', 'constraint' => 100],
            'username'      => ['type' => 'VARCHAR', 'constraint' => 100],
            'password'      => ['type' => 'VARCHAR', 'constraint' => 255],
            'no_telepon'    => ['type' => 'VARCHAR', 'constraint' => 20],
            'alamat'        => ['type' => 'TEXT'],
        ]);
        $this->forge->addPrimaryKey('id_masyarakat');
        $this->forge->createTable('masyarakat', true, self::ATTRIBUTES);

        $this->forge->addField([
            'id_pengaduan'     => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'isi_laporan'      => ['type' => 'TEXT'],
            'tgl_pengaduan'    => ['type' => 'DATETIME'],
            'foto'             => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true, 'default' => 'default.png'],
            'status_pengaduan' => [
                'type'       => 'ENUM',
                'constraint' => ['belum_ditanggapi', 'proses', 'valid', 'pengerjaan', 'selesai', 'tidak_valid'],
                'default'    => 'belum_ditanggapi',
            ],
            'id_masyarakat' => ['type' => 'INT', 'constraint' => 11],
            'id_kelurahan'  => ['type' => 'INT', 'constraint' => 11],
        ]);
        $this->forge->addPrimaryKey('id_pengaduan');
        $this->forge->addKey('id_masyarakat', false, false, 'id_masyarakat');
        $this->forge->addKey('id_kelurahan', false, false, 'id_kelurahan');
        $this->forge->createTable('pengaduan', true, self::ATTRIBUTES);

        $this->forge->addField([
            'id_tanggapan'     => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'isi_tanggapan'    => ['type' => 'TEXT'],
            'tgl_tanggapan'    => ['type' => 'DATETIME'],
            'status_tanggapan' => ['type' => 'ENUM', 'constraint' => ['proses', 'valid', 'pengerjaan', 'selesai', 'tidak_valid']],
            'foto_tanggapan'   => ['type' => 'VARCHAR', 'constraint' => 500, 'default' => 'default.png'],
            'id_pengaduan'     => ['type' => 'INT', 'constraint' => 11],
            'id_user'          => ['type' => 'INT', 'constraint' => 11],
        ]);
        $this->forge->addPrimaryKey('id_tanggapan');
        $this->forge->addKey('id_user', false, false, 'id_user');
        $this->forge->addKey('id_pengaduan', false, false, 'id_pengaduan');
        $this->forge->createTable('tanggapan', true, self::ATTRIBUTES);

        $this->forge->addField([
            'id_saran'   => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'nama'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'no_telepon' => ['type' => 'VARCHAR', 'constraint' => 20],
            'alamat'     => ['type' => 'TEXT'],
            'saran'      => ['type' => 'TEXT'],
            'tgl_saran'  => ['type' => 'DATETIME'],
        ]);
        $this->forge->addPrimaryKey('id_saran');
        $this->forge->createTable('saran', true, self::ATTRIBUTES);

        $this->forge->addField([
            'id_log'  => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'isi_log' => ['type' => 'TEXT'],
            'tgl_log' => ['type' => 'DATETIME'],
            'id_user' => ['type' => 'INT', 'constraint' => 11],
        ]);
        $this->forge->addPrimaryKey('id_log');
        $this->forge->addKey('id_user', false, false, 'id_user');
        $this->forge->createTable('log', true, self::ATTRIBUTES);
    }

    public function down(): void
    {
    }
}
