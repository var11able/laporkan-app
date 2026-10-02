<?php

namespace App\Enums;

enum StatusPengaduan: string
{
    case Baru         = 'belum_ditanggapi';
    case Diverifikasi = 'proses';
    case Valid        = 'valid';
    case Diteruskan   = 'pengerjaan';
    case Selesai      = 'selesai';
    case TidakValid   = 'tidak_valid';

    public function labelWarga(): string
    {
        return match ($this) {
            self::Baru         => 'Laporan diterima',
            self::Diverifikasi => 'Sedang diverifikasi',
            self::Valid        => 'Laporan terverifikasi',
            self::Diteruskan   => 'Diteruskan ke instansi berwenang',
            self::Selesai      => 'Ditindaklanjuti',
            self::TidakValid   => 'Tidak dapat ditindaklanjuti',
        };
    }

    public function labelPetugas(): string
    {
        return match ($this) {
            self::Baru         => 'Baru',
            self::Diverifikasi => 'Diverifikasi',
            self::Valid        => 'Valid',
            self::Diteruskan   => 'Diteruskan',
            self::Selesai      => 'Selesai',
            self::TidakValid   => 'Tidak valid',
        };
    }

    public function ikon(): string
    {
        return match ($this) {
            self::Baru         => 'inbox',
            self::Diverifikasi => 'search',
            self::Valid        => 'badge-check',
            self::Diteruskan   => 'send',
            self::Selesai      => 'check-circle',
            self::TidakValid   => 'x-circle',
        };
    }

    public function labelTahap(): string
    {
        return match ($this) {
            self::Baru         => 'Diterima',
            self::Diverifikasi => 'Diverifikasi',
            self::Valid        => 'Terverifikasi',
            self::Diteruskan   => 'Diteruskan',
            self::Selesai      => 'Ditindaklanjuti',
            self::TidakValid   => 'Ditolak',
        };
    }

    public function penjelasan(): string
    {
        return match ($this) {
            self::Baru         => 'Laporan Anda sudah masuk dan menunggu diperiksa admin, biasanya dalam 1–3 hari kerja.',
            self::Diverifikasi => 'Admin sedang memeriksa laporan dan bukti Anda, dan dapat menghubungi Anda jika perlu keterangan tambahan.',
            self::Valid        => 'Laporan sudah diperiksa dan cukup lengkap. Admin sedang menyiapkannya untuk diteruskan.',
            self::Diteruskan   => 'Laporan sudah diteruskan ke instansi yang berwenang menyelidikinya.',
            self::Selesai      => 'Instansi berwenang sudah menindaklanjuti laporan ini. Terima kasih sudah melapor.',
            self::TidakValid   => 'Laporan ini tidak dapat ditindaklanjuti. Baca alasan dari admin di bawah.',
        };
    }

    public static function alur(): array
    {
        return [self::Baru, self::Diverifikasi, self::Valid, self::Diteruskan, self::Selesai];
    }

    public static function bisaPublik(): array
    {
        return [self::Valid, self::Diteruskan, self::Selesai];
    }

    public static function kelompokWarga(): array
    {
        return [
            'diterima' => [self::Baru],
            'diproses' => [self::Diverifikasi, self::Valid, self::Diteruskan],
            'selesai'  => [self::Selesai],
            'ditolak'  => [self::TidakValid],
        ];
    }

    public function slug(): string
    {
        return str_replace('_', '-', $this->value);
    }

    public function transisiBerikutnya(): array
    {
        return match ($this) {
            self::Baru                      => [self::Diverifikasi, self::TidakValid],
            self::Diverifikasi              => [self::Valid, self::TidakValid],
            self::Valid                     => [self::Diteruskan],
            self::Diteruskan                => [self::Selesai],
            self::Selesai, self::TidakValid => [],
        };
    }

    public function bisaBerubahKe(self $status): bool
    {
        return in_array($status, $this->transisiBerikutnya(), true);
    }

    public function bisaDiubahWarga(): bool
    {
        return $this === self::Baru;
    }

    public function selesaiDiproses(): bool
    {
        return $this === self::Selesai || $this === self::TidakValid;
    }

    public function bisaTampilPublik(): bool
    {
        return in_array($this, self::bisaPublik(), true);
    }

    public static function opsiPetugas(): array
    {
        $opsi = [];

        foreach (self::cases() as $case) {
            $opsi[$case->value] = $case->labelPetugas();
        }

        return $opsi;
    }
}
