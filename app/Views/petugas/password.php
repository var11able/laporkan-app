<?= $this->extend('layouts/petugas') ?>
<?= $this->section('konten') ?>

<div class="halaman-judul">
    <a class="kembali" href="<?= url_to('petugas.profil') ?>"><?= ikon('chevron-left') ?> Profil</a>
    <h1 style="font-size:1.75rem">Ganti kata sandi</h1>
</div>

<?= komponen('components/ringkasan_error') ?>

<form action="<?= url_to('petugas.password') ?>" method="post" class="form" novalidate>
    <?= csrf_field() ?>
    <input type="hidden" name="_method" value="PUT">
    <?= komponen('components/field', ['nama' => 'password_lama', 'label' => 'Kata sandi saat ini', 'tipe' => 'password', 'kelas' => 'input--sedang', 'atribut' => ['autocomplete' => 'current-password']]) ?>
    <?= komponen('components/field', ['nama' => 'password_baru', 'label' => 'Kata sandi baru', 'tipe' => 'password', 'kelas' => 'input--sedang', 'hint' => 'Minimal 8 karakter.', 'atribut' => ['autocomplete' => 'new-password']]) ?>
    <?= komponen('components/field', ['nama' => 'password_konfirmasi', 'label' => 'Ulangi kata sandi baru', 'tipe' => 'password', 'kelas' => 'input--sedang', 'atribut' => ['autocomplete' => 'new-password']]) ?>
    <button type="submit" class="tombol tombol--utama">Ganti kata sandi</button>
</form>

<?= $this->endSection() ?>
