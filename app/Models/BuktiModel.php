<?php

namespace App\Models;

use CodeIgniter\Model;

class BuktiModel extends Model
{
    protected $table         = 'bukti_pengaduan';
    protected $primaryKey    = 'id_bukti';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $updatedField  = '';
    protected $allowedFields = ['id_pengaduan', 'nama_file', 'nama_asli', 'mime', 'ukuran'];

    public function untukPengaduan(int $idPengaduan): array
    {
        return $this->where('id_pengaduan', $idPengaduan)->orderBy('id_bukti')->findAll();
    }

    public function jumlahUntuk(int $idPengaduan): int
    {
        return $this->where('id_pengaduan', $idPengaduan)->countAllResults();
    }
}
