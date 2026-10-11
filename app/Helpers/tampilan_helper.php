<?php

use App\Entities\LaporanKorupsi;
use App\Enums\StatusLaporan;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\I18n\Time;
use Config\FotoStok;

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
        $berkas = FotoStok::namaBerkas($kunci, $lebar, $tinggi);

        return is_file(FCPATH . 'assets/' . $berkas) ? aset($berkas) : config('FotoStok')->urlSumber($kunci, $lebar, $tinggi);
    }
}

if (! function_exists('bacaan')) {
    function bacaan(string $html): string
    {
        $kepala = '';

        if (preg_match('#^\s*(<h1[^>]*>.*?</h1>\s*(?:<p class="lead[^"]*">.*?</p>)?)#s', $html, $m) === 1) {
            $kepala = $m[1];
            $html   = substr($html, strlen($m[0]));
        }

        $daftar = [];
        $html   = (string) preg_replace_callback('#<h2([^>]*)>(.*?)</h2>#s', static function (array $m) use (&$daftar): string {
            $label = trim(strip_tags($m[2]));

            if (preg_match('#\bid="([^"]+)"#', $m[1], $ada) === 1) {
                $daftar[$ada[1]] = $label;

                return $m[0];
            }

            $id          = url_title($label, '-', true);
            $daftar[$id] = $label;

            return '<h2 id="' . esc($id, 'attr') . '"' . $m[1] . '>' . $m[2] . '</h2>';
        }, $html);

        $nav = '';

        if (count($daftar) > 1) {
            $nav = '<nav class="bacaan__daftar" aria-label="Daftar isi"><p class="bacaan__daftar-judul">Di halaman ini</p><ol>';

            foreach ($daftar as $id => $label) {
                $nav .= '<li><a href="#' . esc($id, 'attr') . '">' . esc(html_entity_decode($label)) . '</a></li>';
            }

            $nav .= '</ol></nav>';
        }

        return '<div class="bacaan"><header class="bacaan__kepala">' . $kepala . '</header>' . $nav . '<div class="bacaan__isi">' . $html . '</div></div>';
    }
}

if (! function_exists('foto_pengganti')) {
    function foto_pengganti(int $idLaporan, int $lebar = 480, ?int $tinggi = 360): string
    {
        $daftar = config('FotoStok')->pengganti;

        return foto_stok($daftar[$idLaporan % count($daftar)], $lebar, $tinggi);
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
    function status_badge(StatusLaporan $status, string $untuk = 'warga'): string
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
    function nama_pelapor(LaporanKorupsi $laporan, bool $terbuka = false): string
    {
        return $laporan->rahasia && ! $terbuka ? 'Dirahasiakan' : (string) $laporan->nama_pelapor;
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
