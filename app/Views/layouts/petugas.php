<?php
$menu ??= '';
$aktif = static fn (string $kunci): string => $menu === $kunci ? ' aria-current="page"' : '';
$admin = $petugas->isAdmin();
$inisial = mb_strtoupper(mb_substr($petugas->nama, 0, 1));
$item = static fn (string $kunci, string $url, string $ikonId, string $label, string $tambahan = ''): string =>
    '<li><a href="' . $url . '"' . $aktif($kunci) . ' title="' . esc($label, 'attr') . '">' . ikon($ikonId) . '<span>' . esc($label) . '</span>' . $tambahan . '</a></li>';
?>
<!doctype html>
<html lang="id">
<head>
    <?= komponen('layouts/_head', ['judul' => $judul ?? null, 'deskripsi' => $deskripsi ?? null]) ?>
</head>
<body>
<a class="skip-link" href="#konten">Langsung ke konten utama</a>
<?= komponen('components/pita_demo') ?>
<div class="dasbor">
    <aside class="sidebar" id="sidebar" aria-label="Menu admin">
        <div class="sidebar__kepala">
            <?= komponen('components/logo', ['url' => url_to('petugas.dasbor'), 'sub' => 'Panel Admin']) ?>
            <button type="button" class="tombol tombol--ikon sidebar__ciutkan" data-ciutkan aria-pressed="false" aria-label="Ciutkan menu"><?= ikon('sidebar') ?></button>
        </div>
        <nav>
            <p class="sidebar__grup">Kerja</p>
            <ul>
                <?= $item('dasbor', url_to('petugas.dasbor'), 'home', 'Dasbor') ?>
                <?= $item('laporan', url_to('petugas.laporan'), 'inbox', 'Kotak Masuk', ! empty($jumlahBaru) ? '<span class="sidebar__lencana" title="Laporan baru">' . (int) $jumlahBaru . '</span>' : '') ?>
                <?= $item('rekap', url_to('petugas.rekap'), 'printer', 'Rekap & Cetak') ?>
            </ul>
            <p class="sidebar__grup">Data</p>
            <ul>
                <?= $item('warga', url_to('petugas.warga'), 'users', 'Warga') ?>
                <?= $item('provinsi', url_to('petugas.provinsi'), 'layers', 'Provinsi') ?>
                <?= $item('kabupaten_kota', url_to('petugas.kabupaten_kota'), 'map-pin', 'Kabupaten/Kota') ?>
                <?= $item('saran', url_to('petugas.saran'), 'message', 'Kritik & Saran') ?>
            </ul>
            <?php if ($admin): ?>
                <p class="sidebar__grup">Administrator</p>
                <ul>
                    <?= $item('pengguna', url_to('petugas.pengguna'), 'shield', 'Akun Admin') ?>
                    <?= $item('log', url_to('petugas.log'), 'activity', 'Log Aktivitas') ?>
                </ul>
            <?php endif ?>
        </nav>
        <div class="sidebar__kaki">
            <a href="<?= url_to('beranda') ?>" target="_blank">Lihat situs publik ↗</a>
        </div>
    </aside>
    <div class="latar-gelap" id="latar-sidebar" hidden></div>

    <div class="dasbor__utama">
        <header class="topbar">
            <button type="button" class="tombol tombol--ikon topbar__menu" data-buka aria-controls="sidebar" aria-expanded="false" data-latar="latar-sidebar" aria-label="Buka menu"><?= ikon('menu') ?></button>
            <form class="topbar__cari" action="<?= url_to('petugas.laporan') ?>" method="get" role="search">
                <?= ikon('search') ?>
                <label for="cari-cepat" class="sr-only">Cari laporan</label>
                <input type="search" id="cari-cepat" name="q" placeholder="Cari nomor atau isi laporan" value="<?= esc((string) service('request')->getGet('q'), 'attr') ?>">
                <kbd aria-hidden="true">/</kbd>
            </form>
            <div class="topbar__akun">
            <?= komponen('components/tombol_tema') ?>
            <details class="menu-akun">
                <summary aria-label="Menu akun <?= esc($petugas->nama, 'attr') ?>" class="menu-akun__ringkas">
                    <span class="avatar" aria-hidden="true"><?= esc($inisial) ?></span>
                    <span class="menu-akun__identitas teks-kecil"><strong><?= esc($petugas->nama) ?></strong><span class="teks-muted"><?= esc($petugas->jabatan()->label()) ?></span></span>
                </summary>
                <ul class="menu-akun__daftar">
                    <li><a href="<?= url_to('petugas.profil') ?>"><?= ikon('user') ?> Profil &amp; tampilan</a></li>
                    <li><a href="<?= url_to('petugas.password') ?>"><?= ikon('lock') ?> Ganti kata sandi</a></li>
                    <li>
                        <form action="<?= url_to('keluar') ?>" method="post">
                            <?= csrf_field() ?>
                            <button type="submit"><?= ikon('log-out') ?> Keluar</button>
                        </form>
                    </li>
                </ul>
            </details>
            </div>
        </header>
        <main class="dasbor__konten" id="konten" tabindex="-1">
            <?= komponen('components/flash') ?>
            <?= $this->renderSection('konten') ?>
        </main>
    </div>
</div>
<?= komponen('components/dialog_konfirmasi') ?>
</body>
</html>
