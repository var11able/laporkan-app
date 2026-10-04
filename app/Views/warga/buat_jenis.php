<?php
$ubah    = service('request')->getGet('ubah') !== null;
$dipilih = (string) old('id_kategori', (string) ($draf['id_kategori'] ?? ''));
$error   = galat('id_kategori');
?>
<?= $this->extend('layouts/alur') ?>
<?= $this->section('konten') ?>

<?= komponen('components/ringkasan_error') ?>

<form action="<?= url_to('warga.laporan.langkah', 'jenis') ?>" method="post" novalidate>
    <?= csrf_field() ?>
    <fieldset class="field<?= $error !== null ? ' field--error' : '' ?>" aria-describedby="jenis-hint<?= $error !== null ? ' id_kategori-error' : '' ?>">
        <legend><h1>Apa jenis dugaan korupsinya?</h1></legend>
        <p class="lead" id="jenis-hint">Pilih yang paling mendekati. Jika ragu, pilih "Lainnya". Admin akan memeriksanya lagi.</p>
        <?php if ($error !== null): ?><span class="pesan-error" id="id_kategori-error"><?= ikon('alert') ?><span><span class="sr-only">Error:</span> <?= esc($error) ?></span></span><?php endif ?>
        <div class="pilihan">
            <?php foreach ($kategori as $i => $k): ?>
                <div class="pilihan__item">
                    <input type="radio" id="<?= $i === 0 ? 'id_kategori' : 'kategori-' . $k['id_kategori'] ?>" name="id_kategori" value="<?= esc($k['id_kategori'], 'attr') ?>"<?= $dipilih === (string) $k['id_kategori'] ? ' checked' : '' ?>>
                    <label for="<?= $i === 0 ? 'id_kategori' : 'kategori-' . $k['id_kategori'] ?>">
                        <span><strong><?= esc($k['kategori']) ?></strong><?php if (! empty($k['keterangan'])): ?><br><span class="teks-kecil teks-muted"><?= esc($k['keterangan']) ?></span><?php endif ?></span>
                    </label>
                </div>
            <?php endforeach ?>
        </div>
    </fieldset>

    <?php if ($ubah): ?><input type="hidden" name="kembali_ke_periksa" value="1"><?php endif ?>
    <div class="alur-aksi">
        <button type="submit" class="tombol tombol--utama tombol--besar tombol--penuh-mobile">Lanjutkan</button>
    </div>
</form>

<?= $this->endSection() ?>
