<?php
$ubah     = service('request')->getGet('ubah') !== null;
$kerugian = isset($draf['perkiraan_kerugian']) ? (string) $draf['perkiraan_kerugian'] : '';
?>
<?= $this->extend('layouts/alur') ?>
<?= $this->section('konten') ?>

<?= komponen('components/ringkasan_error') ?>

<form action="<?= url_to('warga.laporan.langkah', 'kronologi') ?>" method="post" novalidate data-draf="laporan-baru">
    <?= csrf_field() ?>
    <h1>Ceritakan kejadiannya</h1>
    <p class="lead">Tulis apa adanya: apa yang terjadi, siapa yang terlibat, kapan, di mana, dan bagaimana caranya.</p>
    <?= komponen('components/field', [
        'nama' => 'isi_laporan', 'label' => 'Kronologi', 'tipe' => 'textarea',
        'hint' => 'Contoh: Saat mengurus izin usaha pada Agustus 2026, petugas loket meminta uang Rp500.000 di luar biaya resmi agar izin cepat selesai. Permintaan itu disampaikan lisan di loket 3.',
        'nilai' => $draf['isi_laporan'] ?? '', 'atribut' => ['maxlength' => 5000, 'rows' => 8, 'data-hitung' => 'isi-hitung', 'required' => true],
    ]) ?>
    <div class="baris baris--antara" style="margin-top: calc(-1 * var(--space-4)); margin-bottom: var(--space-5)">
        <span class="tersimpan" data-tersimpan hidden><?= ikon('check') ?> Tersimpan otomatis di perangkat ini</span>
        <span class="penghitung" id="isi-hitung" aria-live="polite" style="margin:0 0 0 auto"></span>
    </div>

    <?= komponen('components/field', [
        'nama' => 'waktu_kejadian', 'label' => 'Kapan terjadinya? (tidak wajib)', 'tipe' => 'date', 'kelas' => 'input--sedang',
        'hint' => 'Jika tidak ingat tanggal pastinya, pilih perkiraan tanggalnya.',
        'nilai' => $draf['waktu_kejadian'] ?? '', 'atribut' => ['max' => date('Y-m-d')],
    ]) ?>
    <?= komponen('components/field', [
        'nama' => 'perkiraan_kerugian', 'label' => 'Perkiraan nilai uang yang terlibat (tidak wajib)', 'kelas' => 'input--sedang',
        'hint' => 'Dalam rupiah, angka saja. Contoh: 500000',
        'nilai' => $kerugian, 'atribut' => ['inputmode' => 'numeric', 'autocomplete' => 'off', 'maxlength' => 30],
    ]) ?>

    <?php if ($ubah): ?><input type="hidden" name="kembali_ke_periksa" value="1"><?php endif ?>
    <div class="alur-aksi">
        <button type="submit" class="tombol tombol--utama tombol--besar tombol--penuh-mobile">Lanjutkan</button>
    </div>
</form>

<?= $this->endSection() ?>
