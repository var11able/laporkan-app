<?php$angka = static fn (int $n): string => number_format($n, 0, ',', '.');
$srcset = static fn (string $kunci, int $rasioT): string => implode(', ', array_map(
    static fn (int $w): string => foto_stok($kunci, $w, (int) round($w * $rasioT / 100)) . ' ' . $w . 'w',
    [640, 1000, 1600],
));
?>
<?= $this->extend('layouts/publik') ?>
<?= $this->section('konten') ?>

<section class="hero-foto" aria-labelledby="judul-beranda">
    <img class="hero-foto__gambar" src="<?= esc(foto_stok('hero', 1000, 700), 'attr') ?>" srcset="<?= esc($srcset('hero', 70), 'attr') ?>" sizes="100vw" alt="" width="1600" height="1120" fetchpriority="high">
    <div class="wadah hero-foto__isi">
        <span class="hero-foto__label"><?= ikon('shield') ?> Pelaporan kasus korupsi di Indonesia</span>
        <h1 id="judul-beranda">Melihat praktik korupsi? Laporkan di sini.</h1>
        <p class="lead">Pungutan liar, suap, atau penyalahgunaan anggaran. Identitas Anda kami jaga, laporan diteruskan ke instansi yang berwenang, dan perkembangannya bisa Anda pantau.</p>
        <div class="hero-foto__aksi">
            <a class="tombol tombol--utama tombol--besar tombol--penuh-mobile" href="<?= url_to('warga.laporan.baru') ?>"><?= ikon('plus') ?> Buat Laporan</a>
            <form class="lacak-cepat" action="<?= url_to('lacak') ?>" method="get" role="search">
                <label for="nomor">Sudah melapor? Lacak dengan nomor laporan</label>
                <div class="lacak-cepat__baris">
                    <input type="search" id="nomor" name="nomor" placeholder="LP-2026-000123" autocomplete="off" autocapitalize="characters" spellcheck="false">
                    <button type="submit" class="tombol tombol--utama"><?= ikon('search') ?><span class="sr-only">Lacak</span></button>
                </div>
            </form>
        </div>
    </div>
</section>

<section class="pita" aria-labelledby="judul-statistik">
    <div class="wadah">
        <h2 id="judul-statistik" class="sr-only">Angka laporan</h2>
        <dl class="angka-baris">
            <div>
                <dt>laporan masuk bulan ini</dt>
                <dd class="angka-baris__nilai"><?= $angka($statistik['bulanIni']) ?></dd>
            </div>
            <div>
                <dt>laporan diteruskan ke instansi berwenang</dt>
                <dd class="angka-baris__nilai"><?= $angka($statistik['diteruskan']) ?> <small>dari <?= $angka($statistik['diterima']) ?></small></dd>
            </div>
            <div>
                <dt>laporan sudah ditindaklanjuti</dt>
                <dd class="angka-baris__nilai"><?= $angka($statistik['selesai']) ?></dd>
            </div>
        </dl>
    </div>
</section>

<section class="section" aria-labelledby="judul-cara">
    <div class="wadah">
        <div class="section__judul"><h2 id="judul-cara">Cara kerjanya</h2></div>
        <ol class="cara">
            <li class="cara__baris">
                <img class="cara__foto" src="<?= esc(foto_stok('lapor', 800, 600), 'attr') ?>" alt="Seseorang menulis catatan di samping laptop" loading="lazy" width="800" height="600">
                <div class="cara__teks">
                    <span class="cara__nomor" aria-hidden="true">1</span>
                    <h3>Laporkan</h3>
                    <p class="teks-muted">Ceritakan kejadiannya dan lampirkan bukti bila ada. Anda bisa merahasiakan identitas Anda, dan langsung mendapat nomor laporan.</p>
                </div>
            </li>
            <li class="cara__baris">
                <img class="cara__foto" src="<?= esc(foto_stok('keadilan', 800, 600), 'attr') ?>" alt="Patung Dewi Keadilan memegang timbangan" loading="lazy" width="800" height="600">
                <div class="cara__teks">
                    <span class="cara__nomor" aria-hidden="true">2</span>
                    <h3>Admin memverifikasi dan meneruskan</h3>
                    <p class="teks-muted">Admin LaporKan memeriksa laporan Anda, lalu meneruskannya ke instansi yang berwenang, seperti KPK, kejaksaan, kepolisian, atau inspektorat.</p>
                </div>
            </li>
            <li class="cara__baris">
                <img class="cara__foto" src="<?= esc(foto_stok('komunitas', 800, 600), 'attr') ?>" alt="Sekelompok orang berkumpul" loading="lazy" width="800" height="600">
                <div class="cara__teks">
                    <span class="cara__nomor" aria-hidden="true">3</span>
                    <h3>Pantau perkembangannya</h3>
                    <p class="teks-muted">Lihat setiap perkembangan seperti melacak paket. Setelah diverifikasi, ringkasan tanpa identitas tampil di halaman publik untuk meningkatkan kesadaran bersama.</p>
                </div>
            </li>
        </ol>
    </div>
</section>

<section class="section section--subtle" aria-labelledby="judul-kenali">
    <div class="wadah">
        <div class="section__judul">
            <h2 id="judul-kenali">Kenali korupsi</h2>
            <a href="<?= url_to('tentang_korupsi') ?>">Pelajari selengkapnya <?= ikon('arrow-right') ?></a>
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
            <h2 id="judul-terbaru">Laporan terverifikasi terbaru</h2>
            <a href="<?= url_to('laporan.publik') ?>">Lihat semua <?= ikon('arrow-right') ?></a>
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
        <a class="tombol tombol--utama tombol--besar" href="<?= url_to('warga.laporan.baru') ?>"><?= ikon('plus') ?> Buat Laporan</a>
    </div>
</section>

<?= $this->endSection() ?>
