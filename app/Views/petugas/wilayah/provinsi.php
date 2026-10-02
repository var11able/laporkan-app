<?php?>
<?= $this->extend('layouts/petugas') ?>
<?= $this->section('konten') ?>

<div class="halaman-judul baris baris--antara">
    <div>
        <h1 class="mb-0">Provinsi</h1>
        <p class="teks-muted mb-0">Sesuai Kepmendagri No. 300.2.2-2430 Tahun 2025.</p>
    </div>
    <a class="tombol tombol--utama" href="<?= url_to('petugas.provinsi.baru') ?>"><?= ikon('plus') ?> Tambah provinsi</a>
</div>

<?php if ($provinsi === []): ?>
    <div class="kosong"><?= ikon('layers') ?><p>Belum ada provinsi. Jalankan <code>php spark db:seed WilayahSeeder</code>.</p></div>
<?php else: ?>
    <div class="tabel-wadah">
        <table class="tabel">
            <thead><tr><th scope="col">Provinsi</th><th scope="col">Jumlah kabupaten/kota</th><th scope="col" class="kolom-aksi"><span class="sr-only">Aksi</span></th></tr></thead>
            <tbody>
                <?php foreach ($provinsi as $p): ?>
                    <tr>
                        <td class="tebal"><?= esc($p['provinsi']) ?></td>
                        <td><a href="<?= url_to('petugas.kabupaten_kota') ?>?provinsi=<?= (int) $p['id_provinsi'] ?>"><?= (int) $p['jumlah_kabupaten_kota'] ?></a></td>
                        <td class="kolom-aksi">
                            <a class="tombol tombol--kecil" href="<?= url_to('petugas.provinsi.ubah', $p['id_provinsi']) ?>"><?= ikon('edit') ?> Ubah<span class="sr-only"> <?= esc($p['provinsi']) ?></span></a>
                            <?= komponen('components/tombol_hapus', [
                                'aksi'       => site_url('petugas/provinsi/' . $p['id_provinsi']),
                                'konfirmasi' => 'Provinsi ' . $p['provinsi'] . ' akan dihapus.',
                            ]) ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
<?php endif ?>

<?= $this->endSection() ?>
