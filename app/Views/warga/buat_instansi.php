<?php
$ubah = service('request')->getGet('ubah') !== null;
?>
<?= $this->extend('layouts/alur') ?>
<?= $this->section('konten') ?>

<?= komponen('components/ringkasan_error') ?>

<form action="<?= url_to('warga.laporan.langkah', 'instansi') ?>" method="post" novalidate>
    <?= csrf_field() ?>
    <h1>Di mana terjadinya?</h1>
    <p class="lead">Bagian ini hanya dilihat admin LaporKan dan instansi yang menerima laporan Anda. Tidak pernah ditampilkan di halaman publik.</p>

    <?= komponen('components/field', [
        'nama' => 'instansi_terlapor', 'label' => 'Instansi atau kantor',
        'hint' => 'Contoh: Kantor Pelayanan Perizinan Kabupaten X, Dinas Pendidikan Kota Y',
        'nilai' => $draf['instansi_terlapor'] ?? '', 'atribut' => ['maxlength' => 255, 'autocomplete' => 'off', 'required' => true],
    ]) ?>
    <?= komponen('components/field', [
        'nama' => 'pihak_terlapor', 'label' => 'Siapa yang terlibat? (tidak wajib)',
        'hint' => 'Nama atau jabatan, jika Anda tahu. Contoh: petugas loket, kepala seksi perizinan',
        'nilai' => $draf['pihak_terlapor'] ?? '', 'atribut' => ['maxlength' => 255, 'autocomplete' => 'off'],
    ]) ?>
    <?= komponen('components/field', [
        'nama' => 'id_provinsi', 'label' => 'Provinsi', 'tipe' => 'select',
        'opsi' => $opsiProvinsi, 'kosong' => 'Pilih provinsi', 'nilai' => $idProvinsi,
        'atribut' => ['data-anak' => 'id_kabupaten_kota', 'data-url' => site_url('api/wilayah/__ID__/kabupaten-kota'), 'required' => true],
    ]) ?>
    <?= komponen('components/field', [
        'nama' => 'id_kabupaten_kota', 'label' => 'Kabupaten/kota', 'tipe' => 'select',
        'opsi' => $opsiKabupaten, 'kosong' => $opsiKabupaten === [] ? 'Pilih provinsi dulu' : 'Pilih kabupaten/kota',
        'nilai' => (string) ($draf['id_kabupaten_kota'] ?? ''), 'atribut' => ['required' => true],
    ]) ?>
    <noscript><p class="teks-muted teks-kecil">Setelah memilih provinsi, tekan Lanjutkan untuk memuat daftar kabupaten/kota.</p></noscript>

    <?php if ($ubah): ?><input type="hidden" name="kembali_ke_periksa" value="1"><?php endif ?>
    <div class="alur-aksi">
        <button type="submit" class="tombol tombol--utama tombol--besar tombol--penuh-mobile">Lanjutkan</button>
    </div>
</form>

<?= $this->endSection() ?>
