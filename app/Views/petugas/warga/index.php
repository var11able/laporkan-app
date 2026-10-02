<?php?>
<?= $this->extend('layouts/petugas') ?>
<?= $this->section('konten') ?>

<div class="halaman-judul baris baris--antara">
    <h1 class="mb-0">Masyarakat</h1>
    <a class="tombol tombol--utama" href="<?= url_to('petugas.warga.baru') ?>"><?= ikon('plus') ?> Tambah akun</a>
</div>

<form class="filter" method="get" action="<?= url_to('petugas.warga') ?>" role="search">
    <?= komponen('components/field', ['nama' => 'q', 'label' => 'Cari nama, username, atau telepon', 'tipe' => 'search', 'nilai' => $q]) ?>
    <div><button type="submit" class="tombol"><?= ikon('search') ?> Cari</button></div>
</form>

<?php if ($warga === []): ?>
    <div class="kosong"><?= ikon('users') ?><p>Tidak ada akun masyarakat yang cocok.</p></div>
<?php else: ?>
    <div class="tabel-wadah">
        <table class="tabel">
            <thead><tr><th scope="col">Nama</th><th scope="col">Username</th><th scope="col">Telepon</th><th scope="col">Alamat</th><?php if ($petugas->isAdmin()): ?><th scope="col">Laporan</th><?php endif ?><th scope="col" class="kolom-aksi"><span class="sr-only">Aksi</span></th></tr></thead>
            <tbody>
                <?php foreach ($warga as $w): ?>
                    <tr>
                        <td class="tebal"><?= esc($w->nama) ?></td>
                        <td><?= esc($w->username) ?></td>
                        <td class="nowrap"><?= esc($w->no_telepon) ?></td>
                        <td><?= esc(penggalan($w->alamat, 60)) ?></td>
                        <?php  ?>
                        <?php if ($petugas->isAdmin()): ?><td><?= (int) $w->jumlah_laporan ?></td><?php endif ?>
                        <td class="kolom-aksi">
                            <a class="tombol tombol--kecil" href="<?= url_to('petugas.warga.ubah', $w->id_masyarakat) ?>"><?= ikon('edit') ?> Ubah<span class="sr-only"> <?= esc($w->nama) ?></span></a>
                            <?php if ($petugas->isAdmin()): ?>
                                <?= komponen('components/tombol_hapus', [
                                    'aksi'       => site_url('petugas/warga/' . $w->id_masyarakat),
                                    'konfirmasi' => 'Akun ' . $w->username . ' beserta ' . (int) $w->jumlah_laporan . ' laporannya akan dihapus permanen.',
                                ]) ?>
                            <?php endif ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
    <?= $pager->links('default', 'laporkan') ?>
<?php endif ?>

<?= $this->endSection() ?>
