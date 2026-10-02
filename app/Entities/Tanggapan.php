<?php

namespace App\Entities;

use App\Enums\StatusPengaduan;
use App\Services\UploadService;
use CodeIgniter\Entity\Entity;
use CodeIgniter\I18n\Time;

class Tanggapan extends Entity
{
    protected $dates = ['tgl_tanggapan'];
    protected $casts = [
        'id_tanggapan' => 'integer',
        'id_pengaduan' => 'integer',
        'id_user'      => 'integer',
    ];

    public function status(): StatusPengaduan
    {
        return StatusPengaduan::from($this->attributes['status_tanggapan']);
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
