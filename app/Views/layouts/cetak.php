<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($judul) ?> · LaporKan</title>
    <link rel="stylesheet" href="<?= aset('css/app.css') ?>">
    <link rel="stylesheet" href="<?= aset('css/print.css') ?>">
    <style>
        body { background: #fff; color: #000; }
        .lembar { max-width: 210mm; margin: 0 auto; padding: 24px 16px 48px; }
        @media print { .lembar { padding: 0; } }
    </style>
</head>
<body>
<div class="lembar">
    <div class="tombol-grup jangan-cetak" style="margin-bottom: 24px">
        <button type="button" class="tombol tombol--utama" onclick="window.print()"><?= ikon('printer') ?> Cetak</button>
        <a class="tombol" href="<?= esc($kembali ?? url_to('beranda'), 'attr') ?>">Kembali</a>
    </div>
    <header class="kop">
        <div class="kop__logo" aria-hidden="true">L</div>
        <div class="kop__teks">
            <strong>LAPORKAN</strong>
            <span>Website Pelaporan Kasus Korupsi di Indonesia</span>
            <span><?= esc(base_url()) ?></span>
        </div>
    </header>
    <?= $this->renderSection('konten') ?>
    <footer class="cetak-kaki">
        <span>Dicetak <?= tanggal(\CodeIgniter\I18n\Time::now()) ?></span>
        <span>LaporKan · Dokumen rahasia, jangan disebarluaskan</span>
    </footer>
</div>
</body>
</html>
