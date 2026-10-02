<?php$ubah     = static fn (string $slug): string => url_to('warga.laporan.langkah', $slug) . '?ubah=1';
$kerugian = isset($draf['perkiraan_kerugian']) ? 'Rp ' . number_format((int) $draf['perkiraan_kerugian'], 0, ',', '.') : null;
?>
<?= $this->extend('layouts/alur') ?>
<?= $this->section('konten') ?>

<h1>Periksa laporan Anda</h1>
<p class="lead">Pastikan semuanya sudah benar sebelum dikirim.</p>

<ul class="rincian-periksa">
    <li>
        <span class="rincian-periksa__label">Jenis dugaan korupsi</span>
        <a class="rincian-periksa__ubah" href="<?= $ubah('jenis') ?>">Ubah<span class="sr-only"> jenis</span></a>
        <div class="rincian-periksa__isi"><?= esc($kategori) ?></div>
    </li>
    <li>
        <span class="rincian-periksa__label">Kronologi</span>
        <a class="rincian-periksa__ubah" href="<?= $ubah('kronologi') ?>">Ubah<span class="sr-only"> kronologi</span></a>
        <div class="rincian-periksa__isi"><?= esc($draf['isi_laporan']) ?><?= ! empty($draf['waktu_kejadian']) ? "\n\nWaktu kejadian: " . esc(tanggal($draf['waktu_kejadian'], false)) : '' ?><?= $kerugian !== null ? "\nPerkiraan nilai: " . esc($kerugian) : '' ?></div>
    </li>
    <li>
        <span class="rincian-periksa__label">Instansi dan wilayah</span>
        <a class="rincian-periksa__ubah" href="<?= $ubah('instansi') ?>">Ubah<span class="sr-only"> instansi dan wilayah</span></a>
        <div class="rincian-periksa__isi"><?= esc($draf['instansi_terlapor']) ?><?= ! empty($draf['pihak_terlapor']) ? "\nPihak terlibat: " . esc($draf['pihak_terlapor']) : '' ?><?= "\n" . esc($lokasi) ?></div>
    </li>
    <li>
        <span class="rincian-periksa__label">Bukti</span>
        <a class="rincian-periksa__ubah" href="<?= $ubah('bukti') ?>"><?= $bukti === [] ? 'Tambah' : 'Ubah' ?><span class="sr-only"> bukti</span></a>
        <div class="rincian-periksa__isi">
            <?php if ($bukti === []): ?>
                <span class="teks-muted">Tanpa bukti</span>
            <?php else: ?>
                <?= komponen('components/daftar_bukti', ['bukti' => $bukti, 'url' => static fn (array $b): string => url_to('warga.laporan.bukti_draf', $b['nama'])]) ?>
            <?php endif ?>
        </div>
    </li>
</ul>

<form action="<?= url_to('warga.laporan.langkah', 'periksa') ?>" method="post">
    <?= csrf_field() ?>
    <?php  ?>
    <div class="field">
        <label class="centang">
            <input type="checkbox" name="rahasia" value="1" checked aria-describedby="rahasia-hint">
            <span><strong>Rahasiakan identitas saya</strong></span>
        </label>
        <span class="hint" id="rahasia-hint">Nama dan nomor telepon Anda tidak ditampilkan kepada operator. Hanya administrator yang dapat membukanya bila perlu menghubungi Anda, dan setiap pembukaan tercatat.</span>
    </div>

    <div class="banner">
        <?= ikon('eye') ?>
        <div class="banner__isi"><p>Laporan Anda hanya dilihat oleh admin LaporKan. Setelah diverifikasi, admin dapat menampilkan <strong>ringkasan tanpa identitas</strong> di halaman publik. Kronologi lengkap, nama instansi, pihak yang terlibat, dan bukti tidak pernah ditampilkan.</p></div>
    </div>

    <div class="alur-aksi">
        <button type="submit" class="tombol tombol--utama tombol--besar tombol--penuh-mobile"><?= ikon('send') ?> Kirim laporan</button>
    </div>
</form>

<?= $this->endSection() ?>
