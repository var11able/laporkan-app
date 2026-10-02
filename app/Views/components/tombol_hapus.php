<?php?>
<form action="<?= esc($aksi, 'attr') ?>" method="post" data-konfirmasi="<?= esc($konfirmasi, 'attr') ?>" style="display:inline">
    <?= csrf_field() ?>
    <input type="hidden" name="_method" value="DELETE">
    <button type="submit" class="tombol tombol--bahaya<?= ($kecil ?? true) ? ' tombol--kecil' : '' ?>">
        <?= ikon('trash') ?> <?= esc($label ?? 'Hapus') ?>
    </button>
</form>
