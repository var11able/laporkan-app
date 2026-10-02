<?php

namespace App\Controllers\Auth;

trait MembatasiPercobaan
{
    private const PESAN_GAGAL          = 'Username atau kata sandi salah.';
    private const PESAN_TERLALU_BANYAK = 'Terlalu banyak percobaan masuk. Tunggu 1 menit, lalu coba lagi.';
    private const PERCOBAAN_PER_MENIT  = 5;

    private function bolehMencoba(string $jenis, string $username): bool
    {
        $kunci = 'login-' . $jenis . '-' . md5($this->request->getIPAddress() . '|' . mb_strtolower(trim($username)));

        return service('throttler')->check($kunci, self::PERCOBAAN_PER_MENIT, MINUTE);
    }

    private function tujuanSetelahMasuk(string $bawaan): string
    {
        $tujuan = session('kembali_ke');
        session()->remove('kembali_ke');

        return is_string($tujuan) && str_starts_with($tujuan, base_url()) ? $tujuan : $bawaan;
    }
}
