<?= $this->extend('layouts/publik') ?>
<?= $this->section('konten') ?>

<div class="halaman-judul baris baris--antara">
    <div>
        <h1>Rekap Laporan Saya</h1>
        <p class="lead mb-0">Laporan Anda dalam rentang tanggal tertentu, siap dicetak.</p>
    </div>
    <a class="tombol" href="<?= url_to('warga.rekap.cetak') ?>?<?= esc(http_build_query($filter), 'attr') ?>" target="_blank"><?= ikon('printer') ?> Versi cetak</a>
</div>

<?= komponen('components/filter_rekap', ['aksi' => url_to('warga.rekap'), 'filter' => $filter, 'untuk' => 'warga']) ?>

<p class="teks-muted" aria-live="polite"><?= count($laporan) ?> laporan, <?= tanggal($filter['dari'], false) ?> – <?= tanggal($filter['sampai'], false) ?></p>

<?php if ($laporan === []): ?>
    <div class="kosong"><?= ikon('search') ?><p>Tidak ada laporan pada rentang tanggal ini.</p></div>
<?php else: ?>
    <?= komponen('components/tabel_rekap', ['laporan' => $laporan, 'tautan' => static fn ($p) => url_to('warga.laporan.detail', $p->id_pengaduan)]) ?>
<?php endif ?>

<?= $this->endSection() ?>
