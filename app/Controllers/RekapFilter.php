<?php

namespace App\Controllers;

use App\Enums\StatusPengaduan;

trait RekapFilter
{
    private function filterRekap(): array
    {
        $tanggal = static function (mixed $nilai, string $bawaan): string {
            $nilai = (string) $nilai;

            return preg_match('/^\d{4}-\d{2}-\d{2}$/', $nilai) === 1 && strtotime($nilai) !== false ? $nilai : $bawaan;
        };

        $status = (string) $this->request->getGet('status');

        return [
            'dari'     => $tanggal($this->request->getGet('dari'), date('Y-m-01')),
            'sampai'   => $tanggal($this->request->getGet('sampai'), date('Y-m-d')),
            'status'   => StatusPengaduan::tryFrom($status) !== null ? $status : '',
            'provinsi' => ctype_digit((string) $this->request->getGet('provinsi')) ? (string) $this->request->getGet('provinsi') : '',
            'kategori' => ctype_digit((string) $this->request->getGet('kategori')) ? (string) $this->request->getGet('kategori') : '',
        ];
    }
}
