<?php
use App\Services\UploadService;

$error = galat('bukti');
$ubah  = service('request')->getGet('ubah') !== null;
$sisa  = UploadService::MAKS_BUKTI - count($bukti);
?>
<?= $this->extend('layouts/alur') ?>
<?= $this->section('konten') ?>

<?= komponen('components/ringkasan_error') ?>

<h1>Tambahkan bukti</h1>
<p class="lead">Foto atau dokumen membantu admin dan instansi berwenang memeriksa laporan Anda. Anda boleh melewati langkah ini.</p>

<?php if ($bukti !== []): ?>
    <?= komponen('components/daftar_bukti', [
        'bukti' => $bukti,
        'url'   => static fn (array $b): string => url_to('warga.laporan.bukti_draf', $b['nama']),
        'hapus' => url_to('warga.laporan.langkah', 'bukti'),
    ]) ?>
<?php endif ?>

<form action="<?= url_to('warga.laporan.langkah', 'bukti') ?>" method="post" enctype="multipart/form-data" novalidate>
    <?= csrf_field() ?>
    <?php if ($sisa > 0): ?>
        <div class="field<?= $error !== null ? ' field--error' : '' ?>">
            <?php if ($error !== null): ?><span class="pesan-error" id="bukti-error"><?= ikon('alert') ?><span><span class="sr-only">Error:</span> <?= esc($error) ?></span></span><?php endif ?>
            <div class="unggah">
                <input type="file" id="bukti" name="bukti[]" multiple accept="image/jpeg,image/png,image/webp,image/gif,application/pdf"
                       data-pratinjau="pratinjau-bukti" aria-describedby="bukti-hint<?= $error !== null ? ' bukti-error' : '' ?>">
                <label class="unggah__area" for="bukti">
                    <span class="unggah__ikon" aria-hidden="true"><?= ikon('upload') ?></span>
                    <span class="unggah__judul">Pilih foto atau dokumen PDF</span>
                    <span class="hint teks-muted" id="bukti-hint">Sisa <?= $sisa ?> berkas, masing-masing maksimal 5 MB</span>
                </label>
            </div>
            <div class="pratinjau-foto" id="pratinjau-bukti" hidden></div>
        </div>
        <button type="submit" name="aksi" value="unggah" class="tombol"><?= ikon('upload') ?> Unggah</button>
    <?php else: ?>
        <p class="teks-muted">Anda sudah menambahkan <?= UploadService::MAKS_BUKTI ?> bukti. Hapus salah satu untuk menggantinya.</p>
    <?php endif ?>

    <div class="banner" style="margin-top: var(--space-5)">
        <?= ikon('shield') ?>
        <div class="banner__isi">
            <p>Data lokasi dan jenis ponsel di dalam foto kami hapus otomatis. Dokumen PDF dapat menyimpan nama pembuatnya. Jika itu nama Anda, simpan ulang dokumennya sebagai PDF baru atau foto dokumennya saja.</p>
        </div>
    </div>

    <?php if ($ubah): ?><input type="hidden" name="kembali_ke_periksa" value="1"><?php endif ?>
    <div class="alur-aksi">
        <button type="submit" name="aksi" value="lanjut" class="tombol tombol--utama tombol--besar tombol--penuh-mobile" data-memuat="Mengunggah…">Lanjutkan</button>
        <?php if ($bukti === []): ?>
            <p class="teks-kecil teks-muted teks-tengah" style="margin: var(--space-2) 0 0">Tidak ada bukti? Tekan Lanjutkan saja.</p>
        <?php endif ?>
    </div>
</form>

<?= $this->endSection() ?>
