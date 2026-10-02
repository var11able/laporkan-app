<?php$sukses = session()->getFlashdata('sukses');
$error  = session()->getFlashdata('error');
$info   = session()->getFlashdata('info');
?>
<?php if ($error !== null): ?>
    <div class="banner banner--error" role="alert">
        <?= ikon('alert') ?>
        <div class="banner__isi"><p><?= esc($error) ?></p></div>
    </div>
<?php endif ?>
<?php if ($info !== null): ?>
    <div class="banner" role="status">
        <?= ikon('info') ?>
        <div class="banner__isi"><p><?= esc($info) ?></p></div>
        <button type="button" class="tombol tombol--ikon banner__tutup" data-tutup-banner aria-label="Tutup pemberitahuan"><?= ikon('x') ?></button>
    </div>
<?php endif ?>
<?php if ($sukses !== null): ?>
    <div class="snackbar-wadah" role="status">
        <div class="snackbar">
            <?= ikon('check-circle') ?>
            <p><?= esc($sukses) ?></p>
            <button type="button" aria-label="Tutup">✕</button>
        </div>
    </div>
<?php endif ?>
