<?php

namespace App\Models;

use App\Entities\Masyarakat;
use CodeIgniter\Model;

class MasyarakatModel extends Model
{
    protected $table           = 'masyarakat';
    protected $primaryKey      = 'id_masyarakat';
    protected $returnType      = Masyarakat::class;
    protected $allowedFields   = ['nama', 'username', 'password', 'no_telepon', 'alamat'];
    protected $validationRules = [
        'id_masyarakat' => 'permit_empty|is_natural_no_zero',
        'nama'          => 'required|max_length[100]',
        'username'      => 'required|regex_match[/^[A-Za-z0-9._-]+$/]|min_length[3]|max_length[100]|is_unique[masyarakat.username,id_masyarakat,{id_masyarakat}]',
        'no_telepon'    => 'required|max_length[20]',
        'alamat'        => 'required',
    ];
    protected $validationMessages = [
        'username' => [
            'is_unique'   => 'Username ini sudah dipakai. Pilih username lain.',
            'regex_match' => 'Username hanya boleh berisi huruf, angka, titik, garis bawah, dan tanda hubung.',
        ],
    ];
    protected $beforeInsert = ['rapikanNama'];
    protected $beforeUpdate = ['rapikanNama'];

    public function cariUsername(string $username): ?Masyarakat
    {
        return $this->where('username', $username)->first();
    }

    protected function rapikanNama(array $event): array
    {
        if (isset($event['data']['nama'])) {
            $event['data']['nama'] = mb_convert_case(trim((string) $event['data']['nama']), MB_CASE_TITLE);
        }

        return $event;
    }
}
