<?= $this->extend('layouts/petugas') ?>
<?= $this->section('konten') ?>

<div class="halaman-judul"><h1 class="mb-0">Kritik &amp; Saran</h1></div>

<?php if ($saran === []): ?>
    <div class="kosong"><?= ikon('message') ?><p>Belum ada kritik atau saran.</p></div>
<?php else: ?>
    <div class="tumpuk-kecil">
        <?php foreach ($saran as $s): ?>
            <article class="kartu">
                <div class="baris baris--antara">
                    <h2 class="mb-0" style="font-size:1.125rem"><?= esc($s['nama'] ?: 'Anonim') ?></h2>
                    <time class="teks-muted teks-kecil" datetime="<?= tanggal_iso($s['tgl_saran']) ?>"><?= tanggal($s['tgl_saran']) ?></time>
                </div>
                <?php $kontak = array_filter([$s['no_telepon'] ?? '', $s['alamat'] ?? '']); ?>
                <?php if ($kontak !== []): ?><p class="teks-muted teks-kecil"><?= esc(implode(' · ', $kontak)) ?></p><?php endif ?>
                <p class="isi-laporan"><?= esc($s['saran']) ?></p>
            </article>
        <?php endforeach ?>
    </div>
    <?= $pager->links('default', 'laporkan') ?>
<?php endif ?>

<?= $this->endSection() ?>
