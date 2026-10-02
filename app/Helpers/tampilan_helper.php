<?php

use App\Entities\Pengaduan;
use App\Enums\StatusPengaduan;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\I18n\Time;

if (! function_exists('komponen')) {
    function komponen(string $nama, array $data = []): string
    {
        return single_service('renderer')->setData($data, 'raw')->render($nama, null, false);
    }
}

if (! function_exists('ikon')) {
    function ikon(string $id, string $kelas = ''): string
    {
        return '<svg class="ikon ' . esc($kelas, 'attr') . '" aria-hidden="true" focusable="false"><use href="'
            . esc(aset('icons.svg'), 'attr') . '#' . esc($id, 'attr') . '"></use></svg>';
    }
}

if (! function_exists('aset')) {
    function aset(string $path): string
    {
        $file  = FCPATH . 'assets/' . $path;
        $versi = is_file($file) ? (string) filemtime($file) : '1';

        return base_url('assets/' . $path) . '?v=' . $versi;
    }
}

if (! function_exists('foto_stok')) {
    function foto_stok(string $kunci, int $lebar = 800, ?int $tinggi = null): string
    {
        $id = config('FotoStok')->foto[$kunci] ?? config('FotoStok')->foto['kota'];

        return 'https://images.unsplash.com/photo-' . $id . '?auto=format&fit=crop&q=70&w=' . $lebar . ($tinggi !== null ? '&h=' . $tinggi : '');
    }
}

if (! function_exists('foto_pengganti')) {
    function foto_pengganti(int $idPengaduan, int $lebar = 480, ?int $tinggi = 360): string
    {
        $daftar = config('FotoStok')->pengganti;

        return foto_stok($daftar[$idPengaduan % count($daftar)], $lebar, $tinggi);
    }
}

if (! function_exists('tanggal')) {
    function tanggal(string|Time|null $waktu, bool $denganJam = true): string
    {
        if ($waktu === null || $waktu === '') {
            return '–';
        }

        $waktu = $waktu instanceof Time ? $waktu : Time::parse($waktu);

        return $waktu->toLocalizedString($denganJam ? 'd MMM yyyy, HH.mm' : 'd MMMM yyyy');
    }
}

if (! function_exists('tanggal_iso')) {
    function tanggal_iso(string|Time|null $waktu): string
    {
        if ($waktu === null || $waktu === '') {
            return '';
        }

        return ($waktu instanceof Time ? $waktu : Time::parse($waktu))->format('c');
    }
}

if (! function_exists('waktu_relatif')) {
    function waktu_relatif(string|Time|null $waktu): string
    {
        if ($waktu === null || $waktu === '') {
            return '–';
        }

        $waktu = $waktu instanceof Time ? $waktu : Time::parse($waktu);
        $detik = Time::now()->getTimestamp() - $waktu->getTimestamp();

        return match (true) {
            $detik < 60        => 'Baru saja',
            $detik < 3600      => intdiv($detik, 60) . ' menit lalu',
            $detik < 86400     => intdiv($detik, 3600) . ' jam lalu',
            $detik < 2 * 86400 => 'Kemarin',
            $detik < 7 * 86400 => intdiv($detik, 86400) . ' hari lalu',
            default            => tanggal($waktu, false),
        };
    }
}

if (! function_exists('tautan_wa')) {
    function tautan_wa(?string $telepon, string $pesan = ''): ?string
    {
        $angka = preg_replace('/\D+/', '', (string) $telepon) ?? '';

        if (str_starts_with($angka, '0')) {
            $angka = '62' . substr($angka, 1);
        }

        if (strlen($angka) < 9) {
            return null;
        }

        return 'https://wa.me/' . $angka . ($pesan !== '' ? '?text=' . rawurlencode($pesan) : '');
    }
}

if (! function_exists('status_badge')) {
    function status_badge(StatusPengaduan $status, string $untuk = 'warga'): string
    {
        $label = $untuk === 'petugas' ? $status->labelPetugas() : $status->labelWarga();

        return '<span class="status status--' . $status->slug() . '">' . ikon($status->ikon()) . esc($label) . '</span>';
    }
}

if (! function_exists('galat')) {
    function galat(string $field): ?string
    {
        $errors = session('_ci_validation_errors');

        return is_array($errors) && isset($errors[$field]) ? (string) $errors[$field] : null;
    }
}

if (! function_exists('semua_galat')) {
    function semua_galat(): array
    {
        $errors = session('_ci_validation_errors');

        return is_array($errors) ? $errors : [];
    }
}

if (! function_exists('kirim_berkas')) {
    function kirim_berkas(ResponseInterface $response, string $path, string $mime, string $namaAsli): ResponseInterface
    {
        $gambar = str_starts_with($mime, 'image/');
        $nama   = preg_replace('/[^\w.\- ]+/u', '_', $namaAsli) ?: 'bukti';

        $response->removeHeader('Cache-Control');

        return $response
            ->setHeader('Content-Type', $gambar ? $mime : 'application/octet-stream')
            ->setHeader('Content-Disposition', ($gambar ? 'inline' : 'attachment') . '; filename="' . $nama . '"')
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setHeader('Cache-Control', 'private, no-store')
            ->setBody((string) file_get_contents($path));
    }
}

if (! function_exists('ukuran_berkas')) {
    function ukuran_berkas(int $byte): string
    {
        return $byte < 1024 * 1024
            ? max(1, (int) round($byte / 1024)) . ' KB'
            : number_format($byte / 1024 / 1024, 1, ',', '.') . ' MB';
    }
}

if (! function_exists('nama_pelapor')) {
    function nama_pelapor(Pengaduan $pengaduan, bool $terbuka = false): string
    {
        return $pengaduan->rahasia && ! $terbuka ? 'Dirahasiakan' : (string) $pengaduan->nama_pelapor;
    }
}

if (! function_exists('umur_laporan')) {
    function umur_laporan(int $hari): string
    {
        return match (true) {
            $hari === 0 => '< 1 hari',
            $hari === 1 => '1 hari',
            default     => $hari . ' hari',
        };
    }
}

if (! function_exists('penggalan')) {
    function penggalan(string $teks, int $panjang = 80): string
    {
        return character_limiter(preg_replace('/\s+/', ' ', trim($teks)) ?? '', $panjang, '…');
    }
}
