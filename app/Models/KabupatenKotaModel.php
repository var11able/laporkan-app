<?php

namespace App\Models;

use CodeIgniter\Model;

class KabupatenKotaModel extends Model
{
    protected $table           = 'kabupaten_kota';
    protected $primaryKey      = 'id_kabupaten_kota';
    protected $returnType      = 'array';
    protected $allowedFields   = ['kabupaten_kota', 'id_provinsi', 'kode'];
    protected $validationRules = [
        'kabupaten_kota' => 'required|max_length[100]',
        'id_provinsi'    => 'required|is_natural_no_zero|is_not_unique[provinsi.id_provinsi]',
    ];

    public function semuaDenganProvinsi(?int $idProvinsi = null): array
    {
        $this->select('kabupaten_kota.*, provinsi.provinsi')
            ->join('provinsi', 'provinsi.id_provinsi = kabupaten_kota.id_provinsi')
            ->orderBy('provinsi.provinsi')
            ->orderBy('kabupaten_kota.kabupaten_kota');

        if ($idProvinsi !== null) {
            $this->where('kabupaten_kota.id_provinsi', $idProvinsi);
        }

        return $this->findAll();
    }

    public function cariDenganProvinsi(int $id): ?array
    {
        return $this->select('kabupaten_kota.*, provinsi.provinsi')
            ->join('provinsi', 'provinsi.id_provinsi = kabupaten_kota.id_provinsi')
            ->where('kabupaten_kota.id_kabupaten_kota', $id)
            ->first();
    }

    public function untukProvinsi(int $idProvinsi): array
    {
        return $this->select('id_kabupaten_kota, kabupaten_kota')
            ->where('id_provinsi', $idProvinsi)
            ->orderBy('kabupaten_kota')
            ->findAll();
    }

    public function opsiUntukProvinsi(int $idProvinsi): array
    {
        return array_column($this->untukProvinsi($idProvinsi), 'kabupaten_kota', 'id_kabupaten_kota');
    }

    public function adaDiProvinsi(string $nama, int $idProvinsi, ?int $kecualiId = null): bool
    {
        $builder = $this->where(['kabupaten_kota' => $nama, 'id_provinsi' => $idProvinsi]);

        if ($kecualiId !== null) {
            $builder->where('id_kabupaten_kota !=', $kecualiId);
        }

        return $builder->countAllResults() > 0;
    }
}
