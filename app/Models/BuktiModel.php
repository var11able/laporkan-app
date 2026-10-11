<?php

namespace App\Models;

use CodeIgniter\Model;

class BuktiModel extends Model
{
    protected $table         = 'bukti_laporan';
    protected $primaryKey    = 'id_bukti';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $updatedField  = '';
    protected $allowedFields = ['id_laporan', 'nama_file', 'nama_asli', 'mime', 'ukuran'];

    public function untukLaporan(int $idLaporan): array
    {
        return $this->where('id_laporan', $idLaporan)->orderBy('id_bukti')->findAll();
    }

    public function jumlahUntuk(int $idLaporan): int
    {
        return $this->where('id_laporan', $idLaporan)->countAllResults();
    }
}
