<?php
$denganPelapor ??= false;
$tautan ??= null;
$untuk = $denganPelapor ? 'petugas' : 'warga';
?>
<div class="tabel-wadah">
    <table class="tabel tabel--kartu">
        <caption class="sr-only">Daftar laporan</caption>
        <thead>
            <tr>
                <th scope="col">No.</th>
                <th scope="col">Nomor laporan</th>
                <th scope="col">Tanggal</th>
                <?php if ($denganPelapor): ?><th scope="col">Pelapor</th><?php endif ?>
                <th scope="col">Jenis &amp; wilayah</th>
                <th scope="col">Kronologi</th>
                <th scope="col">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($laporan as $i => $p): ?>
                <tr>
                    <td data-label="No."><?= $i + 1 ?></td>
                    <td class="nowrap" data-label="Nomor"><?= $tautan !== null ? '<a href="' . esc($tautan($p), 'attr') . '">' . esc($p->nomor_laporan) . '</a>' : esc($p->nomor_laporan) ?></td>
                    <td class="nowrap" data-label="Tanggal"><?= tanggal($p->tgl_pengaduan, false) ?></td>
                    <?php if ($denganPelapor): ?><td data-label="Pelapor"><?= esc(nama_pelapor($p)) ?></td><?php endif ?>
                    <td data-label="Jenis"><?= esc($p->kategori ?? '–') ?><br><?= esc($p->provinsi ?? '–') ?></td>
                    <td data-label="Isi"><?= esc(penggalan($p->isi_laporan, 120)) ?></td>
                    <td data-label="Status"><?= status_badge($p->status(), $untuk) ?></td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>
