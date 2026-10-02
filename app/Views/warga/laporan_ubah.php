<?php?>
<?= $this->extend('layouts/publik') ?>
<?= $this->section('konten') ?>

<div class="halaman-judul">
    <a class="kembali" href="<?= url_to('warga.laporan.detail', $pengaduan->id_pengaduan) ?>"><?= ikon('chevron-left') ?> Batal</a>
    <p class="teks-muted tebal mb-0"><?= esc($pengaduan->nomor_laporan) ?></p>
    <h1>Ubah laporan</h1>
</div>

<?= komponen('components/ringkasan_error') ?>

<form action="<?= url_to('warga.laporan.detail', $pengaduan->id_pengaduan) ?>" method="post" enctype="multipart/form-data" class="form" novalidate>
    <?= csrf_field() ?>
    <input type="hidden" name="_method" value="PUT">

    <?= komponen('components/form_laporan', [
        'pengaduan'     => $pengaduan,
        'bukti'         => $bukti,
        'urlBukti'      => static fn (array $b): string => url_to('warga.laporan.bukti', $pengaduan->id_pengaduan, $b['id_bukti']),
        'opsiKategori'  => $opsiKategori,
        'opsiProvinsi'  => $opsiProvinsi,
        'opsiKabupaten' => $opsiKabupaten,
    ]) ?>

    <div class="tombol-grup">
        <button type="submit" class="tombol tombol--utama">Simpan perubahan</button>
        <a class="tombol" href="<?= url_to('warga.laporan.detail', $pengaduan->id_pengaduan) ?>">Batal</a>
    </div>
</form>

<?= $this->endSection() ?>
