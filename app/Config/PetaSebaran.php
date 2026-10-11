<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class PetaSebaran extends BaseConfig
{
    public int $ambang        = 5;
    public array $skala       = [1, 5, 20, 50, 100];
    public int $hariTerbaru   = 30;
    public array $rotasiAwal  = [-118, 38];
    public int $cacheDetik    = 600;
    public int $daftarTeratas = 16;
    public string $kunciCache = 'sebaran_publik';
}
