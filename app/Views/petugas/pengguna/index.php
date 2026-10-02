<?php?>
<?= $this->extend('layouts/petugas') ?>
<?= $this->section('konten') ?>

<div class="halaman-judul baris baris--antara">
    <h1 class="mb-0">Akun Admin</h1>
    <a class="tombol tombol--utama" href="<?= url_to('petugas.pengguna.baru') ?>"><?= ikon('plus') ?> Tambah petugas</a>
</div>

<div class="banner"><?= ikon('info') ?><div class="banner__isi"><p><strong>Administrator</strong> dapat menghapus laporan dan tanggapan, mengelola akun admin, membuka identitas pelapor yang dirahasiakan, dan melihat log aktivitas. <strong>Operator</strong> dapat menanggapi laporan dan mengelola data wilayah dan akun masyarakat, tetapi tidak dapat melihat pelapor yang dirahasiakan.</p></div></div>

<div class="tabel-wadah">
    <table class="tabel">
        <thead><tr><th scope="col">Nama</th><th scope="col">Username</th><th scope="col">Telepon</th><th scope="col">Jabatan</th><th scope="col" class="kolom-aksi"><span class="sr-only">Aksi</span></th></tr></thead>
        <tbody>
            <?php foreach ($pengguna as $u): ?>
                <tr>
                    <td class="tebal"><?= esc($u->nama) ?><?= $u->getId() === $petugas->getId() ? ' <span class="teks-muted">(Anda)</span>' : '' ?></td>
                    <td><?= esc($u->username) ?></td>
                    <td class="nowrap"><?= esc($u->no_telepon) ?></td>
                    <td><?= esc($u->jabatan()->label()) ?></td>
                    <td class="kolom-aksi">
                        <a class="tombol tombol--kecil" href="<?= url_to('petugas.pengguna.ubah', $u->id_user) ?>"><?= ikon('edit') ?> Ubah<span class="sr-only"> <?= esc($u->nama) ?></span></a>
                        <?php if ($u->getId() !== $petugas->getId()): ?>
                            <?= komponen('components/tombol_hapus', ['aksi' => site_url('petugas/pengguna/' . $u->id_user), 'konfirmasi' => 'Akun admin ' . $u->username . ' akan dihapus.']) ?>
                        <?php endif ?>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>

<?= $this->endSection() ?>
