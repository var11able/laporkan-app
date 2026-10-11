<?php
$baru = $laporan === null;
$aksi = $baru ? site_url('petugas/laporan') : url_to('petugas.laporan.detail', $laporan->id_laporan);
?>
<?= $this->extend('layouts/petugas') ?>
<?= $this->section('konten') ?>

<div class="halaman-judul">
    <a class="kembali" href="<?= $baru ? url_to('petugas.laporan') : url_to('petugas.laporan.detail', $laporan->id_laporan) ?>"><?= ikon('chevron-left') ?> Kembali</a>
    <h1 style="font-size:1.75rem"><?= esc($judul) ?></h1>
    <?php if ($baru): ?><p class="lead mb-0">Untuk laporan yang diterima lewat telepon, surat, atau datang langsung.</p><?php endif ?>
</div>

<?= komponen('components/ringkasan_error') ?>

<form action="<?= $aksi ?>" method="post" enctype="multipart/form-data" class="form" novalidate>
    <?= csrf_field() ?>
    <?php if (! $baru): ?><input type="hidden" name="_method" value="PUT"><?php endif ?>

    <?php if ($identitasTerbuka): ?>
        <?= komponen('components/field', [
            'nama' => 'id_warga', 'label' => 'Pelapor', 'tipe' => 'select', 'opsi' => $opsiWarga, 'kosong' => 'Pilih pelapor',
            'hint' => 'Belum terdaftar? Tambahkan dulu di menu Warga.', 'nilai' => (string) ($laporan?->id_warga ?? ''),
        ]) ?>
    <?php else: ?>
        <div class="field">
            <span class="tebal">Pelapor</span>
            <p class="mb-0"><?= ikon('lock') ?> Dirahasiakan. Pelapor tidak dapat diganti tanpa membuka identitasnya.</p>
        </div>
    <?php endif ?>

    <?= komponen('components/form_laporan', [
        'laporan'     => $laporan,
        'bukti'         => $bukti,
        'urlBukti'      => $baru ? null : static fn (array $b): string => url_to('petugas.laporan.bukti', $laporan->id_laporan, $b['id_bukti']),
        'opsiKategori'  => $opsiKategori,
        'opsiProvinsi'  => $opsiProvinsi,
        'opsiKabupaten' => $opsiKabupaten,
    ]) ?>

    <div class="tombol-grup">
        <button type="submit" class="tombol tombol--utama"><?= $baru ? 'Buat laporan' : 'Simpan perubahan' ?></button>
        <a class="tombol" href="<?= $baru ? url_to('petugas.laporan') : url_to('petugas.laporan.detail', $laporan->id_laporan) ?>">Batal</a>
    </div>
</form>

<?= $this->endSection() ?>
