<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\KabupatenKotaModel;
use CodeIgniter\HTTP\ResponseInterface;

class Wilayah extends BaseController
{
    public function kabupatenKota(int $idProvinsi): ResponseInterface
    {
        $data = array_map(
            static fn (array $k) => ['id' => (int) $k['id_kabupaten_kota'], 'nama' => $k['kabupaten_kota']],
            (new KabupatenKotaModel())->untukProvinsi($idProvinsi),
        );

        return $this->response
            ->setHeader('Cache-Control', 'public, max-age=3600')
            ->setJSON($data);
    }
}
