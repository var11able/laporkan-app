<?php
use App\Enums\StatusPengaduan;

$opsiStatus = [];
foreach (StatusPengaduan::cases() as $s) {
    $opsiStatus[$s->value] = ($untuk ?? 'warga') === 'petugas' ? $s->labelPetugas() : $s->labelWarga();
}
?>
<form class="filter" method="get" action="<?= esc($aksi, 'attr') ?>">
    <?= komponen('components/field', ['nama' => 'dari', 'label' => 'Dari tanggal', 'tipe' => 'date', 'nilai' => $filter['dari']]) ?>
    <?= komponen('components/field', ['nama' => 'sampai', 'label' => 'Sampai tanggal', 'tipe' => 'date', 'nilai' => $filter['sampai']]) ?>
    <?= komponen('components/field', ['nama' => 'status', 'label' => 'Status', 'tipe' => 'select', 'opsi' => $opsiStatus, 'kosong' => 'Semua status', 'nilai' => $filter['status']]) ?>
    <?php if (! empty($opsiKategori)): ?>
        <?= komponen('components/field', ['nama' => 'kategori', 'label' => 'Jenis', 'tipe' => 'select', 'opsi' => $opsiKategori, 'kosong' => 'Semua jenis', 'nilai' => $filter['kategori']]) ?>
    <?php endif ?>
    <?php if (! empty($opsiProvinsi)): ?>
        <?= komponen('components/field', ['nama' => 'provinsi', 'label' => 'Provinsi', 'tipe' => 'select', 'opsi' => $opsiProvinsi, 'kosong' => 'Semua provinsi', 'nilai' => $filter['provinsi']]) ?>
    <?php endif ?>
    <div><button type="submit" class="tombol"><?= ikon('filter') ?> Tampilkan</button></div>
</form>
