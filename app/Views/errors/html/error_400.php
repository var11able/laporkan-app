<?php
echo view('errors/html/_halaman_error', [
    'judul' => 'Permintaan tidak dapat diproses',
    'isi'   => '<p>Halaman ini mungkin sudah terlalu lama terbuka. Muat ulang halaman sebelumnya, lalu coba lagi.</p>'
        . (ENVIRONMENT !== 'production' && isset($message) ? '<p class="teks-muted teks-kecil">' . esc($message) . '</p>' : ''),
    'kode'  => '400',
]);
