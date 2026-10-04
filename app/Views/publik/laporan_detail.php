<?php
$milikSaya = isset($warga) && $warga !== null && $pengaduan->milik($warga->getId());
?>
<?= $this->extend('layouts/publik') ?>
<?= $this->section('konten') ?>

<div class="halaman-judul">
    <a class="kembali" href="<?= url_to('laporan.publik') ?>"><?= ikon('chevron-left') ?> Daftar laporan</a>
    <div class="salin teks-muted tebal">
        <?= ikon('hash') ?> <span class="angka-tabular"><?= esc($pengaduan->nomor_laporan) ?></span>
        <button type="button" class="tombol tombol--teks tombol--kecil" data-salin="<?= esc($pengaduan->nomor_laporan, 'attr') ?>" hidden><?= ikon('copy') ?> Salin</button>
    </div>
    <h1><?= esc(penggalan((string) $pengaduan->ringkasan_publik, 90)) ?></h1>
</div>

<?php if ($milikSaya): ?>
    <div class="banner"><?= ikon('info') ?><div class="banner__isi"><p>Ini laporan Anda. <a href="<?= url_to('warga.laporan.detail', $pengaduan->id_pengaduan) ?>">Buka di Laporan Saya</a> untuk melihat isi lengkap dan pesan dari admin.</p></div></div>
<?php endif ?>

<div class="dua-kolom">
    <div>
        <?= komponen('components/tracker', ['pengaduan' => $pengaduan, 'tanggapan' => $tanggapan, 'untuk' => 'publik']) ?>

        <section class="blok" aria-labelledby="judul-riwayat">
            <h2 id="judul-riwayat" class="blok__judul">Riwayat</h2>
            <?= komponen('components/riwayat', ['pengaduan' => $pengaduan, 'tanggapan' => $tanggapan, 'untuk' => 'publik']) ?>
        </section>
    </div>

    <div class="tumpuk">
        <section class="blok" aria-labelledby="judul-isi">
            <h2 id="judul-isi" class="blok__judul">Ringkasan</h2>
            <p class="isi-laporan"><?= esc($pengaduan->ringkasan_publik) ?></p>
            <dl class="rincian">
                <dt>Jenis</dt>
                <dd><?= esc($pengaduan->kategori ?? 'Lainnya') ?></dd>
                <dt>Provinsi</dt>
                <dd><?= esc($pengaduan->provinsi ?? 'Tidak disebutkan') ?></dd>
                <dt>Dilaporkan</dt>
                <dd><time datetime="<?= tanggal_iso($pengaduan->tgl_pengaduan) ?>"><?= tanggal($pengaduan->tgl_pengaduan, false) ?></time></dd>
            </dl>
            <p class="teks-kecil teks-muted">Ringkasan ditulis admin LaporKan setelah laporan diverifikasi. Laporan yang masuk adalah dugaan dan belum tentu terbukti; pembuktian menjadi wewenang instansi penegak hukum.</p>
        </section>
    </div>
</div>

<?= $this->endSection() ?>
