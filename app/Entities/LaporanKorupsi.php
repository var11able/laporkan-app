<?php

namespace App\Entities;

use App\Enums\StatusLaporan;
use App\Services\UploadService;
use CodeIgniter\Entity\Entity;
use CodeIgniter\I18n\Time;

class LaporanKorupsi extends Entity
{
    public const BATAS_HARI_TANGGAPAN = 3;

    protected $dates = ['tgl_laporan', 'updated_at'];
    protected $casts = [
        'id_laporan'         => 'integer',
        'id_warga'           => 'integer',
        'id_kabupaten_kota'  => '?integer',
        'id_provinsi'        => '?integer',
        'id_kategori'        => '?integer',
        'perkiraan_kerugian' => '?integer',
        'rahasia'            => 'boolean',
    ];

    public function status(): StatusLaporan
    {
        return StatusLaporan::from($this->attributes['status_laporan'] ?? StatusLaporan::Baru->value);
    }

    public function milik(int $idWarga): bool
    {
        return $this->id_warga === $idWarga;
    }

    public function tampilPublik(): bool
    {
        return $this->status()->bisaTampilPublik() && trim((string) ($this->attributes['ringkasan_publik'] ?? '')) !== '';
    }

    public function fotoUrl(): ?string
    {
        return UploadService::url(UploadService::FOLDER_LAPORAN, $this->foto);
    }

    public function thumbUrl(): ?string
    {
        return UploadService::thumbUrl(UploadService::FOLDER_LAPORAN, $this->foto);
    }

    public function lokasi(): ?string
    {
        $kabupaten = $this->attributes['kabupaten_kota'] ?? null;
        $provinsi  = $this->attributes['provinsi'] ?? null;

        if ($kabupaten === null) {
            return null;
        }

        return $kabupaten . ($provinsi !== null ? ', ' . $provinsi : '');
    }

    public function kerugianRupiah(): ?string
    {
        $nilai = $this->attributes['perkiraan_kerugian'] ?? null;

        return $nilai === null || $nilai === '' ? null : 'Rp ' . number_format((int) $nilai, 0, ',', '.');
    }

    public function umurHari(): int
    {
        $tgl = $this->tgl_laporan;

        return $tgl instanceof Time ? max(0, (int) $tgl->difference(Time::now())->getDays()) : 0;
    }

    public function melewatiBatas(): bool
    {
        return $this->status() === StatusLaporan::Baru && $this->umurHari() > self::BATAS_HARI_TANGGAPAN;
    }
}
