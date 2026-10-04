<?php
$baru = $provinsi === null;
?>
<?= $this->extend('layouts/petugas') ?>
<?= $this->section('konten') ?>

<div class="halaman-judul">
    <a class="kembali" href="<?= url_to('petugas.provinsi') ?>"><?= ikon('chevron-left') ?> Provinsi</a>
    <h1 style="font-size:1.75rem"><?= esc($judul) ?></h1>
</div>

<?= komponen('components/ringkasan_error') ?>

<form action="<?= $baru ? site_url('petugas/provinsi') : site_url('petugas/provinsi/' . $provinsi['id_provinsi']) ?>" method="post" class="form" novalidate>
    <?= csrf_field() ?>
    <?php if (! $baru): ?><input type="hidden" name="_method" value="PUT"><?php endif ?>
    <?= komponen('components/field', ['nama' => 'provinsi', 'label' => 'Nama provinsi', 'kelas' => 'input--sedang', 'nilai' => $provinsi['provinsi'] ?? '', 'atribut' => ['maxlength' => 100]]) ?>
    <div class="tombol-grup">
        <button type="submit" class="tombol tombol--utama">Simpan</button>
        <a class="tombol" href="<?= url_to('petugas.provinsi') ?>">Batal</a>
    </div>
</form>

<?= $this->endSection() ?>
