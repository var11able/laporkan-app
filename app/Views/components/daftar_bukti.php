<?php
$hapus ??= null;
$pilihHapus ??= null;
?>
<ul class="daftar-bukti">
    <?php foreach ($bukti as $b): ?>
        <?php
        $asli   = (string) ($b['asli'] ?? $b['nama_asli']);
        $gambar = str_starts_with((string) $b['mime'], 'image/');
        $href   = $url($b);
        ?>
        <li>
            <a class="daftar-bukti__foto" href="<?= esc($href, 'attr') ?>" target="_blank" rel="noopener">
                <?php if ($gambar): ?>
                    <img src="<?= esc($href, 'attr') ?>" alt="" loading="lazy" width="64" height="64">
                <?php else: ?>
                    <?= ikon('file-text') ?>
                <?php endif ?>
            </a>
            <div class="daftar-bukti__isi">
                <a href="<?= esc($href, 'attr') ?>" target="_blank" rel="noopener" class="tebal"><?= esc($asli) ?></a>
                <span class="teks-kecil teks-muted"><?= $gambar ? 'Foto' : 'Dokumen PDF' ?> · <?= ukuran_berkas((int) $b['ukuran']) ?></span>
            </div>
            <?php if ($hapus !== null): ?>
                <form action="<?= esc($hapus, 'attr') ?>" method="post">
                    <?= csrf_field() ?>
                    <button type="submit" name="hapus_bukti" value="<?= esc((string) $b['nama'], 'attr') ?>" class="tombol tombol--teks tombol--kecil"><?= ikon('trash') ?> Hapus<span class="sr-only"> <?= esc($asli) ?></span></button>
                </form>
            <?php elseif ($pilihHapus !== null): ?>
                <label class="daftar-bukti__hapus"><input type="checkbox" name="<?= esc($pilihHapus, 'attr') ?>[]" value="<?= (int) $b['id_bukti'] ?>"> Hapus</label>
            <?php endif ?>
        </li>
    <?php endforeach ?>
</ul>
