<?php$maksMinggu  = max(1, ...array_column($perMinggu, 'jumlah'));
$lebarSvg    = 560;
$tinggiSvg   = 180;
$lebarBatang = $lebarSvg / count($perMinggu);
$jam         = (int) date('G');
$sapaan      = $jam < 11 ? 'Selamat pagi' : ($jam < 15 ? 'Selamat siang' : ($jam < 18 ? 'Selamat sore' : 'Selamat malam'));
?>
<?= $this->extend('layouts/petugas') ?>
<?= $this->section('konten') ?>

<div class="halaman-judul">
    <h1><?= $sapaan ?>, <?= esc(explode(' ', $petugas->nama)[0]) ?></h1>
    <p class="teks-muted mb-0"><?= tanggal(date('Y-m-d'), false) ?> · <?= $angka['baru'] > 0 ? '<strong>' . $angka['baru'] . ' laporan baru</strong> menunggu ditinjau.' : 'Tidak ada laporan baru yang menunggu.' ?></p>
</div>

<div class="angka-baris angka-baris--panel">
    <a class="angka angka--peringatan" href="<?= url_to('petugas.laporan') ?>?status=belum_ditanggapi">
        <span class="angka__ikon" aria-hidden="true"><?= ikon('inbox') ?></span>
        <span class="angka__nilai"><?= $angka['baru'] ?></span>
        <span class="angka__label">Baru, menunggu diperiksa</span>
    </a>
    <div class="angka angka--info">
        <span class="angka__ikon" aria-hidden="true"><?= ikon('send') ?></span>
        <span class="angka__nilai"><?= $angka['diproses'] ?></span>
        <span class="angka__label">Diverifikasi atau diteruskan</span>
    </div>
    <div class="angka angka--sukses">
        <span class="angka__ikon" aria-hidden="true"><?= ikon('check-circle') ?></span>
        <span class="angka__nilai"><?= $angka['selesaiBulanIni'] ?></span>
        <span class="angka__label">Ditindaklanjuti bulan ini</span>
    </div>
    <div class="angka">
        <span class="angka__ikon" aria-hidden="true"><?= ikon('clock') ?></span>
        <span class="angka__nilai"><?= $angka['rataHari'] === null ? '–' : number_format($angka['rataHari'], 1, ',', '.') ?></span>
        <span class="angka__label">Rata-rata hari penanganan</span>
    </div>
</div>

<div class="dua-kolom" style="margin-bottom: var(--space-5)">
    <section class="blok" aria-labelledby="judul-mingguan">
        <h2 id="judul-mingguan" class="blok__judul">Laporan masuk per minggu</h2>
        <svg class="grafik" viewBox="0 0 <?= $lebarSvg ?> <?= $tinggiSvg + 28 ?>" role="img" aria-labelledby="judul-mingguan grafik-desc">
            <desc id="grafik-desc"><?= esc(implode('; ', array_map(static fn ($m) => 'minggu ' . tanggal($m['mulai'], false) . ': ' . $m['jumlah'] . ' laporan', $perMinggu))) ?></desc>
            <line class="grafik__garis" x1="0" y1="<?= $tinggiSvg ?>" x2="<?= $lebarSvg ?>" y2="<?= $tinggiSvg ?>"/>
            <?php foreach ($perMinggu as $i => $m): ?>
                <?php
                $tinggi = max($m['jumlah'] > 0 ? 4 : 0, (int) round(($m['jumlah'] / $maksMinggu) * ($tinggiSvg - 24)));
                $x      = $i * $lebarBatang + $lebarBatang * 0.22;
                $lebar  = $lebarBatang * 0.56;
                ?>
                <rect class="grafik__batang<?= $i === count($perMinggu) - 1 ? ' grafik__batang--kini' : '' ?>" x="<?= $x ?>" y="<?= $tinggiSvg - $tinggi ?>" width="<?= $lebar ?>" height="<?= $tinggi ?>" rx="6"/>
                <text class="grafik__nilai" x="<?= $x + $lebar / 2 ?>" y="<?= $tinggiSvg - $tinggi - 6 ?>" text-anchor="middle"><?= $m['jumlah'] ?></text>
                <text class="grafik__teks" x="<?= $x + $lebar / 2 ?>" y="<?= $tinggiSvg + 20 ?>" text-anchor="middle"><?= $i === count($perMinggu) - 1 ? 'Minggu ini' : date('d/m', strtotime($m['mulai'])) ?></text>
            <?php endforeach ?>
        </svg>
    </section>

    <?= komponen('components/sebaran', ['id' => 'judul-jenis', 'judul' => 'Sebaran per jenis dugaan korupsi', 'baris' => $perKategori]) ?>
</div>

<div style="margin-bottom: var(--space-5)">
    <?= komponen('components/sebaran', ['id' => 'judul-provinsi', 'judul' => 'Sebaran per provinsi', 'baris' => $perProvinsi]) ?>
</div>

<section class="blok" aria-labelledby="judul-menunggu">
    <div class="section__judul" style="margin-bottom: var(--space-3)">
        <h2 id="judul-menunggu" class="blok__judul mb-0">Menunggu paling lama</h2>
        <a href="<?= url_to('petugas.laporan') ?>?status=belum_ditanggapi">Lihat semua <?= ikon('arrow-right') ?></a>
    </div>
    <?php if ($menunggu === []): ?>
        <div class="kosong"><div class="kosong__ikon"><?= ikon('check-circle') ?></div><p>Semua laporan baru sudah ditinjau. Kerja bagus!</p></div>
    <?php else: ?>
        <ul class="daftar-laporan">
            <?php foreach ($menunggu as $p): ?>
                <li>
                    <div class="daftar-laporan__foto">
                        <?= ikon('inbox') ?>
                    </div>
                    <div>
                        <h3 class="daftar-laporan__judul potong-2"><a href="<?= url_to('petugas.laporan.detail', $p->id_pengaduan) ?>"><?= esc($p->isi_laporan) ?></a></h3>
                        <div class="daftar-laporan__meta">
                            <span class="angka-tabular"><?= esc($p->nomor_laporan) ?></span>
                            <?php if ($p->kategori !== null): ?><span><?= esc($p->kategori) ?></span><?php endif ?>
                            <?php if ($p->provinsi !== null): ?><span><?= ikon('map-pin') ?> <?= esc($p->provinsi) ?></span><?php endif ?>
                            <span class="<?= $p->melewatiBatas() ? 'umur--lama' : '' ?>"><?= ikon($p->melewatiBatas() ? 'alert' : 'clock') ?> Menunggu <?= umur_laporan($p->umurHari()) ?></span>
                        </div>
                    </div>
                </li>
            <?php endforeach ?>
        </ul>
    <?php endif ?>
</section>

<?= $this->endSection() ?>
