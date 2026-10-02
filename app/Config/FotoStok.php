<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class FotoStok extends BaseConfig
{
    public array $foto = [
        'hero'         => '1555899434-94d1368aa7af',
        'kota'         => '1555899434-94d1368aa7af',
        'komunitas'    => '1517457373958-b7bdd4587205',
        'lapor'        => '1454165804606-c3d57bc86b40',
        'keadilan'     => '1589829545856-d10d557cf95f',
        'dokumen'      => '1450101499163-c8848c66ca85',
        'berkas'       => '1554224155-6726b3ff858f',
        'meja'         => '1512486130939-2c4f79935e4f',
        'tanda-tangan' => '1521791055366-0d553872125f',
    ];

    public array $pengganti = ['dokumen', 'berkas', 'meja', 'tanda-tangan'];
}
