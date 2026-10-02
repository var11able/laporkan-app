<?php

namespace App\Models;

use App\Entities\Tanggapan;
use CodeIgniter\Model;

class TanggapanModel extends Model
{
    protected $table           = 'tanggapan';
    protected $primaryKey      = 'id_tanggapan';
    protected $returnType      = Tanggapan::class;
    protected $useTimestamps   = true;
    protected $dateFormat      = 'datetime';
    protected $createdField    = 'tgl_tanggapan';
    protected $updatedField    = '';
    protected $allowedFields   = ['isi_tanggapan', 'tgl_tanggapan', 'status_tanggapan', 'instansi_tujuan', 'foto_tanggapan', 'id_pengaduan', 'id_user'];
    protected $validationRules = [
        'isi_tanggapan'    => 'required|max_length[5000]',
        'status_tanggapan' => 'required|in_list[proses,valid,pengerjaan,selesai,tidak_valid]',
        'id_pengaduan'     => 'required|is_natural_no_zero',
        'id_user'          => 'required|is_natural_no_zero',
    ];

    public function untukPengaduan(int $idPengaduan): array
    {
        return $this->select('tanggapan.*, user.nama AS nama_petugas, user.jabatan AS jabatan_petugas')
            ->join('user', 'user.id_user = tanggapan.id_user')
            ->where('tanggapan.id_pengaduan', $idPengaduan)
            ->orderBy('tanggapan.id_tanggapan', 'ASC')
            ->findAll();
    }

    public function terakhirUntuk(int $idPengaduan): ?Tanggapan
    {
        return $this->where('id_pengaduan', $idPengaduan)->orderBy('id_tanggapan', 'DESC')->first();
    }
}
