<?= $this->extend('layouts/petugas') ?>
<?= $this->section('konten') ?>

<div class="halaman-judul">
    <h1>Log Aktivitas</h1>
    <p class="lead mb-0">Semua tindakan admin, termasuk membuka identitas pelapor dan percobaan akses yang ditolak.</p>
</div>

<?php if ($log === []): ?>
    <div class="kosong"><?= ikon('activity') ?><p>Belum ada aktivitas.</p></div>
<?php else: ?>
    <div class="tabel-wadah">
        <table class="tabel">
            <thead><tr><th scope="col">Waktu</th><th scope="col">Admin</th><th scope="col">Aktivitas</th></tr></thead>
            <tbody>
                <?php foreach ($log as $l): ?>
                    <tr>
                        <td class="nowrap"><?= tanggal($l['tgl_log']) ?></td>
                        <td><?= $l['nama'] !== null ? esc($l['nama']) . ' <span class="teks-muted">(' . esc($l['username']) . ')</span>' : '<span class="teks-muted">Akun sudah dihapus</span>' ?></td>
                        <td<?= str_starts_with((string) $l['isi_log'], 'Ditolak') ? ' class="umur--lama"' : '' ?>><?= esc(html_entity_decode((string) $l['isi_log'], ENT_QUOTES)) ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
    <?= $pager->links('default', 'laporkan') ?>
<?php endif ?>

<?= $this->endSection() ?>
