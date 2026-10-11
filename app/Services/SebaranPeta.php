<?php

namespace App\Services;

use App\Enums\StatusLaporan;
use App\Models\LaporanModel;
use Config\PetaSebaran;

class SebaranPeta
{
    public function __construct(private readonly PetaSebaran $config = new PetaSebaran())
    {
    }

    public function publik(): array
    {
        return cache()->remember(
            $this->config->kunciCache,
            $this->config->cacheDetik,
            fn (): array => $this->bangun((new LaporanModel())->sebaranProvinsiPublik($this->sejak()), true, url_to('laporan.publik') . '?provinsi='),
        );
    }

    public function petugas(?StatusLaporan $status = null): array
    {
        return $this->bangun((new LaporanModel())->sebaranProvinsi($this->sejak(), $status), false, url_to('petugas.laporan') . '?provinsi=');
    }

    public function bangun(array $baris, bool $samarkan, string $tautan): array
    {
        $provinsi = array_map(function (array $b) use ($samarkan): array {
            $tersamar = $samarkan && $b['jumlah'] > 0 && $b['jumlah'] < $this->config->ambang;

            return [
                'id'     => $b['id'],
                'kode'   => $b['kode'],
                'nama'   => $b['nama'],
                'jumlah' => $tersamar ? null : $b['jumlah'],
                'label'  => $tersamar ? '<' . $this->config->ambang : number_format($b['jumlah'], 0, ',', '.'),
                'kelas'  => $tersamar ? 1 : $this->kelas($b['jumlah']),
                'baru'   => ! $tersamar && $b['baru'] > 0,
            ];
        }, $baris);

        $terisi = array_values(array_filter($provinsi, static fn (array $p): bool => $p['kelas'] > 0));

        return [
            'skala'      => $this->config->skala,
            'ambang'     => $this->config->ambang,
            'samar'      => $samarkan,
            'rotasi'     => $this->config->rotasiAwal,
            'tautan'     => $tautan,
            'provinsi'   => $provinsi,
            'terjangkau' => count($terisi),
            'total'      => count($provinsi),
            'ringkasan'  => $this->ringkasan($provinsi, count($terisi)),
        ];
    }

    private function kelas(int $jumlah): int
    {
        $kelas = 0;

        foreach ($this->config->skala as $i => $batas) {
            if ($jumlah >= $batas) {
                $kelas = $i + 1;
            }
        }

        return $kelas;
    }

    private function ringkasan(array $provinsi, int $terisi): string
    {
        $teratas = array_slice(array_filter($provinsi, static fn (array $p): bool => $p['jumlah'] !== null && $p['jumlah'] > 0), 0, 3);

        if ($teratas === []) {
            return 'Peta sebaran laporan di ' . count($provinsi) . ' provinsi. Belum ada laporan yang bisa ditampilkan.';
        }

        return sprintf(
            'Peta sebaran laporan di %d provinsi, %d di antaranya sudah memiliki laporan. Terbanyak: %s. Daftar lengkap ada di bawah peta.',
            count($provinsi),
            $terisi,
            implode(', ', array_map(static fn (array $p): string => $p['nama'] . ' (' . $p['label'] . ')', $teratas)),
        );
    }

    private function sejak(): string
    {
        return date('Y-m-d H:i:s', strtotime('-' . $this->config->hariTerbaru . ' days'));
    }
}
