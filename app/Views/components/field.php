<?php
$tipe    ??= 'text';
$hint    ??= null;
$atribut ??= [];
$kelas   ??= '';
$nilai     = old($nama, $nilai ?? '');
$error     = galat($nama);
$deskripsi = trim(($hint !== null ? $nama . '-hint ' : '') . ($error !== null ? $nama . '-error' : ''));

$attr = '';

foreach ($atribut as $k => $v) {
    $attr .= $v === true ? ' ' . esc($k, 'attr') : ($v === false || $v === null ? '' : ' ' . esc($k, 'attr') . '="' . esc((string) $v, 'attr') . '"');
}
$umum = 'id="' . esc($nama, 'attr') . '" name="' . esc($nama, 'attr') . '"'
    . ($kelas !== '' ? ' class="' . esc($kelas, 'attr') . '"' : '')
    . ($deskripsi !== '' ? ' aria-describedby="' . esc($deskripsi, 'attr') . '"' : '')
    . ($error !== null ? ' aria-invalid="true"' : '') . $attr;
?>
<div class="field<?= $error !== null ? ' field--error' : '' ?>">
    <label for="<?= esc($nama, 'attr') ?>"><?= esc($label) ?></label>
    <?php if ($hint !== null): ?><span class="hint" id="<?= esc($nama, 'attr') ?>-hint"><?= esc($hint) ?></span><?php endif ?>
    <?php if ($error !== null): ?><span class="pesan-error" id="<?= esc($nama, 'attr') ?>-error"><?= ikon('alert') ?><span><span class="sr-only">Error:</span> <?= esc($error) ?></span></span><?php endif ?>
    <?php if ($tipe === 'textarea'): ?>
        <textarea <?= $umum ?>><?= esc($nilai) ?></textarea>
    <?php elseif ($tipe === 'select'): ?>
        <select <?= $umum ?>>
            <?php if (isset($kosong)): ?><option value=""><?= esc($kosong) ?></option><?php endif ?>
            <?php foreach ($opsi ?? [] as $v => $l): ?>
                <option value="<?= esc((string) $v, 'attr') ?>"<?= (string) $v === (string) $nilai ? ' selected' : '' ?>><?= esc($l) ?></option>
            <?php endforeach ?>
        </select>
    <?php elseif ($tipe === 'password'): ?>
        <div class="sandi<?= $kelas !== '' ? ' ' . esc($kelas, 'attr') : '' ?>">
            <input type="password" <?= $umum ?> value="">
            <button type="button" class="sandi__tombol" data-lihat-sandi="<?= esc($nama, 'attr') ?>" aria-pressed="false" aria-label="Tampilkan kata sandi" hidden><?= ikon('eye') ?></button>
        </div>
    <?php else: ?>
        <input type="<?= esc($tipe, 'attr') ?>" <?= $umum ?> value="<?= esc($nilai, 'attr') ?>">
    <?php endif ?>
</div>
