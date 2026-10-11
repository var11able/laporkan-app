<?php
$foto ??= 'hero';
$varian ??= 'warga';
$sambutan ??= 'Satu laporan Anda membantu Indonesia bebas korupsi.';
?>
<!doctype html>
<html lang="id">
<head>
    <?= komponen('layouts/_head', ['judul' => $judul ?? null]) ?>
</head>
<body>
<a class="skip-link" href="#konten">Langsung ke konten utama</a>
<?= komponen('components/pita_demo') ?>
<div class="auth auth--<?= esc($varian, 'attr') ?>">
    <aside class="auth__foto">
        <?php if ($varian !== 'petugas'): ?>
            <img src="<?= esc(foto_stok($foto, 900, 1200), 'attr') ?>" alt="" width="900" height="1200" fetchpriority="high">
        <?php endif ?>
        <p class="auth__sambutan"><?= esc($sambutan) ?></p>
        <p class="auth__merek" aria-hidden="true">LaporKan</p>
    </aside>
    <main id="konten" tabindex="-1" class="auth__isi">
        <div class="auth__kolom">
            <div class="auth__kepala">
                <?= komponen('components/logo', ['sub' => $varian === 'petugas' ? 'Panel Admin' : null]) ?>
                <?= komponen('components/tombol_tema') ?>
            </div>
            <div class="auth__kartu">
                <?= komponen('components/flash') ?>
                <?= $this->renderSection('konten') ?>
            </div>
        </div>
    </main>
</div>
<?= komponen('components/dialog_konfirmasi') ?>
</body>
</html>
