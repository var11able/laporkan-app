<?php$chip = [
    ''         => 'Semua',
    'diterima' => 'Diterima',
    'diproses' => 'Diproses',
    'selesai'  => 'Selesai',
    'ditolak'  => 'Tidak dapat ditindaklanjuti',
];
?>
<?= $this->extend('layouts/publik') ?>
<?= $this->section('konten') ?>

<div class="halaman-judul baris baris--antara">
    <div>
        <p class="teks-muted mb-0">Halo, <?= esc(explode(' ', $warga->nama)[0]) ?></p>
        <h1 class="mb-0">Laporan Saya</h1>
    </div>
    <a class="tombol tombol--utama tombol-lapor-header" href="<?= url_to('warga.laporan.baru') ?>"><?= ikon('plus') ?> Buat Laporan</a>
</div>

<?php if ($adaDraf): ?>
    <div class="banner banner--peringatan">
        <?= ikon('file-text') ?>
        <div class="banner__isi">
            <p class="tebal mb-0">Ada laporan yang belum selesai Anda kirim.</p>
            <div class="tombol-grup" style="margin-top: var(--space-2)">
                <a class="tombol tombol--utama tombol--kecil" href="<?= url_to('warga.laporan.baru') ?>">Lanjutkan</a>
                <a class="tombol tombol--teks tombol--kecil" href="<?= url_to('warga.laporan.baru') ?>?ulang=1">Buang dan mulai baru</a>
            </div>
        </div>
    </div>
<?php endif ?>

<?php if ($total === 0): ?>
    <div class="kosong">
        <div class="kosong__ikon"><?= ikon('shield') ?></div>
        <h2>Belum ada laporan</h2>
        <p>Menemukan pungutan liar, suap, atau penyalahgunaan anggaran? Laporkan di sini. Identitas Anda kami jaga, dan admin akan meneruskannya ke instansi yang berwenang.</p>
        <a class="tombol tombol--utama" href="<?= url_to('warga.laporan.baru') ?>"><?= ikon('plus') ?> Buat laporan pertama</a>
    </div>
<?php else: ?>
    <nav aria-label="Saring berdasarkan status">
        <ul class="tab">
            <?php foreach ($chip as $kunci => $label): ?>
                <?php if ($kunci === '' || $jumlah[$kunci] > 0 || $kelompok === $kunci): ?>
                    <li>
                        <a href="<?= url_to('warga.beranda') . ($kunci === '' ? '' : '?kelompok=' . $kunci) ?>"<?= $kelompok === $kunci ? ' aria-current="page"' : '' ?>>
                            <?= esc($label) ?> <span class="tab__jumlah"><?= $kunci === '' ? $total : $jumlah[$kunci] ?></span>
                        </a>
                    </li>
                <?php endif ?>
            <?php endforeach ?>
        </ul>
    </nav>

    <form class="filter" method="get" action="<?= url_to('warga.beranda') ?>" role="search">
        <?php if ($kelompok !== ''): ?><input type="hidden" name="kelompok" value="<?= esc($kelompok, 'attr') ?>"><?php endif ?>
        <?= komponen('components/field', ['nama' => 'q', 'label' => 'Cari laporan', 'tipe' => 'search', 'nilai' => $cari, 'atribut' => ['placeholder' => 'Kata kunci, instansi, atau nomor laporan']]) ?>
        <div><button type="submit" class="tombol"><?= ikon('search') ?> Cari</button></div>
    </form>

    <?php if ($laporan === []): ?>
        <div class="kosong"><p><?= $cari !== '' ? 'Tidak ada laporan yang cocok dengan pencarian ini.' : 'Tidak ada laporan dengan status ini.' ?></p></div>
    <?php else: ?>
        <ul class="daftar-laporan">
            <?php foreach ($laporan as $p): ?>
                <?php $diperbarui = $p->tanggapan_terakhir ?? $p->tgl_pengaduan; ?>
                <li>
                    <div class="daftar-laporan__foto">
                        <?= ikon($p->status()->ikon()) ?>
                    </div>
                    <div>
                        <h2 class="daftar-laporan__judul potong-2"><a href="<?= url_to('warga.laporan.detail', $p->id_pengaduan) ?>"><?= esc($p->isi_laporan) ?></a></h2>
                        <div class="daftar-laporan__meta">
                            <?= status_badge($p->status()) ?>
                            <span class="angka-tabular"><?= esc($p->nomor_laporan) ?></span>
                            <?php if ($p->kategori !== null): ?><span><?= esc($p->kategori) ?></span><?php endif ?>
                            <span title="Terakhir diperbarui"><?= ikon('clock') ?> <?= esc(waktu_relatif($diperbarui)) ?></span>
                        </div>
                    </div>
                </li>
            <?php endforeach ?>
        </ul>
        <?= $pager->links('default', 'laporkan') ?>
    <?php endif ?>
<?php endif ?>

<?= $this->endSection() ?>
