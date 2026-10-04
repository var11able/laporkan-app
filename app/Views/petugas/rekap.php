<?= $this->extend('layouts/petugas') ?>
<?= $this->section('konten') ?>

<div class="halaman-judul baris baris--antara">
    <h1 class="mb-0">Rekap Laporan</h1>
    <a class="tombol tombol--utama" href="<?= url_to('petugas.rekap.cetak') ?>?<?= esc(http_build_query($filter), 'attr') ?>" target="_blank"><?= ikon('printer') ?> Versi cetak</a>
</div>

<?= komponen('components/filter_rekap', ['aksi' => url_to('petugas.rekap'), 'filter' => $filter, 'opsiProvinsi' => $opsiProvinsi, 'opsiKategori' => $opsiKategori, 'untuk' => 'petugas']) ?>

<p class="teks-muted" aria-live="polite"><?= count($laporan) ?> laporan, <?= tanggal($filter['dari'], false) ?> – <?= tanggal($filter['sampai'], false) ?></p>

<?php if ($laporan === []): ?>
    <div class="kosong"><?= ikon('search') ?><p>Tidak ada laporan pada rentang ini.</p></div>
<?php else: ?>
    <?= komponen('components/tabel_rekap', ['laporan' => $laporan, 'denganPelapor' => true, 'tautan' => static fn ($p) => url_to('petugas.laporan.detail', $p->id_pengaduan)]) ?>
<?php endif ?>

<?= $this->endSection() ?>
