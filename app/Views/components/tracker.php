<?php
use App\Enums\StatusPengaduan;

$untuk ??= 'warga';
$status     = $pengaduan->status();
$terakhir   = $tanggapan === [] ? null : $tanggapan[array_key_last($tanggapan)];
$alur       = StatusPengaduan::alur();
$posisi     = array_search($status, $alur, true);
$diperbarui = $terakhir?->tgl_tanggapan ?? $pengaduan->tgl_pengaduan;
$tujuan     = null;

foreach ($tanggapan as $t) {
    $tujuan = $t->instansi_tujuan ?: $tujuan;
}
?>
<section class="tracker tracker--<?= $status->slug() ?>" aria-labelledby="tracker-judul">
    <div class="tracker__kepala">
        <span class="tracker__ikon" aria-hidden="true"><?= ikon($status->ikon()) ?></span>
        <div>
            <h2 class="tracker__judul" id="tracker-judul"><?= esc($untuk === 'petugas' ? $status->labelPetugas() : $status->labelWarga()) ?></h2>
            <?php if ($untuk === 'petugas'): ?>
                <p class="tracker__penjelasan"><?= $status->transisiBerikutnya() === [] ? 'Laporan sudah selesai diproses.' : 'Langkah berikutnya: ' . esc(implode(' atau ', array_map(static fn (StatusPengaduan $s) => $s->labelPetugas(), $status->transisiBerikutnya()))) . '.' ?></p>
            <?php else: ?>
                <p class="tracker__penjelasan"><?= esc($status->penjelasan()) ?></p>
            <?php endif ?>
            <?php if ($tujuan !== null): ?>
                <p class="tracker__penjelasan"><?= ikon('send') ?> Diteruskan ke <strong><?= esc($tujuan) ?></strong></p>
            <?php endif ?>
            <p class="tracker__update mb-0">Terakhir diperbarui: <time datetime="<?= tanggal_iso($diperbarui) ?>" title="<?= esc(tanggal($diperbarui), 'attr') ?>"><?= esc(waktu_relatif($diperbarui)) ?></time></p>
        </div>
    </div>

    <?php if ($status === StatusPengaduan::TidakValid): ?>
        <?php if ($untuk === 'publik'): ?>
            <p class="teks-kecil teks-muted" style="margin: var(--space-3) 0 0">Pelapor dapat membaca alasannya dengan masuk ke akunnya.</p>
        <?php else: ?>
            <div class="alasan">
                <p class="tebal">Alasan dari admin</p>
                <p><?= esc($terakhir?->isi_tanggapan ?? '–') ?></p>
            </div>
            <?php if ($untuk === 'warga'): ?>
                <p class="teks-kecil teks-muted" style="margin: var(--space-3) 0 0">Anda dapat mengirim laporan baru dengan kronologi atau bukti yang lebih lengkap.</p>
            <?php endif ?>
        <?php endif ?>
    <?php else: ?>
        <ol class="tahap" aria-label="Tahapan laporan">
            <?php foreach ($alur as $i => $tahap): ?>
                <?php $atribut = $i < $posisi ? ' data-lewat' : ($i === $posisi ? ' data-kini aria-current="step"' : ''); ?>
                <li<?= $atribut ?>>
                    <span class="tahap__titik" aria-hidden="true"></span>
                    <?= esc($tahap->labelTahap()) ?>
                    <?php if ($i < $posisi): ?><span class="sr-only">(selesai)</span><?php endif ?>
                </li>
            <?php endforeach ?>
        </ol>
    <?php endif ?>
</section>
