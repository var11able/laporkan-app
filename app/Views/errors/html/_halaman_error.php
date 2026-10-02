<?phphelper(['url', 'tampilan']);
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
<div class="tata-letak">
    <header class="appbar">
        <div class="wadah appbar__baris">
            <a class="logo" href="<?= base_url() ?>">
                <span class="logo__tanda"><?= ikon('message') ?></span>
                <span class="logo__teks">LaporKan<small>Lapor Korupsi</small></span>
            </a>
        </div>
    </header>
    <main id="konten" class="halaman">
        <div class="wadah wadah--sempit">
            <div class="kosong__ikon" style="margin:0 0 var(--space-4)"><?= ikon('help') ?></div>
            <p class="teks-muted tebal mb-0">Kode <?= esc($kode) ?></p>
            <h1><?= esc($judul) ?></h1>
            <?= $isi ?>
            <div class="tombol-grup">
                <a class="tombol tombol--utama" href="<?= base_url() ?>"><?= ikon('home') ?> Ke beranda</a>
                <a class="tombol" href="<?= base_url('lacak') ?>"><?= ikon('search') ?> Lacak laporan</a>
            </div>
        </div>
    </main>
</div>
</body>
</html>
