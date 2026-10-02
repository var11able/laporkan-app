<?php?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= esc(isset($judul) ? $judul . ' · LaporKan' : 'LaporKan — Pelaporan Kasus Korupsi di Indonesia') ?></title>
<meta name="description" content="<?= esc($deskripsi ?? 'Laporkan dugaan korupsi di Indonesia dengan identitas terlindungi, lalu pantau tindak lanjutnya.') ?>">
<meta name="theme-color" content="#0B6E4F">
<link rel="icon" href="<?= base_url('favicon.ico') ?>">
<link rel="preload" href="<?= base_url('assets/fonts/public-sans-latin.woff2') ?>" as="font" type="font/woff2" crossorigin>
<link rel="stylesheet" href="<?= aset('css/app.css') ?>">
<link rel="stylesheet" href="<?= aset('css/print.css') ?>" media="print">
<script>
  try { const t = localStorage.getItem('tema'); if (t === 'light' || t === 'dark') document.documentElement.dataset.theme = t; } catch (e) {}
</script>
<script type="module" src="<?= aset('js/app.js') ?>"></script>
