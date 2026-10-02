<?php$foto ??= 'hero';
$sambutan ??= 'Satu laporan Anda membantu Indonesia bebas korupsi.';
?>
<!doctype html>
<html lang="id">
<head>
    <?= komponen('layouts/_head', ['judul' => $judul ?? null]) ?>
</head>
<body>
<a class="skip-link" href="#konten">Langsung ke konten utama</a>
<div class="auth">
    <aside class="auth__foto">
        <img src="<?= esc(foto_stok($foto, 900, 1200), 'attr') ?>" alt="" width="900" height="1200" fetchpriority="high">
        <p class="auth__sambutan"><?= esc($sambutan) ?></p>
    </aside>
    <main id="konten" tabindex="-1" class="auth__isi">
        <div class="auth__kolom">
            <a class="logo" href="<?= url_to('beranda') ?>">
                <span class="logo__tanda"><?= ikon('message') ?></span>
                <span class="logo__teks">LaporKan<small>Lapor Korupsi</small></span>
            </a>
            <?= komponen('components/flash') ?>
            <?= $this->renderSection('konten') ?>
        </div>
    </main>
</div>
<?= komponen('components/dialog_konfirmasi') ?>
</body>
</html>
