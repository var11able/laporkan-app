<?php$errors = semua_galat();
?>
<?php if ($errors !== []): ?>
    <div class="ringkasan-error" role="alert" tabindex="-1" data-fokus>
        <h2>Ada yang perlu diperbaiki</h2>
        <ul>
            <?php foreach ($errors as $field => $pesan): ?>
                <li><a href="#<?= esc($field, 'attr') ?>"><?= esc($pesan) ?></a></li>
            <?php endforeach ?>
        </ul>
    </div>
<?php endif ?>
