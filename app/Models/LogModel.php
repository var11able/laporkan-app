<?php

namespace App\Models;

use CodeIgniter\Model;

class LogModel extends Model
{
    protected $table         = 'log';
    protected $primaryKey    = 'id_log';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'tgl_log';
    protected $updatedField  = '';
    protected $allowedFields = ['isi_log', 'id_user'];

    public function denganPetugas(): static
    {
        $this->select('log.*, user.nama, user.username')
            ->join('user', 'user.id_user = log.id_user', 'left')
            ->orderBy('log.tgl_log', 'DESC')
            ->orderBy('log.id_log', 'DESC');

        return $this;
    }
}
