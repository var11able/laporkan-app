<?php
$errorFoto = galat('foto_tanggapan');
?>
<?= $this->extend('layouts/petugas') ?>
<?= $this->section('konten') ?>

<div class="halaman-judul">
    <a class="kembali" href="<?= url_to('petugas.laporan.detail', $pengaduan->id_pengaduan) ?>"><?= ikon('chevron-left') ?> <?= esc($pengaduan->nomor_laporan) ?></a>
    <h1 style="font-size:1.75rem">Ubah tanggapan</h1>
    <p class="mb-0">Status tanggapan ini: <?= status_badge($tanggapan->status(), 'petugas') ?> <span class="teks-muted">(status tidak dapat diubah; hapus tanggapan terakhir jika statusnya salah)</span></p>
</div>

<?= komponen('components/ringkasan_error') ?>

<form action="<?= site_url('petugas/tanggapan/' . $tanggapan->id_tanggapan) ?>" method="post" enctype="multipart/form-data" class="form" novalidate>
    <?= csrf_field() ?>
    <input type="hidden" name="_method" value="PUT">
    <?= komponen('components/field', ['nama' => 'isi_tanggapan', 'label' => 'Pesan untuk pelapor', 'tipe' => 'textarea', 'nilai' => $tanggapan->isi_tanggapan, 'atribut' => ['rows' => 5, 'maxlength' => 5000]]) ?>
    <div class="field<?= $errorFoto !== null ? ' field--error' : '' ?>">
        <label for="foto_tanggapan"><?= $tanggapan->fotoUrl() !== null ? 'Ganti foto bukti' : 'Tambah foto bukti' ?> (tidak wajib)</label>
        <?php if ($tanggapan->thumbUrl() !== null): ?>
            <img src="<?= esc($tanggapan->thumbUrl(), 'attr') ?>" alt="Foto bukti saat ini" style="max-width:200px;border-radius:var(--radius-md);margin-bottom:var(--space-2)">
        <?php endif ?>
        <?php if ($errorFoto !== null): ?><span class="pesan-error"><?= ikon('alert') ?><span><?= esc($errorFoto) ?></span></span><?php endif ?>
        <input type="file" id="foto_tanggapan" name="foto_tanggapan" accept="image/jpeg,image/png,image/webp,image/gif" data-pratinjau="pratinjau-tanggapan">
        <div class="pratinjau-foto" id="pratinjau-tanggapan" hidden></div>
    </div>
    <div class="tombol-grup">
        <button type="submit" class="tombol tombol--utama">Simpan</button>
        <a class="tombol" href="<?= url_to('petugas.laporan.detail', $pengaduan->id_pengaduan) ?>">Batal</a>
    </div>
</form>

<?= $this->endSection() ?>
