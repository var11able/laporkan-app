<?php
$maks = max(1, ...(array_column($baris, 'jumlah') ?: [1]));
?>
<section class="blok" aria-labelledby="<?= esc($id, 'attr') ?>">
    <h2 id="<?= esc($id, 'attr') ?>" class="blok__judul"><?= esc($judul) ?></h2>
    <?php if ($baris === []): ?>
        <p class="teks-muted">Belum ada laporan.</p>
    <?php else: ?>
        <ul class="batang-h">
            <?php foreach ($baris as $b): ?>
                <li>
                    <span><?= esc($b['label']) ?></span>
                    <span class="batang-h__trek" aria-hidden="true"><span class="batang-h__isi" style="width: <?= round($b['jumlah'] / $maks * 100) ?>%"></span></span>
                    <span class="tebal angka-tabular"><?= $b['jumlah'] ?></span>
                </li>
            <?php endforeach ?>
        </ul>
    <?php endif ?>
</section>
