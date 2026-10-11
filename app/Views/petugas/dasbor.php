<?php
use App\Enums\StatusLaporan;

$maksMinggu  = max(1, ...array_column($perMinggu, 'jumlah'));
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

<dl class="kartu-angka-grid">
    <?= komponen('components/kartu_angka', ['label' => 'Baru, menunggu diperiksa', 'ikon' => 'inbox', 'nilai' => $angka['baru'], 'url' => url_to('petugas.laporan') . '?status=belum_ditanggapi', 'nada' => $angka['baru'] > 0 ? 'peringatan' : null]) ?>
    <?= komponen('components/kartu_angka', ['label' => 'Diverifikasi atau diteruskan', 'ikon' => 'send', 'nilai' => $angka['diproses']]) ?>
    <?= komponen('components/kartu_angka', ['label' => 'Ditindaklanjuti bulan ini', 'ikon' => 'check-circle', 'nilai' => $angka['selesaiBulanIni']]) ?>
    <?= komponen('components/kartu_angka', ['label' => 'Rata-rata hari penanganan', 'ikon' => 'clock', 'nilai' => $angka['rataHari'] === null ? '–' : number_format($angka['rataHari'], 1, ',', '.')]) ?>
</dl>

<div class="dasbor-grid">
    <section class="kartu dasbor-grid__peta" aria-labelledby="judul-peta">
        <div class="section__judul">
            <h2 id="judul-peta" class="blok__judul mb-0">Sebaran per provinsi</h2>
            <nav class="tab tab--kecil mb-0" aria-label="Saring peta menurut status">
                <a href="<?= url_to('petugas.dasbor') ?>"<?= $statusPeta === null ? ' aria-current="page"' : '' ?>>Semua</a>
                <?php foreach (StatusLaporan::cases() as $s): ?>
                    <a href="<?= url_to('petugas.dasbor') ?>?peta=<?= $s->value ?>"<?= $statusPeta === $s ? ' aria-current="page"' : '' ?>><?= esc($s->labelPetugas()) ?></a>
                <?php endforeach ?>
            </nav>
        </div>
        <?= komponen('components/peta_sebaran', ['peta' => $peta, 'id' => 'peta-dasbor', 'mode' => 'datar', 'teratas' => 8]) ?>
    </section>

    <section class="kartu dasbor-grid__samping" aria-labelledby="judul-menunggu">
        <div class="section__judul">
            <h2 id="judul-menunggu" class="blok__judul mb-0">Menunggu paling lama</h2>
            <a class="tautan-panah" href="<?= url_to('petugas.laporan') ?>?status=belum_ditanggapi">Lihat semua <?= ikon('arrow-up-right') ?></a>
        </div>
        <?php if ($menunggu === []): ?>
            <div class="kosong"><div class="kosong__ikon"><?= ikon('check-circle') ?></div><p>Semua laporan baru sudah ditinjau. Kerja bagus!</p></div>
        <?php else: ?>
            <ul class="daftar-laporan daftar-laporan--ringkas">
                <?php foreach ($menunggu as $p): ?>
                    <li>
                        <div class="daftar-laporan__foto"><?= ikon('inbox') ?></div>
                        <div>
                            <h3 class="daftar-laporan__judul potong-2"><a href="<?= url_to('petugas.laporan.detail', $p->id_laporan) ?>"><?= esc($p->isi_laporan) ?></a></h3>
                            <div class="daftar-laporan__meta">
                                <span class="angka-tabular"><?= esc($p->nomor_laporan) ?></span>
                                <?php if ($p->provinsi !== null): ?><span><?= ikon('map-pin') ?> <?= esc($p->provinsi) ?></span><?php endif ?>
                                <span class="<?= $p->melewatiBatas() ? 'umur--lama' : '' ?>"><?= ikon($p->melewatiBatas() ? 'alert' : 'clock') ?> Menunggu <?= umur_laporan($p->umurHari()) ?></span>
                            </div>
                        </div>
                    </li>
                <?php endforeach ?>
            </ul>
        <?php endif ?>
    </section>

    <section class="kartu dasbor-grid__peta" aria-labelledby="judul-mingguan">
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

    <div class="kartu dasbor-grid__samping">
        <?= komponen('components/sebaran', ['id' => 'judul-jenis', 'judul' => 'Sebaran per jenis dugaan korupsi', 'baris' => $perKategori]) ?>
    </div>
</div>

<?= $this->endSection() ?>
