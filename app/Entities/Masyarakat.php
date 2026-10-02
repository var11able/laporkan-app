<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Masyarakat extends Entity
{
    protected $casts = ['id_masyarakat' => 'integer'];

    public function getId(): int
    {
        return $this->id_masyarakat;
    }
}
