<?php
use App\Entities\Pengaduan;
use App\Enums\StatusPengaduan;

$angka = static fn (int $n): string => number_format($n, 0, ',', '.');
$srcset = static fn (string $kunci, int $rasioT): string => implode(', ', array_map(
    static fn (int $w): string => foto_stok($kunci, $w, (int) round($w * $rasioT / 100)) . ' ' . $w . 'w',
    [640, 1000, 1600],
));
$demoJenis  = array_slice(array_values(array_filter($kategori, static fn (array $k): bool => $k['kategori'] !== 'Lainnya')), 0, 3);
$demoAwal   = StatusPengaduan::Baru;
$demoNomor  = 'LP-' . date('Y') . '-000123';
$demoStatus = [];

foreach (StatusPengaduan::cases() as $s) {
    $demoStatus[$s->value] = [
        'warga'      => $s->labelWarga(),
        'petugas'    => $s->labelPetugas(),
        'ikon'       => $s->ikon(),
        'slug'       => $s->slug(),
        'penjelasan' => $s->penjelasan(),
        'lanjut'     => array_map(static fn (StatusPengaduan $t): string => $t->value, $s->transisiBerikutnya()),
    ];
}

$demoAlur = array_map(static fn (StatusPengaduan $s): string => $s->value, StatusPengaduan::alur());
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
        <p class="lead cara__ajakan">Coba alurnya langsung di bawah ini. Tampilannya sama dengan aplikasi aslinya, tetapi tidak ada data yang dikirim.</p>
        <ol class="cara" data-demo-cara data-ikon="<?= esc(aset('icons.svg'), 'attr') ?>" data-status="<?= esc(json_encode($demoStatus), 'attr') ?>" data-alur="<?= esc(json_encode($demoAlur), 'attr') ?>">
            <li class="cara__baris">
                <div class="cara__demo">
                    <p class="cara__demo-label"><?= ikon('user') ?> Tampilan pelapor</p>
                    <div class="kartu">
                        <fieldset class="mb-0">
                            <legend>Apa jenis dugaan korupsinya?</legend>
                            <div class="pilihan">
                                <?php foreach ($demoJenis as $i => $k): ?>
                                    <div class="pilihan__item">
                                        <input type="radio" id="demo-jenis-<?= esc($k['id_kategori'], 'attr') ?>" name="demo_jenis" value="<?= esc($k['kategori'], 'attr') ?>"<?= $i === 0 ? ' checked' : '' ?>>
                                        <label for="demo-jenis-<?= esc($k['id_kategori'], 'attr') ?>"><?= esc($k['kategori']) ?></label>
                                    </div>
                                <?php endforeach ?>
                            </div>
                        </fieldset>
                        <label class="centang cara__demo-centang">
                            <input type="checkbox" data-demo-rahasia checked>
                            <span><strong>Rahasiakan identitas saya</strong></span>
                        </label>
                        <button type="button" class="tombol tombol--utama tombol--penuh-mobile" data-demo-kirim><?= ikon('send') ?> <span>Kirim laporan</span></button>
                        <div class="banner banner--sukses cara__demo-hasil" data-demo-terkirim role="status" hidden>
                            <?= ikon('check-circle') ?>
                            <div class="banner__isi">
                                <p class="tebal mb-0">Laporan terkirim</p>
                                <p>Nomor laporan Anda <strong data-demo-nomor><?= esc($demoNomor) ?></strong>. Laporan ini sekarang masuk ke admin di langkah 2.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="cara__teks">
                    <span class="cara__nomor" aria-hidden="true">1</span>
                    <h3>Laporkan</h3>
                    <p class="teks-muted">Ceritakan kejadiannya dan lampirkan bukti bila ada. Anda bisa merahasiakan identitas Anda, dan langsung mendapat nomor laporan.</p>
                </div>
            </li>
            <li class="cara__baris">
                <div class="cara__demo">
                    <p class="cara__demo-label"><?= ikon('shield') ?> Tampilan admin</p>
                    <div class="kartu">
                        <div class="cara__demo-kepala">
                            <span class="teks-kecil teks-muted" data-demo-nomor><?= esc($demoNomor) ?></span>
                            <span data-demo-lencana><?= status_badge($demoAwal, 'petugas') ?></span>
                        </div>
                        <p class="tebal mb-0" data-demo-jenis><?= esc($demoJenis[0]['kategori'] ?? 'Lainnya') ?></p>
                        <p class="teks-kecil teks-muted">Pelapor: <span data-demo-pelapor>Dirahasiakan</span></p>
                        <div data-demo-form>
                            <fieldset>
                                <legend>Ubah status menjadi</legend>
                                <div class="pilihan" data-demo-opsi>
                                    <?php foreach ($demoAwal->transisiBerikutnya() as $i => $s): ?>
                                        <div class="pilihan__item">
                                            <input type="radio" id="demo-status-<?= $s->value ?>" name="demo_status" value="<?= $s->value ?>"<?= $i === 0 ? ' checked' : '' ?>>
                                            <label for="demo-status-<?= $s->value ?>"><?= ikon($s->ikon()) ?> <?= esc($s->labelPetugas()) ?></label>
                                        </div>
                                    <?php endforeach ?>
                                </div>
                            </fieldset>
                            <div class="field" data-demo-bidang="<?= StatusPengaduan::Diteruskan->value ?>" hidden>
                                <label for="demo-instansi">Diteruskan ke instansi</label>
                                <input type="text" id="demo-instansi" list="daftar-instansi" autocomplete="off" value="Komisi Pemberantasan Korupsi (KPK)">
                            </div>
                            <div class="field" data-demo-bidang="<?= StatusPengaduan::TidakValid->value ?>" hidden>
                                <label for="demo-alasan">Alasan untuk pelapor</label>
                                <textarea id="demo-alasan" rows="3">Kronologi belum menyebutkan waktu dan tempat kejadian. Silakan kirim laporan baru dengan keterangan yang lebih lengkap.</textarea>
                            </div>
                            <button type="button" class="tombol tombol--utama tombol--penuh-mobile" data-demo-simpan><?= ikon('send') ?> Simpan tanggapan</button>
                        </div>
                        <p class="teks-muted mb-0" data-demo-tuntas hidden>Laporan sudah selesai diproses.</p>
                        <?= komponen('components/daftar_instansi') ?>
                    </div>
                </div>
                <div class="cara__teks">
                    <span class="cara__nomor" aria-hidden="true">2</span>
                    <h3>Admin memverifikasi dan meneruskan</h3>
                    <p class="teks-muted">Admin LaporKan memeriksa laporan Anda, lalu meneruskannya ke instansi yang berwenang, seperti KPK, kejaksaan, kepolisian, atau inspektorat.</p>
                </div>
            </li>
            <li class="cara__baris">
                <div class="cara__demo">
                    <p class="cara__demo-label"><?= ikon('activity') ?> Halaman lacak laporan</p>
                    <div class="kartu" data-demo-lacak aria-live="polite">
                        <?= komponen('components/tracker', [
                            'pengaduan' => new Pengaduan(['status_pengaduan' => $demoAwal->value, 'tgl_pengaduan' => date('Y-m-d H:i:s')]),
                            'tanggapan' => [],
                        ]) ?>
                        <button type="button" class="tombol tombol--teks tombol--kecil" data-demo-ulang hidden><?= ikon('undo') ?> Ulangi dari awal</button>
                    </div>
                </div>
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
