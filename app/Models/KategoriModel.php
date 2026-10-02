<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriModel extends Model
{
    protected $table         = 'kategori';
    protected $primaryKey    = 'id_kategori';
    protected $returnType    = 'array';
    protected $allowedFields = ['kategori', 'keterangan', 'urutan'];

    public function semua(): array
    {
        return $this->orderBy('urutan')->orderBy('kategori')->findAll();
    }

    public function opsi(): array
    {
        return array_column($this->semua(), 'kategori', 'id_kategori');
    }
}
