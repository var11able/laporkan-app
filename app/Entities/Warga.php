<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Warga extends Entity
{
    protected $casts = ['id_warga' => 'integer'];

    public function getId(): int
    {
        return $this->id_warga;
    }
}
