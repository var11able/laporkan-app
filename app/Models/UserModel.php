<?php

namespace App\Models;

use App\Entities\User;
use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table           = 'user';
    protected $primaryKey      = 'id_user';
    protected $returnType      = User::class;
    protected $allowedFields   = ['nama', 'username', 'password', 'no_telepon', 'jabatan'];
    protected $validationRules = [
        'id_user'    => 'permit_empty|is_natural_no_zero',
        'nama'       => 'required|max_length[100]',
        'username'   => 'required|regex_match[/^[A-Za-z0-9._-]+$/]|min_length[3]|max_length[100]|is_unique[user.username,id_user,{id_user}]',
        'no_telepon' => 'required|max_length[20]',
        'jabatan'    => 'required|in_list[administrator,operator]',
    ];
    protected $validationMessages = [
        'username' => [
            'is_unique'   => 'Username ini sudah dipakai. Pilih username lain.',
            'regex_match' => 'Username hanya boleh berisi huruf, angka, titik, garis bawah, dan tanda hubung.',
        ],
    ];
    protected $beforeInsert = ['rapikanNama'];
    protected $beforeUpdate = ['rapikanNama'];

    public function cariUsername(string $username): ?User
    {
        return $this->where('username', $username)->first();
    }

    public function jumlahAdmin(): int
    {
        return $this->where('jabatan', 'administrator')->countAllResults();
    }

    protected function rapikanNama(array $event): array
    {
        if (isset($event['data']['nama'])) {
            $event['data']['nama'] = mb_convert_case(trim((string) $event['data']['nama']), MB_CASE_TITLE);
        }

        return $event;
    }
}
