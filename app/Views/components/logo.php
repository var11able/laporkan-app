<?php
$url ??= url_to('beranda');
$sub ??= null;
?>
<a class="logo" href="<?= esc($url, 'attr') ?>" aria-label="LaporKan<?= $sub !== null ? ', ' . esc($sub, 'attr') : '' ?>, ke halaman utama">
    <span class="logo__teks">LaporKan<?php if ($sub !== null): ?><small><?= esc($sub) ?></small><?php endif ?></span>
</a>
