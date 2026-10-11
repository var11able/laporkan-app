<?php

namespace App\Entities;

use App\Enums\StatusLaporan;
use App\Services\UploadService;
use CodeIgniter\Entity\Entity;

class Tanggapan extends Entity
{
    protected $dates = ['tgl_tanggapan'];
    protected $casts = [
        'id_tanggapan' => 'integer',
        'id_laporan'   => 'integer',
        'id_user'      => 'integer',
    ];

    public function status(): StatusLaporan
    {
        return StatusLaporan::from($this->attributes['status_tanggapan']);
    }

    public function fotoUrl(): ?string
    {
        return UploadService::url(UploadService::FOLDER_TANGGAPAN, $this->foto_tanggapan);
    }

    public function thumbUrl(): ?string
    {
        return UploadService::thumbUrl(UploadService::FOLDER_TANGGAPAN, $this->foto_tanggapan);
    }
}
