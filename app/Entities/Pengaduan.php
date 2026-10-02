<?php

namespace App\Entities;

use App\Enums\StatusPengaduan;
use App\Services\UploadService;
use CodeIgniter\Entity\Entity;
use CodeIgniter\I18n\Time;

class Pengaduan extends Entity
{
    public const BATAS_HARI_TANGGAPAN = 3;

    protected $dates = ['tgl_pengaduan', 'updated_at'];
    protected $casts = [
        'id_pengaduan'       => 'integer',
        'id_masyarakat'      => 'integer',
        'id_kabupaten_kota'  => '?integer',
        'id_provinsi'        => '?integer',
        'id_kategori'        => '?integer',
        'perkiraan_kerugian' => '?integer',
        'rahasia'            => 'boolean',
    ];

    public function status(): StatusPengaduan
    {
        return StatusPengaduan::from($this->attributes['status_pengaduan'] ?? StatusPengaduan::Baru->value);
    }

    public function milik(int $idMasyarakat): bool
    {
        return $this->id_masyarakat === $idMasyarakat;
    }

    public function tampilPublik(): bool
    {
        return $this->status()->bisaTampilPublik() && trim((string) ($this->attributes['ringkasan_publik'] ?? '')) !== '';
    }

    public function fotoUrl(): ?string
    {
        return UploadService::url(UploadService::FOLDER_PENGADUAN, $this->foto);
    }

    public function thumbUrl(): ?string
    {
        return UploadService::thumbUrl(UploadService::FOLDER_PENGADUAN, $this->foto);
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
        $tgl = $this->tgl_pengaduan;

        return $tgl instanceof Time ? max(0, (int) $tgl->difference(Time::now())->getDays()) : 0;
    }

    public function melewatiBatas(): bool
    {
        return $this->status() === StatusPengaduan::Baru && $this->umurHari() > self::BATAS_HARI_TANGGAPAN;
    }
}
