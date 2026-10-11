<?php
$url ??= null;
$akhiran ??= null;
$nada ??= null;
$nilaiAngka ??= null;
?>
<div class="kartu-angka<?= $nada !== null ? ' kartu-angka--' . esc($nada, 'attr') : '' ?><?= $url !== null ? ' kartu-angka--tautan' : '' ?>">
    <dt class="kartu-angka__kepala">
        <?php if ($url !== null): ?>
            <a class="kartu-angka__label" href="<?= esc($url, 'attr') ?>"><?= esc($label) ?></a>
        <?php else: ?>
            <span class="kartu-angka__label"><?= esc($label) ?></span>
        <?php endif ?>
        <?= ikon($ikon, 'kartu-angka__ikon') ?>
    </dt>
    <dd class="kartu-angka__nilai"><span<?= $nilaiAngka !== null ? ' data-hitung-naik="' . (int) $nilaiAngka . '"' : '' ?>><?= esc((string) $nilai) ?></span><?php if ($akhiran !== null): ?><span class="kartu-angka__akhiran"><?= esc($akhiran) ?></span><?php endif ?></dd>
</div>
