<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ConvertToInnoDb extends Migration
{
    public function up(): void
    {
        $this->db->query('ALTER TABLE `tanggapan` ENGINE=InnoDB');
    }

    public function down(): void
    {
    }
}
