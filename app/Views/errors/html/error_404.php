<?php
$isi = '<p>Periksa kembali alamat yang Anda ketik. Jika Anda mengikuti tautan dari tempat lain, halaman itu mungkin sudah dipindahkan atau dihapus.</p>';

if (ENVIRONMENT !== 'production' && isset($message) && $message !== '') {
    $isi .= '<p class="teks-muted teks-kecil">' . esc($message) . '</p>';
}

echo view('errors/html/_halaman_error', ['judul' => 'Halaman tidak ditemukan', 'isi' => $isi, 'kode' => '404']);
