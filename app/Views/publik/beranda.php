<?php
$angka = static fn (int $n): string => number_format($n, 0, ',', '.');
$srcset = static fn (string $kunci, int $rasioT): string => implode(', ', array_map(
    static fn (int $w): string => foto_stok($kunci, $w, (int) round($w * $rasioT / 100)) . ' ' . $w . 'w',
    [640, 1000, 1600],
));
$langkah = [
    ['jenis' => 'hp', 'judul' => 'Laporkan', 'teks' => 'Pilih jenis dugaan korupsi, ceritakan kejadiannya, dan lampirkan bukti bila ada. Anda bisa merahasiakan identitas dan langsung mendapat nomor laporan.'],
    ['jenis' => 'web', 'judul' => 'Admin memverifikasi dan meneruskan', 'teks' => 'Admin memeriksa laporan tanpa melihat identitas Anda, lalu meneruskannya ke instansi yang berwenang, seperti KPK, kejaksaan, kepolisian, atau inspektorat.'],
    ['jenis' => 'hp', 'judul' => 'Pantau perkembangannya', 'teks' => 'Lihat setiap perkembangan seperti melacak paket. Setiap perubahan status langsung terlihat dengan nomor laporan Anda.'],
    ['jenis' => 'web', 'judul' => 'Tampil tanpa identitas', 'teks' => 'Setelah diverifikasi, ringkasan tanpa nama pelapor tampil di halaman publik dan ikut mewarnai peta sebaran, untuk meningkatkan kesadaran bersama.'],
];
?>
<?= $this->extend('layouts/publik') ?>
<?= $this->section('konten') ?>

<section class="hero" aria-labelledby="judul-beranda">
    <div class="hero__bingkai">
        <img class="hero__gambar" src="<?= esc(foto_stok('hero', 1000, 700), 'attr') ?>" srcset="<?= esc($srcset('hero', 70), 'attr') ?>" sizes="100vw" alt="" width="1600" height="1120" fetchpriority="high">
        <div class="hero__isi">
            <span class="hero__label"><?= ikon('info') ?> Demo portofolio · Semua data fiktif</span>
            <h1 id="judul-beranda" class="hero__judul">
                <span>Berani melapor.</span>
                <span>Dijaga kerahasiaannya.</span>
                <span>Dikawal sampai tuntas.</span>
            </h1>
            <div class="hero__bawah">
                <div class="hero__teks">
                    <p class="hero__lead">Laporkan pungutan liar, suap, atau penyalahgunaan anggaran. Identitas Anda kami jaga, dan laporan diteruskan ke instansi yang berwenang.</p>
                    <div class="hero__aksi">
                        <a class="tombol tombol--lapor tombol--besar tombol--penuh-mobile" href="<?= url_to('warga.laporan.baru') ?>"><?= ikon('plus') ?> Buat Laporan</a>
                        <form class="lacak-cepat" action="<?= url_to('lacak') ?>" method="get" role="search">
                            <label for="nomor">Sudah melapor? Lacak dengan nomor laporan</label>
                            <div class="lacak-cepat__baris">
                                <input type="search" id="nomor" name="nomor" placeholder="LP-2026-000123" autocomplete="off" autocapitalize="characters" spellcheck="false">
                                <button type="submit" class="tombol tombol--terang"><?= ikon('arrow-right') ?><span class="sr-only">Lacak</span></button>
                            </div>
                        </form>
                    </div>
                </div>
                <p class="hero__merek" aria-hidden="true">LaporKan</p>
            </div>
        </div>
    </div>
</section>

<section class="suara" aria-labelledby="judul-suara">
    <div class="wadah">
        <h2 id="judul-suara" class="judul-besar"><span>Suara warga</span> <span>dari Sabang sampai Merauke</span></h2>
        <dl class="kartu-angka-grid">
            <?= komponen('components/kartu_angka', ['label' => 'Laporan bulan ini', 'ikon' => 'inbox', 'nilai' => $angka($statistik['bulanIni']), 'nilaiAngka' => $statistik['bulanIni']]) ?>
            <?= komponen('components/kartu_angka', ['label' => 'Diteruskan ke instansi', 'ikon' => 'send', 'nilai' => $angka($statistik['diteruskan']), 'nilaiAngka' => $statistik['diteruskan']]) ?>
            <?= komponen('components/kartu_angka', ['label' => 'Ditindaklanjuti', 'ikon' => 'check-circle', 'nilai' => $angka($statistik['selesai']), 'nilaiAngka' => $statistik['selesai']]) ?>
            <?= komponen('components/kartu_angka', ['label' => 'Provinsi terjangkau', 'ikon' => 'map-pin', 'nilai' => $angka($statistik['terjangkau']), 'nilaiAngka' => $statistik['terjangkau'], 'akhiran' => '/' . $statistik['provinsi']]) ?>
        </dl>
        <p class="catatan-demo"><?= ikon('info') ?> Angka, peta, dan laporan di halaman ini berasal dari data contoh untuk keperluan demo, bukan statistik nyata.</p>
    </div>
    <?= komponen('components/peta_sebaran', ['peta' => $peta, 'id' => 'peta-beranda', 'judul' => 'Laporan dari seluruh Indonesia']) ?>
</section>

<section class="section" aria-labelledby="judul-cara">
    <div class="wadah">
        <div class="section__judul"><h2 id="judul-cara" class="judul-besar">Cara kerjanya</h2></div>
        <p class="lead cara__ajakan">Empat langkah dari laporan Anda sampai tampil di publik. Animasinya memakai tampilan aplikasi yang sebenarnya, dengan data fiktif.</p>
        <ol class="cara">
            <?php foreach ($langkah as $i => $l): ?>
                <li class="cara__baris">
                    <div class="cara__video cara__video--<?= $l['jenis'] ?>">
                        <video data-video-otomatis muted playsinline loop preload="none" aria-hidden="true"
                               width="<?= $l['jenis'] === 'hp' ? 580 : 1206 ?>" height="<?= $l['jenis'] === 'hp' ? 1040 : 850 ?>"
                               poster="<?= esc(aset('video/fase-' . ($i + 1) . '.webp'), 'attr') ?>">
                            <source src="<?= esc(aset('video/fase-' . ($i + 1) . '.webm'), 'attr') ?>" type="video/webm">
                            <source src="<?= esc(aset('video/fase-' . ($i + 1) . '.mp4'), 'attr') ?>" type="video/mp4">
                        </video>
                        <button type="button" class="tombol tombol--kaca tombol--ikon cara__putar" data-video-alih aria-label="Jeda animasi langkah <?= $i + 1 ?>" hidden><?= ikon('pause', 'cara__ikon-jeda') ?><?= ikon('play', 'cara__ikon-putar') ?></button>
                    </div>
                    <div class="cara__teks">
                        <span class="cara__nomor" aria-hidden="true"><?= sprintf('%02d', $i + 1) ?></span>
                        <h3><?= esc($l['judul']) ?></h3>
                        <p class="teks-muted"><?= esc($l['teks']) ?></p>
                    </div>
                </li>
            <?php endforeach ?>
        </ol>
    </div>
</section>

<section class="section" aria-labelledby="judul-kenali">
    <div class="wadah">
        <div class="section__judul">
            <h2 id="judul-kenali" class="judul-besar">Kenali korupsi</h2>
            <a class="tautan-panah" href="<?= url_to('tentang_korupsi') ?>">Pelajari selengkapnya <?= ikon('arrow-up-right') ?></a>
        </div>
        <p class="lead">Korupsi adalah penyalahgunaan kekuasaan yang dipercayakan untuk keuntungan pribadi. Bentuknya bisa terjadi di sekitar kita, antara lain:</p>
        <ul class="jenis-korupsi">
            <?php foreach ($kategori as $k): ?>
                <?php if ($k['kategori'] !== 'Lainnya'): ?>
                    <li><strong><?= esc($k['kategori']) ?></strong><span class="teks-muted"><?= esc((string) $k['keterangan']) ?></span></li>
                <?php endif ?>
            <?php endforeach ?>
        </ul>
    </div>
</section>

<section class="section" aria-labelledby="judul-terbaru">
    <div class="wadah">
        <div class="section__judul">
            <h2 id="judul-terbaru" class="judul-besar">Laporan terverifikasi terbaru</h2>
            <a class="tautan-panah" href="<?= url_to('laporan.publik') ?>">Lihat semua <?= ikon('arrow-up-right') ?></a>
        </div>
        <?php if ($terbaru === []): ?>
            <div class="kosong">
                <img class="kosong__foto" src="<?= esc(foto_stok('dokumen', 480, 320), 'attr') ?>" alt="" loading="lazy" width="480" height="320">
                <p>Belum ada laporan yang diverifikasi. Laporan tampil di sini setelah diperiksa admin, tanpa identitas pelapor.</p>
            </div>
        <?php else: ?>
            <div class="grid grid--3">
                <?php foreach ($terbaru as $p): ?>
                    <?= komponen('components/kartu_laporan', ['p' => $p]) ?>
                <?php endforeach ?>
            </div>
        <?php endif ?>
    </div>
</section>

<section class="penutup" aria-labelledby="judul-penutup">
    <img class="penutup__gambar" src="<?= esc(foto_stok('dokumen', 1200, 500), 'attr') ?>" alt="" loading="lazy" width="1200" height="500">
    <div class="wadah penutup__isi">
        <h2 id="judul-penutup">Satu laporan Anda ikut membangun Indonesia yang bebas korupsi.</h2>
        <a class="tombol tombol--lapor tombol--besar" href="<?= url_to('warga.laporan.baru') ?>"><?= ikon('plus') ?> Buat Laporan</a>
    </div>
</section>

<?= $this->endSection() ?>
