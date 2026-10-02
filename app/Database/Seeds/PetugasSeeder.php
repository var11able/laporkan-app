<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use RuntimeException;

class PetugasSeeder extends Seeder
{
    public function run(): void
    {
        if ($this->db->table('user')->where('username', 'admin')->countAllResults() > 0) {
            return;
        }

        $password = (string) env('SEED_ADMIN_PASSWORD', '');

        if (strlen($password) < 8) {
            throw new RuntimeException('Isi SEED_ADMIN_PASSWORD (minimal 8 karakter) di .env sebelum menjalankan seeder.');
        }

        $this->db->table('user')->insert([
            'nama'       => 'Administrator',
            'username'   => 'admin',
            'password'   => password_hash($password, PASSWORD_DEFAULT),
            'no_telepon' => '-',
            'jabatan'    => 'administrator',
        ]);
    }
}
