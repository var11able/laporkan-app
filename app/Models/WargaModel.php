<?php

namespace App\Models;

use App\Entities\Warga;
use CodeIgniter\Model;

class WargaModel extends Model
{
    protected $table           = 'warga';
    protected $primaryKey      = 'id_warga';
    protected $returnType      = Warga::class;
    protected $allowedFields   = ['nama', 'username', 'password', 'no_telepon', 'alamat'];
    protected $validationRules = [
        'id_warga'   => 'permit_empty|is_natural_no_zero',
        'nama'       => 'required|max_length[100]',
        'username'   => 'required|regex_match[/^[A-Za-z0-9._-]+$/]|min_length[3]|max_length[100]|is_unique[warga.username,id_warga,{id_warga}]',
        'no_telepon' => 'required|max_length[20]',
        'alamat'     => 'required',
    ];
    protected $validationMessages = [
        'username' => [
            'is_unique'   => 'Username ini sudah dipakai. Pilih username lain.',
            'regex_match' => 'Username hanya boleh berisi huruf, angka, titik, garis bawah, dan tanda hubung.',
        ],
    ];
    protected $beforeInsert = ['rapikanNama'];
    protected $beforeUpdate = ['rapikanNama'];

    public function cariUsername(string $username): ?Warga
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
