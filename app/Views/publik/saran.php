<?= $this->extend('layouts/publik') ?>
<?= $this->section('konten') ?>

<h1>Kritik &amp; Saran</h1>
<p class="lead">Punya masukan untuk website LaporKan? Sampaikan di sini; admin membacanya untuk memperbaiki layanan. Untuk melaporkan dugaan korupsi, gunakan <a href="<?= url_to('warga.laporan.baru') ?>">Buat Laporan</a> agar bisa dilacak dan diteruskan.</p>
<p>Nama, nomor telepon, dan alamat boleh dikosongkan.</p>

<?= komponen('components/ringkasan_error') ?>

<form action="<?= url_to('saran') ?>" method="post" class="form" novalidate>
    <?= csrf_field() ?>
    <?= komponen('components/field', ['nama' => 'nama', 'label' => 'Nama (tidak wajib)', 'atribut' => ['autocomplete' => 'name', 'maxlength' => 100]]) ?>
    <?= komponen('components/field', [
        'nama' => 'no_telepon', 'label' => 'Nomor telepon (tidak wajib)', 'tipe' => 'tel', 'kelas' => 'input--sedang',
        'hint' => 'Agar admin bisa membalas masukan Anda. Contoh: 081234567890',
        'atribut' => ['autocomplete' => 'tel', 'inputmode' => 'tel'],
    ]) ?>
    <?= komponen('components/field', ['nama' => 'alamat', 'label' => 'Alamat (tidak wajib)', 'atribut' => ['autocomplete' => 'street-address', 'maxlength' => 500]]) ?>
    <?= komponen('components/field', ['nama' => 'saran', 'label' => 'Kritik atau saran', 'tipe' => 'textarea', 'atribut' => ['maxlength' => 5000, 'data-hitung' => 'saran-hitung', 'required' => true]]) ?>
    <span class="penghitung" id="saran-hitung" aria-live="polite" style="margin-top: calc(-1 * var(--space-4)); margin-bottom: var(--space-5)"></span>
    <button type="submit" class="tombol tombol--utama tombol--penuh-mobile"><?= ikon('send') ?> Kirim</button>
</form>

<?= $this->endSection() ?>
