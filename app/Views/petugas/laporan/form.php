<?php$baru = $pengaduan === null;
$aksi = $baru ? site_url('petugas/laporan') : url_to('petugas.laporan.detail', $pengaduan->id_pengaduan);
?>
<?= $this->extend('layouts/petugas') ?>
<?= $this->section('konten') ?>

<div class="halaman-judul">
    <a class="kembali" href="<?= $baru ? url_to('petugas.laporan') : url_to('petugas.laporan.detail', $pengaduan->id_pengaduan) ?>"><?= ikon('chevron-left') ?> Kembali</a>
    <h1 style="font-size:1.75rem"><?= esc($judul) ?></h1>
    <?php if ($baru): ?><p class="lead mb-0">Untuk laporan yang diterima lewat telepon, surat, atau datang langsung.</p><?php endif ?>
</div>

<?= komponen('components/ringkasan_error') ?>

<form action="<?= $aksi ?>" method="post" enctype="multipart/form-data" class="form" novalidate>
    <?= csrf_field() ?>
    <?php if (! $baru): ?><input type="hidden" name="_method" value="PUT"><?php endif ?>

    <?php if ($identitasTerbuka): ?>
        <?= komponen('components/field', [
            'nama' => 'id_masyarakat', 'label' => 'Pelapor', 'tipe' => 'select', 'opsi' => $opsiWarga, 'kosong' => 'Pilih pelapor',
            'hint' => 'Belum terdaftar? Tambahkan dulu di menu Masyarakat.', 'nilai' => (string) ($pengaduan?->id_masyarakat ?? ''),
        ]) ?>
    <?php else: ?>
        <div class="field">
            <span class="tebal">Pelapor</span>
            <p class="mb-0"><?= ikon('lock') ?> Dirahasiakan. Pelapor tidak dapat diganti tanpa membuka identitasnya.</p>
        </div>
    <?php endif ?>

    <?= komponen('components/form_laporan', [
        'pengaduan'     => $pengaduan,
        'bukti'         => $bukti,
        'urlBukti'      => $baru ? null : static fn (array $b): string => url_to('petugas.laporan.bukti', $pengaduan->id_pengaduan, $b['id_bukti']),
        'opsiKategori'  => $opsiKategori,
        'opsiProvinsi'  => $opsiProvinsi,
        'opsiKabupaten' => $opsiKabupaten,
    ]) ?>

    <div class="tombol-grup">
        <button type="submit" class="tombol tombol--utama"><?= $baru ? 'Buat laporan' : 'Simpan perubahan' ?></button>
        <a class="tombol" href="<?= $baru ? url_to('petugas.laporan') : url_to('petugas.laporan.detail', $pengaduan->id_pengaduan) ?>">Batal</a>
    </div>
</form>

<?= $this->endSection() ?>
