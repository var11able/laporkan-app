<?php
helper(['url', 'tampilan']);
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title><?= esc($judul) ?> · LaporKan</title>
    <link rel="stylesheet" href="<?= aset('css/app.css') ?>">
</head>
<body>
<main id="konten" class="galat">
    <div class="galat__bingkai">
        <a class="logo logo--terang" href="<?= base_url() ?>">
            <span class="logo__teks">LaporKan</span>
        </a>
        <p class="galat__kode" aria-hidden="true"><?= esc($kode) ?></p>
        <p class="galat__label">Kode <?= esc($kode) ?></p>
        <h1><?= esc($judul) ?></h1>
        <div class="galat__isi"><?= $isi ?></div>
        <p class="galat__label">Situs demo portofolio · Semua data fiktif</p>
        <div class="tombol-grup">
            <a class="tombol tombol--terang" href="<?= base_url() ?>"><?= ikon('home') ?> Kembali ke Beranda</a>
            <a class="tombol tombol--kaca" href="<?= base_url('lacak') ?>"><?= ikon('search') ?> Lacak laporan</a>
        </div>
    </div>
</main>
</body>
</html>
