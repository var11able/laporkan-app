<!doctype html>
<html lang="id">
<head>
    <?= komponen('layouts/_head', ['judul' => $judul ?? null]) ?>
</head>
<body>
<a class="skip-link" href="#konten">Langsung ke konten utama</a>
<?= komponen('components/pita_demo') ?>
<header class="alur-bar">
    <div class="wadah wadah--sempit alur-bar__baris">
        <a class="tombol tombol--kaca tombol--kecil" href="<?= esc($kembali, 'attr') ?>" aria-label="Kembali ke langkah sebelumnya"><?= ikon('chevron-left') ?><span class="alur-bar__teks">Kembali</span></a>
        <span class="alur-bar__judul">Buat Laporan</span>
        <a class="tombol tombol--kaca tombol--kecil" href="<?= url_to('warga.beranda') ?>" aria-label="Tutup. Isian Anda tetap tersimpan."><span class="alur-bar__teks">Simpan &amp; keluar</span><?= ikon('x') ?></a>
    </div>
    <div class="wadah wadah--sempit">
        <div class="alur-bar__progres" role="progressbar" aria-label="Kemajuan" aria-valuemin="1" aria-valuemax="<?= $total ?>" aria-valuenow="<?= $nomor ?>">
            <span style="width: <?= round($nomor / $total * 100) ?>%"></span>
        </div>
    </div>
</header>
<main id="konten" tabindex="-1" class="halaman halaman--alur">
    <div class="wadah wadah--sempit">
        <p class="langkah-label">Langkah <?= $nomor ?> dari <?= $total ?></p>
        <?= komponen('components/flash') ?>
        <?= $this->renderSection('konten') ?>
    </div>
</main>
<?= komponen('components/dialog_konfirmasi') ?>
</body>
</html>
