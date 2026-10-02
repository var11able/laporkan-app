<?php?>
<?= $this->extend('layouts/publik') ?>
<?= $this->section('konten') ?>

<div class="halaman-judul">
    <h1>Daftar Laporan</h1>
    <p class="lead mb-0">Ringkasan laporan dugaan korupsi yang sudah diverifikasi admin dan perkembangannya. Identitas pelapor tidak pernah ditampilkan.</p>
</div>

<form class="filter" method="get" action="<?= url_to('laporan.publik') ?>" role="search">
    <?= komponen('components/field', ['nama' => 'q', 'label' => 'Cari laporan', 'tipe' => 'search', 'nilai' => $filter['q_publik'], 'atribut' => ['placeholder' => 'Kata kunci atau nomor laporan']]) ?>
    <?= komponen('components/field', ['nama' => 'kategori', 'label' => 'Jenis', 'tipe' => 'select', 'opsi' => $opsiKategori, 'kosong' => 'Semua jenis', 'nilai' => $filter['kategori']]) ?>
    <?= komponen('components/field', ['nama' => 'provinsi', 'label' => 'Provinsi', 'tipe' => 'select', 'opsi' => $opsiProvinsi, 'kosong' => 'Semua provinsi', 'nilai' => $filter['provinsi']]) ?>
    <?= komponen('components/field', ['nama' => 'status', 'label' => 'Status', 'tipe' => 'select', 'opsi' => $opsiStatus, 'kosong' => 'Semua status', 'nilai' => $filter['status']]) ?>
    <div><button type="submit" class="tombol"><?= ikon('search') ?> Cari</button></div>
</form>

<?php if ($laporan === []): ?>
    <div class="kosong">
        <div class="kosong__ikon"><?= ikon('search') ?></div>
        <h2>Tidak ada laporan</h2>
        <p>Belum ada laporan terverifikasi yang cocok dengan pencarian ini.</p>
        <a class="tombol" href="<?= url_to('laporan.publik') ?>">Tampilkan semua</a>
    </div>
<?php else: ?>
    <p class="teks-kecil teks-muted" aria-live="polite"><?= $pager->getTotal() ?> laporan</p>
    <div class="grid grid--3">
        <?php foreach ($laporan as $p): ?>
            <?= komponen('components/kartu_laporan', ['p' => $p]) ?>
        <?php endforeach ?>
    </div>
    <?= $pager->links('default', 'laporkan') ?>
<?php endif ?>

<?= $this->endSection() ?>
