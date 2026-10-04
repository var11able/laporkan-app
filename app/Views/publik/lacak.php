<?= $this->extend('layouts/publik') ?>
<?= $this->section('konten') ?>

<div class="teks-tengah" style="margin-bottom: var(--space-5)">
    <div class="sukses__ikon" style="width:72px;height:72px" aria-hidden="true"><?= ikon('search') ?></div>
    <h1>Lacak Laporan</h1>
    <p class="lead" style="margin-inline:auto">Masukkan nomor laporan untuk melihat perkembangannya. Tidak perlu masuk.</p>
</div>

<form action="<?= url_to('lacak') ?>" method="get" role="search" class="form">
    <div class="field<?= $tidakDitemukan ? ' field--error' : '' ?>">
        <label for="nomor">Nomor laporan</label>
        <span class="hint" id="nomor-hint">Ada di layar setelah Anda mengirim laporan. Contoh: LP-2026-000123</span>
        <?php if ($tidakDitemukan): ?>
            <span class="pesan-error" id="nomor-error"><?= ikon('alert') ?><span><span class="sr-only">Error:</span> Laporan <?= esc($nomor) ?> tidak ditemukan. Periksa kembali nomornya.</span></span>
        <?php endif ?>
        <input type="search" id="nomor" name="nomor" value="<?= esc($nomor, 'attr') ?>" style="font-size:1.25rem;font-weight:600;letter-spacing:0.02em"
               aria-describedby="nomor-hint<?= $tidakDitemukan ? ' nomor-error' : '' ?>"<?= $tidakDitemukan ? ' aria-invalid="true" autofocus' : '' ?>
               autocomplete="off" autocapitalize="characters" spellcheck="false" required>
    </div>
    <button type="submit" class="tombol tombol--utama tombol--besar" style="width:100%"><?= ikon('search') ?> Lacak</button>
</form>

<?php if ($pengaduan !== null): ?>
    <section class="blok" style="margin-top: var(--space-6)" aria-labelledby="judul-hasil">
        <h2 id="judul-hasil" class="blok__judul">Laporan <?= esc($pengaduan->nomor_laporan) ?></h2>
        <?= komponen('components/tracker', ['pengaduan' => $pengaduan, 'tanggapan' => $tanggapan, 'untuk' => 'publik']) ?>
        <?= komponen('components/riwayat', ['pengaduan' => $pengaduan, 'tanggapan' => $tanggapan, 'untuk' => 'publik']) ?>
        <p class="teks-kecil teks-muted">Demi melindungi pelapor, isi laporan tidak ditampilkan di sini. Pelapor dapat membaca isi dan pesan dari admin dengan <a href="<?= url_to('masuk') ?>">masuk ke akunnya</a>.</p>
    </section>
<?php endif ?>

<p class="teks-tengah teks-muted" style="margin-top: var(--space-5)">Lupa nomornya? Semua laporan Anda ada di <a href="<?= url_to('warga.beranda') ?>">Laporan Saya</a>.</p>

<?= $this->endSection() ?>
