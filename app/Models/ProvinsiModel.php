<?php

namespace App\Models;

use CodeIgniter\Model;

class ProvinsiModel extends Model
{
    protected $table           = 'provinsi';
    protected $primaryKey      = 'id_provinsi';
    protected $returnType      = 'array';
    protected $allowedFields   = ['provinsi', 'kode'];
    protected $validationRules = [
        'id_provinsi' => 'permit_empty|is_natural_no_zero',
        'provinsi'    => 'required|max_length[100]|is_unique[provinsi.provinsi,id_provinsi,{id_provinsi}]',
    ];
    protected $validationMessages = [
        'provinsi' => ['is_unique' => 'Provinsi ini sudah ada.'],
    ];

    public function semuaDenganJumlah(): array
    {
        return $this->select('provinsi.*, COUNT(kabupaten_kota.id_kabupaten_kota) AS jumlah_kabupaten_kota')
            ->join('kabupaten_kota', 'kabupaten_kota.id_provinsi = provinsi.id_provinsi', 'left')
            ->groupBy('provinsi.id_provinsi')
            ->orderBy('provinsi.provinsi')
            ->findAll();
    }

    public function opsi(): array
    {
        return array_column($this->orderBy('provinsi')->findAll(), 'provinsi', 'id_provinsi');
    }
}
