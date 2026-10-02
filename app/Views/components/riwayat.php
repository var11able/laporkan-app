<?phpuse App\Enums\StatusPengaduan;

$untuk ??= 'warga';
$identitasTerbuka ??= false;
$entri = array_reverse($tanggapan);
$label = static fn (StatusPengaduan $s): string => $untuk === 'petugas' ? $s->labelPetugas() : $s->labelWarga();
?>
<ol class="riwayat" aria-label="Riwayat laporan">
    <?php foreach ($entri as $i => $t): ?>
        <li class="riwayat__item<?= $i === 0 ? ' riwayat__item--kini' : '' ?>">
            <span class="riwayat__titik" aria-hidden="true"></span>
            <div class="riwayat__kepala">
                <h3 class="riwayat__judul"><?= esc($label($t->status())) ?></h3>
                <time class="riwayat__waktu" datetime="<?= tanggal_iso($t->tgl_tanggapan) ?>"><?= tanggal($t->tgl_tanggapan, $untuk !== 'publik') ?></time>
            </div>
            <?php if (! empty($t->instansi_tujuan)): ?>
                <p class="riwayat__pesan"><?= ikon('send') ?> Diteruskan ke <strong><?= esc($t->instansi_tujuan) ?></strong></p>
            <?php endif ?>
            <?php if ($untuk !== 'publik'): ?>
                <p class="riwayat__pesan"><?= esc($t->isi_tanggapan) ?></p>
                <p class="riwayat__oleh"><?= $untuk === 'petugas' ? esc($t->nama_petugas ?? 'Admin') : 'Admin LaporKan' ?></p>
                <?php if ($t->fotoUrl() !== null): ?>
                    <a class="riwayat__foto" href="<?= esc($t->fotoUrl(), 'attr') ?>" target="_blank" rel="noopener">
                        <img src="<?= esc($t->thumbUrl(), 'attr') ?>" alt="Foto pendukung dari admin" loading="lazy" width="200" height="150">
                    </a>
                <?php endif ?>
            <?php endif ?>
            <?php if (isset($aksi) && is_callable($aksi) && ($tombol = $aksi($t, $i === 0)) !== ''): ?>
                <div class="riwayat__aksi"><?= $tombol ?></div>
            <?php endif ?>
        </li>
    <?php endforeach ?>
    <li class="riwayat__item<?= $entri === [] ? ' riwayat__item--kini' : '' ?>">
        <span class="riwayat__titik" aria-hidden="true"></span>
        <div class="riwayat__kepala">
            <h3 class="riwayat__judul"><?= esc($label(StatusPengaduan::Baru)) ?></h3>
            <time class="riwayat__waktu" datetime="<?= tanggal_iso($pengaduan->tgl_pengaduan) ?>"><?= tanggal($pengaduan->tgl_pengaduan, $untuk !== 'publik') ?></time>
        </div>
        <p class="riwayat__oleh mb-0">Laporan dikirim<?= $untuk === 'petugas' ? ' oleh ' . esc(nama_pelapor($pengaduan, $identitasTerbuka)) : '' ?></p>
    </li>
</ol>
