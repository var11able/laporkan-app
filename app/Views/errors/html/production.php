<?php

echo view('errors/html/_halaman_error', [
    'judul' => 'Maaf, terjadi masalah pada sistem',
    'isi'   => '<p>Kami sudah mencatat masalah ini. Coba lagi dalam beberapa menit. Jika Anda sedang mengirim laporan, isian Anda masih tersimpan di perangkat ini.</p>',
    'kode'  => '500',
]);
