<?php?>
<?= $this->extend('layouts/petugas') ?>
<?= $this->section('konten') ?>

<div class="halaman-judul"><h1 class="mb-0">Profil</h1></div>

<?= komponen('components/ringkasan_error') ?>

<form action="<?= url_to('petugas.profil') ?>" method="post" class="form" novalidate>
    <?= csrf_field() ?>
    <input type="hidden" name="_method" value="PUT">
    <dl class="rincian" style="margin-bottom: var(--space-5)">
        <dt>Username</dt><dd><?= esc($petugas->username) ?></dd>
        <dt>Jabatan</dt><dd><?= esc($petugas->jabatan()->label()) ?></dd>
    </dl>
    <?= komponen('components/field', ['nama' => 'nama', 'label' => 'Nama lengkap', 'nilai' => $petugas->nama, 'atribut' => ['maxlength' => 100]]) ?>
    <?= komponen('components/field', ['nama' => 'no_telepon', 'label' => 'Nomor telepon', 'tipe' => 'tel', 'kelas' => 'input--sedang', 'nilai' => $petugas->no_telepon]) ?>
    <button type="submit" class="tombol tombol--utama">Simpan profil</button>
</form>

<h2 style="margin-top: var(--space-7)">Keamanan</h2>
<p><a href="<?= url_to('petugas.password') ?>"><?= ikon('lock') ?> Ganti kata sandi</a></p>

<?= komponen('components/pilihan_tema') ?>

<?= $this->endSection() ?>
