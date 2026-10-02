<?php

namespace App\Entities;

use App\Enums\Jabatan;
use CodeIgniter\Entity\Entity;

class User extends Entity
{
    protected $casts = ['id_user' => 'integer'];

    public function getId(): int
    {
        return $this->id_user;
    }

    public function jabatan(): Jabatan
    {
        return Jabatan::from($this->attributes['jabatan']);
    }

    public function isAdmin(): bool
    {
        return $this->jabatan() === Jabatan::Administrator;
    }
}
