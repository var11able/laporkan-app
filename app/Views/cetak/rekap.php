<?php
use App\Enums\StatusPengaduan;

$status = StatusPengaduan::tryFrom($filter['status']);
$untukPetugas = isset($petugas) && $petugas !== null;
?>
<?= $this->extend('layouts/cetak') ?>
<?= $this->section('konten') ?>

<h1 style="font-size:16pt;text-align:center;margin-bottom:4px">REKAPITULASI LAPORAN DUGAAN KORUPSI</h1>
<p style="text-align:center;margin-bottom:16px">
    Periode <?= tanggal($filter['dari'], false) ?> s.d. <?= tanggal($filter['sampai'], false) ?>
</p>

<div class="cetak-meta">
    <span>
        Status: <?= esc($status === null ? 'Semua' : ($untukPetugas ? $status->labelPetugas() : $status->labelWarga())) ?>
        <?php if (! empty($namaKategori)): ?> · Jenis: <?= esc($namaKategori) ?><?php endif ?>
        <?php if (! empty($namaProvinsi)): ?> · Provinsi: <?= esc($namaProvinsi) ?><?php endif ?>
        <?php if (! empty($pelapor)): ?> · Pelapor: <?= esc($pelapor) ?><?php endif ?>
    </span>
    <span>Jumlah: <?= count($laporan) ?> laporan</span>
</div>

<?php if ($laporan === []): ?>
    <p>Tidak ada laporan pada periode ini.</p>
<?php else: ?>
    <?= komponen('components/tabel_rekap', ['laporan' => $laporan, 'denganPelapor' => $untukPetugas]) ?>
<?php endif ?>

<?php if ($untukPetugas): ?>
    <div class="ttd">
        <p class="mb-0"><?= tanggal(date('Y-m-d'), false) ?></p>
        <p class="mb-0"><?= esc($petugas->jabatan()->label()) ?> LaporKan</p>
        <div class="ttd__ruang"></div>
        <p class="mb-0" style="text-decoration:underline;font-weight:600"><?= esc($petugas->nama) ?></p>
    </div>
<?php endif ?>

<?= $this->endSection() ?>
