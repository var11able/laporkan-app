<?php?>
<?= $this->extend('layouts/petugas') ?>
<?= $this->section('konten') ?>

<div class="halaman-judul baris baris--antara">
    <h1 class="mb-0">Kabupaten/Kota</h1>
    <a class="tombol tombol--utama" href="<?= url_to('petugas.kabupaten_kota.baru') ?>"><?= ikon('plus') ?> Tambah kabupaten/kota</a>
</div>

<form class="filter" method="get" action="<?= url_to('petugas.kabupaten_kota') ?>" data-auto-submit>
    <?= komponen('components/field', ['nama' => 'provinsi', 'label' => 'Provinsi', 'tipe' => 'select', 'opsi' => $opsiProvinsi, 'kosong' => 'Semua provinsi', 'nilai' => $idProvinsi ?: '']) ?>
    <noscript><div><button type="submit" class="tombol"><?= ikon('filter') ?> Tampilkan</button></div></noscript>
</form>

<?php if ($kabupaten === []): ?>
    <div class="kosong"><?= ikon('map-pin') ?><p>Belum ada kabupaten/kota.</p></div>
<?php else: ?>
    <p class="teks-kecil teks-muted"><?= count($kabupaten) ?> kabupaten/kota</p>
    <div class="tabel-wadah">
        <table class="tabel">
            <thead><tr><th scope="col">Kabupaten/kota</th><th scope="col">Provinsi</th><th scope="col" class="kolom-aksi"><span class="sr-only">Aksi</span></th></tr></thead>
            <tbody>
                <?php foreach ($kabupaten as $k): ?>
                    <tr>
                        <td class="tebal"><?= esc($k['kabupaten_kota']) ?></td>
                        <td><?= esc($k['provinsi']) ?></td>
                        <td class="kolom-aksi">
                            <a class="tombol tombol--kecil" href="<?= url_to('petugas.kabupaten_kota.ubah', $k['id_kabupaten_kota']) ?>"><?= ikon('edit') ?> Ubah<span class="sr-only"> <?= esc($k['kabupaten_kota']) ?></span></a>
                            <?= komponen('components/tombol_hapus', [
                                'aksi'       => site_url('petugas/kabupaten-kota/' . $k['id_kabupaten_kota']),
                                'konfirmasi' => $k['kabupaten_kota'] . ' akan dihapus. Laporan di wilayah ini tetap ada, tetapi wilayahnya menjadi kosong.',
                            ]) ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
<?php endif ?>

<?= $this->endSection() ?>
