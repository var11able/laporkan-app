<?php

namespace App\Services;

use App\Models\LogModel;

class AuditLog
{
    public function __construct(private readonly LogModel $logModel = new LogModel())
    {
    }

    public function catat(string $pesan, ?int $idUser): void
    {
        $this->logModel->insert(['isi_log' => mb_substr($pesan, 0, 1000), 'id_user' => $idUser]);
    }
}
