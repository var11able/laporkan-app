<?php
$baru = $akun === null;
?>
<?= $this->extend('layouts/petugas') ?>
<?= $this->section('konten') ?>

<div class="halaman-judul">
    <a class="kembali" href="<?= url_to('petugas.pengguna') ?>"><?= ikon('chevron-left') ?> Akun Petugas</a>
    <h1 style="font-size:1.75rem"><?= esc($judul) ?></h1>
</div>

<?= komponen('components/ringkasan_error') ?>

<form action="<?= $baru ? site_url('petugas/pengguna') : site_url('petugas/pengguna/' . $akun->id_user) ?>" method="post" class="form" novalidate autocomplete="off">
    <?= csrf_field() ?>
    <?php if (! $baru): ?><input type="hidden" name="_method" value="PUT"><?php endif ?>
    <?= komponen('components/field', ['nama' => 'nama', 'label' => 'Nama lengkap', 'nilai' => $akun?->nama ?? '', 'atribut' => ['maxlength' => 100]]) ?>
    <?php if ($baru): ?>
        <?= komponen('components/field', ['nama' => 'username', 'label' => 'Username', 'kelas' => 'input--sedang', 'atribut' => ['maxlength' => 100, 'autocapitalize' => 'none']]) ?>
    <?php else: ?>
        <div class="field"><span class="label">Username</span><p class="mb-0"><?= esc($akun->username) ?></p></div>
    <?php endif ?>
    <?= komponen('components/field', ['nama' => 'no_telepon', 'label' => 'Nomor telepon', 'tipe' => 'tel', 'kelas' => 'input--sedang', 'nilai' => $akun?->no_telepon ?? '']) ?>
    <?= komponen('components/field', ['nama' => 'jabatan', 'label' => 'Jabatan', 'tipe' => 'select', 'kelas' => 'input--sedang', 'opsi' => ['operator' => 'Operator', 'administrator' => 'Administrator'], 'nilai' => $akun?->jabatan ?? 'operator']) ?>
    <?= komponen('components/field', [
        'nama' => 'password', 'label' => $baru ? 'Kata sandi' : 'Kata sandi baru (tidak wajib)', 'tipe' => 'password', 'kelas' => 'input--sedang',
        'hint' => 'Minimal 8 karakter.', 'atribut' => ['autocomplete' => 'new-password'],
    ]) ?>
    <div class="tombol-grup">
        <button type="submit" class="tombol tombol--utama">Simpan</button>
        <a class="tombol" href="<?= url_to('petugas.pengguna') ?>">Batal</a>
    </div>
</form>

<?= $this->endSection() ?>
