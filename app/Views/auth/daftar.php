<?= $this->extend('layouts/auth') ?>
<?= $this->section('konten') ?>
<h1>Daftar Akun</h1>
<p class="teks-muted">Satu menit saja. Dengan akun, Anda bisa melapor dan memantau semua laporan Anda.</p>

<?= komponen('components/ringkasan_error') ?>
<form action="<?= url_to('daftar') ?>" method="post" novalidate>
    <?= csrf_field() ?>
    <?= komponen('components/field', ['nama' => 'nama', 'label' => 'Nama lengkap', 'atribut' => ['autocomplete' => 'name', 'maxlength' => 100, 'required' => true]]) ?>
    <?= komponen('components/field', [
        'nama' => 'no_telepon', 'label' => 'Nomor HP / WhatsApp', 'tipe' => 'tel',
        'hint' => 'Admin menghubungi nomor ini jika perlu keterangan tambahan. Anda bisa merahasiakannya dari operator di setiap laporan.',
        'atribut' => ['autocomplete' => 'tel', 'inputmode' => 'tel', 'placeholder' => '081234567890', 'required' => true],
    ]) ?>
    <?= komponen('components/field', ['nama' => 'alamat', 'label' => 'Alamat', 'tipe' => 'textarea', 'atribut' => ['autocomplete' => 'street-address', 'rows' => 2, 'maxlength' => 500, 'required' => true]]) ?>
    <?= komponen('components/field', [
        'nama' => 'username', 'label' => 'Username',
        'hint' => 'Untuk masuk. Huruf, angka, titik, garis bawah, atau tanda hubung.',
        'atribut' => ['autocomplete' => 'username', 'autocapitalize' => 'none', 'spellcheck' => 'false', 'maxlength' => 100, 'required' => true],
    ]) ?>
    <?= komponen('components/field', ['nama' => 'password', 'label' => 'Kata sandi', 'tipe' => 'password', 'hint' => 'Minimal 8 karakter.', 'atribut' => ['autocomplete' => 'new-password', 'minlength' => 8, 'required' => true]]) ?>
    <?= komponen('components/field', ['nama' => 'password_konfirmasi', 'label' => 'Ulangi kata sandi', 'tipe' => 'password', 'atribut' => ['autocomplete' => 'new-password', 'required' => true]]) ?>

    <p class="teks-kecil teks-muted">Dengan mendaftar, Anda menyetujui <a href="<?= url_to('syarat') ?>">Syarat &amp; Ketentuan</a> dan <a href="<?= url_to('privasi') ?>">Kebijakan Privasi</a>.</p>
    <button type="submit" class="tombol tombol--utama tombol--besar tombol--penuh" data-memuat="Membuat akun…">Buat akun</button>
</form>

<p class="auth__alt">Sudah punya akun? <a href="<?= url_to('masuk') ?>">Masuk</a></p>
<?= $this->endSection() ?>
