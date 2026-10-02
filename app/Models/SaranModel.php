<?php

namespace App\Models;

use CodeIgniter\Model;

class SaranModel extends Model
{
    protected $table           = 'saran';
    protected $primaryKey      = 'id_saran';
    protected $returnType      = 'array';
    protected $useTimestamps   = true;
    protected $dateFormat      = 'datetime';
    protected $createdField    = 'tgl_saran';
    protected $updatedField    = '';
    protected $allowedFields   = ['nama', 'no_telepon', 'alamat', 'saran'];
    protected $validationRules = [
        'nama'       => 'permit_empty|max_length[100]',
        'no_telepon' => 'permit_empty|max_length[20]',
        'alamat'     => 'permit_empty|max_length[500]',
        'saran'      => 'required|max_length[5000]',
    ];
}
