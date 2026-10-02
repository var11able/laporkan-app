<?php$bisaDiubah = $pengaduan->status()->bisaDiubahWarga();
?>
<?= $this->extend('layouts/publik') ?>
<?= $this->section('konten') ?>

<div class="halaman-judul">
    <a class="kembali" href="<?= url_to('warga.beranda') ?>"><?= ikon('chevron-left') ?> Laporan Saya</a>
    <div class="salin teks-muted tebal">
        <?= ikon('hash') ?> <span class="angka-tabular"><?= esc($pengaduan->nomor_laporan) ?></span>
        <button type="button" class="tombol tombol--teks tombol--kecil" data-salin="<?= esc($pengaduan->nomor_laporan, 'attr') ?>" hidden><?= ikon('copy') ?> Salin</button>
    </div>
    <h1><?= esc(penggalan($pengaduan->isi_laporan, 90)) ?></h1>
</div>

<div class="dua-kolom">
    <div>
        <?= komponen('components/tracker', ['pengaduan' => $pengaduan, 'tanggapan' => $tanggapan]) ?>

        <?php if ($bisaDiubah): ?>
            <div class="banner">
                <?= ikon('info') ?>
                <div class="banner__isi">
                    <p>Selama belum diperiksa admin, Anda masih bisa mengubah atau menghapus laporan ini.</p>
                    <div class="tombol-grup" style="margin-top: var(--space-3)">
                        <a class="tombol tombol--kecil" href="<?= url_to('warga.laporan.ubah', $pengaduan->id_pengaduan) ?>"><?= ikon('edit') ?> Ubah</a>
                        <?= komponen('components/tombol_hapus', [
                            'aksi'       => url_to('warga.laporan.detail', $pengaduan->id_pengaduan),
                            'konfirmasi' => 'Laporan ' . $pengaduan->nomor_laporan . ' dan buktinya akan dihapus permanen.',
                        ]) ?>
                    </div>
                </div>
            </div>
        <?php endif ?>

        <?php if ($pengaduan->tampilPublik()): ?>
            <div class="banner banner--sukses">
                <?= ikon('eye') ?>
                <div class="banner__isi"><p>Ringkasan laporan ini tampil di <a href="<?= url_to('laporan.detail', $pengaduan->nomor_laporan) ?>">halaman publik</a> tanpa identitas Anda.</p></div>
            </div>
        <?php endif ?>

        <section class="blok" aria-labelledby="judul-riwayat">
            <h2 id="judul-riwayat" class="blok__judul">Riwayat</h2>
            <?= komponen('components/riwayat', ['pengaduan' => $pengaduan, 'tanggapan' => $tanggapan]) ?>
        </section>
    </div>

    <div class="tumpuk">
        <section class="blok" aria-labelledby="judul-isi">
            <h2 id="judul-isi" class="blok__judul">Laporan Anda</h2>
            <p class="isi-laporan"><?= esc($pengaduan->isi_laporan) ?></p>
            <?= komponen('components/rincian_laporan', ['pengaduan' => $pengaduan]) ?>
        </section>

        <?php if ($bukti !== [] || $pengaduan->fotoUrl() !== null): ?>
            <section class="blok" aria-labelledby="judul-bukti">
                <h2 id="judul-bukti" class="blok__judul">Bukti</h2>
                <?php if ($pengaduan->fotoUrl() !== null): ?>
                    <figure class="foto-laporan"><a href="<?= esc($pengaduan->fotoUrl(), 'attr') ?>" target="_blank" rel="noopener"><img src="<?= esc($pengaduan->thumbUrl(), 'attr') ?>" alt="Foto laporan" loading="lazy"></a></figure>
                <?php endif ?>
                <?= komponen('components/daftar_bukti', [
                    'bukti' => $bukti,
                    'url'   => static fn (array $b): string => url_to('warga.laporan.bukti', $pengaduan->id_pengaduan, $b['id_bukti']),
                ]) ?>
            </section>
        <?php endif ?>
    </div>
</div>

<?= $this->endSection() ?>
