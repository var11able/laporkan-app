<?php
$url ??= url_to('laporan.detail', $p->nomor_laporan);
?>
<article class="kartu-laporan">
    <div class="kartu-laporan__foto">
        <img src="<?= esc(foto_pengganti((int) $p->id_pengaduan), 'attr') ?>" alt="" loading="lazy" width="480" height="360">
        <?= status_badge($p->status()) ?>
        <span class="kartu-laporan__ilustrasi">Foto ilustrasi</span>
    </div>
    <div class="kartu-laporan__isi">
        <?php if ($p->kategori !== null): ?><p class="kartu-laporan__jenis mb-0"><?= esc($p->kategori) ?></p><?php endif ?>
        <h3 class="kartu-laporan__judul">
            <a href="<?= esc($url, 'attr') ?>" class="potong-2"><?= esc((string) $p->ringkasan_publik) ?></a>
        </h3>
        <div class="kartu-laporan__meta">
            <?php if ($p->provinsi !== null): ?><span><?= ikon('map-pin') ?> <?= esc($p->provinsi) ?></span><?php endif ?>
            <span><?= ikon('clock') ?> <time datetime="<?= tanggal_iso($p->tgl_pengaduan) ?>"><?= esc(waktu_relatif($p->tgl_pengaduan)) ?></time></span>
        </div>
    </div>
</article>
