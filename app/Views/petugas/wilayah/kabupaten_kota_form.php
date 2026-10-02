<?php$baru = $kabupaten === null;
?>
<?= $this->extend('layouts/petugas') ?>
<?= $this->section('konten') ?>

<div class="halaman-judul">
    <a class="kembali" href="<?= url_to('petugas.kabupaten_kota') ?>"><?= ikon('chevron-left') ?> Kabupaten/Kota</a>
    <h1 style="font-size:1.75rem"><?= esc($judul) ?></h1>
</div>

<?= komponen('components/ringkasan_error') ?>

<form action="<?= $baru ? site_url('petugas/kabupaten-kota') : site_url('petugas/kabupaten-kota/' . $kabupaten['id_kabupaten_kota']) ?>" method="post" class="form" novalidate>
    <?= csrf_field() ?>
    <?php if (! $baru): ?><input type="hidden" name="_method" value="PUT"><?php endif ?>
    <?= komponen('components/field', ['nama' => 'id_provinsi', 'label' => 'Provinsi', 'tipe' => 'select', 'kelas' => 'input--sedang', 'opsi' => $opsiProvinsi, 'kosong' => 'Pilih provinsi', 'nilai' => (string) ($kabupaten['id_provinsi'] ?? '')]) ?>
    <?= komponen('components/field', ['nama' => 'kabupaten_kota', 'label' => 'Nama kabupaten/kota', 'hint' => 'Tulis lengkap, contoh: Kabupaten Pasuruan atau Kota Malang.', 'kelas' => 'input--sedang', 'nilai' => $kabupaten['kabupaten_kota'] ?? '', 'atribut' => ['maxlength' => 100]]) ?>
    <div class="tombol-grup">
        <button type="submit" class="tombol tombol--utama">Simpan</button>
        <a class="tombol" href="<?= url_to('petugas.kabupaten_kota') ?>">Batal</a>
    </div>
</form>

<?= $this->endSection() ?>
