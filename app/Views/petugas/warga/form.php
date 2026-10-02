<?php$baru = $akun === null;
?>
<?= $this->extend('layouts/petugas') ?>
<?= $this->section('konten') ?>

<div class="halaman-judul">
    <a class="kembali" href="<?= url_to('petugas.warga') ?>"><?= ikon('chevron-left') ?> Warga</a>
    <h1 style="font-size:1.75rem"><?= esc($judul) ?></h1>
</div>

<?= komponen('components/ringkasan_error') ?>

<form action="<?= $baru ? site_url('petugas/warga') : site_url('petugas/warga/' . $akun->id_masyarakat) ?>" method="post" class="form" novalidate autocomplete="off">
    <?= csrf_field() ?>
    <?php if (! $baru): ?><input type="hidden" name="_method" value="PUT"><?php endif ?>
    <?= komponen('components/field', ['nama' => 'nama', 'label' => 'Nama lengkap', 'nilai' => $akun?->nama ?? '', 'atribut' => ['maxlength' => 100]]) ?>
    <?php if ($baru): ?>
        <?= komponen('components/field', ['nama' => 'username', 'label' => 'Username', 'kelas' => 'input--sedang', 'hint' => 'Huruf, angka, titik, garis bawah, atau tanda hubung.', 'atribut' => ['maxlength' => 100, 'autocapitalize' => 'none']]) ?>
    <?php else: ?>
        <div class="field"><span class="label">Username</span><p class="mb-0"><?= esc($akun->username) ?></p></div>
    <?php endif ?>
    <?= komponen('components/field', ['nama' => 'no_telepon', 'label' => 'Nomor telepon', 'tipe' => 'tel', 'kelas' => 'input--sedang', 'nilai' => $akun?->no_telepon ?? '']) ?>
    <?= komponen('components/field', ['nama' => 'alamat', 'label' => 'Alamat', 'tipe' => 'textarea', 'nilai' => $akun?->alamat ?? '', 'atribut' => ['rows' => 3, 'maxlength' => 500]]) ?>
    <?= komponen('components/field', [
        'nama' => 'password', 'label' => $baru ? 'Kata sandi' : 'Kata sandi baru (tidak wajib)', 'tipe' => 'password', 'kelas' => 'input--sedang',
        'hint' => $baru ? 'Minimal 8 karakter. Sampaikan ke pemilik akun secara langsung.' : 'Isi hanya jika pemilik akun lupa kata sandinya. Minimal 8 karakter.',
        'atribut' => ['autocomplete' => 'new-password'],
    ]) ?>
    <div class="tombol-grup">
        <button type="submit" class="tombol tombol--utama">Simpan</button>
        <a class="tombol" href="<?= url_to('petugas.warga') ?>">Batal</a>
    </div>
</form>

<?= $this->endSection() ?>
