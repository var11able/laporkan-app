<?= $this->extend('layouts/auth') ?>
<?= $this->section('konten') ?>
<h1>Masuk</h1>
<p class="teks-muted">Untuk membuat laporan dan memantau perkembangannya.</p>

<?= komponen('components/ringkasan_error') ?>
<form action="<?= url_to('masuk') ?>" method="post" novalidate>
    <?= csrf_field() ?>
    <?= komponen('components/field', ['nama' => 'username', 'label' => 'Username', 'atribut' => ['autocomplete' => 'username', 'autocapitalize' => 'none', 'spellcheck' => 'false', 'required' => true]]) ?>
    <?= komponen('components/field', ['nama' => 'password', 'label' => 'Kata sandi', 'tipe' => 'password', 'atribut' => ['autocomplete' => 'current-password', 'required' => true]]) ?>
    <button type="submit" class="tombol tombol--utama tombol--besar tombol--penuh" data-memuat="Memeriksa…">Masuk</button>
</form>
<p class="teks-kecil teks-muted" style="margin: var(--space-4) 0 0">Lupa kata sandi? Kirim permintaan lewat <a href="<?= url_to('saran') ?>">Kritik &amp; Saran</a>.</p>

<p class="auth__alt">Belum punya akun? <a href="<?= url_to('daftar') ?>">Daftar akun baru</a></p>
<?= $this->endSection() ?>
