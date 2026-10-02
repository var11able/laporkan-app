<?= $this->extend('layouts/auth') ?>
<?= $this->section('konten') ?>
<h1>Masuk Admin</h1>
<p class="teks-muted">Khusus administrator dan operator LaporKan.</p>

<?= komponen('components/ringkasan_error') ?>
<form action="<?= url_to('petugas.masuk') ?>" method="post" novalidate>
    <?= csrf_field() ?>
    <?= komponen('components/field', ['nama' => 'username', 'label' => 'Username', 'atribut' => ['autocomplete' => 'username', 'autocapitalize' => 'none', 'spellcheck' => 'false', 'required' => true]]) ?>
    <?= komponen('components/field', ['nama' => 'password', 'label' => 'Kata sandi', 'tipe' => 'password', 'atribut' => ['autocomplete' => 'current-password', 'required' => true]]) ?>
    <button type="submit" class="tombol tombol--utama tombol--besar tombol--penuh" data-memuat="Memeriksa…"><?= ikon('lock') ?> Masuk</button>
</form>

<p class="auth__alt">Ingin melapor? <a href="<?= url_to('masuk') ?>">Masuk di sini</a>.</p>
<?= $this->endSection() ?>
