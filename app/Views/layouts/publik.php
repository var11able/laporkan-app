<?php$menu ??= '';
$warga ??= null;
$aktif = static fn (string $kunci): string => $menu === $kunci ? ' aria-current="page"' : '';
$inisial = $warga !== null ? mb_strtoupper(mb_substr($warga->nama, 0, 1)) : '';
?>
<!doctype html>
<html lang="id">
<head>
    <?= komponen('layouts/_head', ['judul' => $judul ?? null, 'deskripsi' => $deskripsi ?? null]) ?>
</head>
<body class="ada-navbawah">
<a class="skip-link" href="#konten">Langsung ke konten utama</a>
<div class="tata-letak">
    <header class="appbar">
        <div class="wadah appbar__baris">
            <a class="logo" href="<?= url_to('beranda') ?>">
                <span class="logo__tanda"><?= ikon('message') ?></span>
                <span class="logo__teks">LaporKan<small>Lapor Korupsi</small></span>
            </a>
            <nav class="appbar__nav" aria-label="Menu utama">
                <ul>
                    <li><a href="<?= url_to('beranda') ?>"<?= $aktif('beranda') ?>>Beranda</a></li>
                    <li><a href="<?= url_to('laporan.publik') ?>"<?= $aktif('laporan') ?>>Daftar Laporan</a></li>
                    <li><a href="<?= url_to('lacak') ?>"<?= $aktif('lacak') ?>>Lacak</a></li>
                    <li><a href="<?= url_to('tentang_korupsi') ?>"<?= $aktif('tentang') ?>>Tentang Korupsi</a></li>
                    <?php if ($warga !== null): ?>
                        <li><a href="<?= url_to('warga.beranda') ?>"<?= $aktif('laporan-saya') ?>>Laporan Saya</a></li>
                    <?php endif ?>
                </ul>
            </nav>
            <div class="appbar__aksi">
                <a class="tombol tombol--utama tombol--kecil tombol-lapor-header" href="<?= url_to('warga.laporan.baru') ?>"><?= ikon('plus') ?> Buat Laporan</a>
                <?php if ($warga !== null): ?>
                    <details class="menu-akun appbar__akun">
                        <summary aria-label="Menu akun <?= esc($warga->nama, 'attr') ?>"><span class="avatar" aria-hidden="true"><?= esc($inisial) ?></span></summary>
                        <ul class="menu-akun__daftar">
                            <li class="teks-kecil teks-muted" style="padding: 8px 12px"><?= esc($warga->nama) ?></li>
                            <li><a href="<?= url_to('warga.beranda') ?>"><?= ikon('file-text') ?> Laporan Saya</a></li>
                            <li><a href="<?= url_to('warga.rekap') ?>"><?= ikon('printer') ?> Rekap</a></li>
                            <li><a href="<?= url_to('warga.profil') ?>"><?= ikon('user') ?> Akun</a></li>
                            <li>
                                <form action="<?= url_to('keluar') ?>" method="post">
                                    <?= csrf_field() ?>
                                    <button type="submit"><?= ikon('log-out') ?> Keluar</button>
                                </form>
                            </li>
                        </ul>
                    </details>
                <?php elseif (isset($petugas) && $petugas !== null): ?>
                    <a class="tombol tombol--kecil appbar__akun" href="<?= url_to('petugas.dasbor') ?>">Panel Admin</a>
                <?php else: ?>
                    <a class="tombol tombol--kecil appbar__akun" href="<?= url_to('masuk') ?>"<?= $aktif('masuk') ?>>Masuk</a>
                <?php endif ?>
            </div>
        </div>
    </header>

    <main id="konten" tabindex="-1">
        <?php if (! empty($penuh)): ?>
            <?php if (trim((string) preg_replace('/<!--.*?-->/s', '', $flash = komponen('components/flash'))) !== ''): ?>
                <div class="wadah" style="padding-top: var(--space-4)"><?= $flash ?></div>
            <?php endif ?>
            <?= $this->renderSection('konten') ?>
        <?php else: ?>
            <div class="halaman">
                <div class="wadah<?= ($lebar ?? '') === 'sempit' ? ' wadah--sempit' : '' ?>">
                    <?= komponen('components/flash') ?>
                    <?= $this->renderSection('konten') ?>
                </div>
            </div>
        <?php endif ?>
    </main>

    <footer class="footer">
        <div class="wadah footer__baris">
            <p class="mb-0">© <?= date('Y') ?> LaporKan · Pelaporan Kasus Korupsi di Indonesia</p>
            <ul>
                <li><a href="<?= url_to('tentang_korupsi') ?>">Tentang Korupsi</a></li>
                <li><a href="<?= url_to('saran') ?>">Kritik &amp; Saran</a></li>
                <li><a href="<?= url_to('privasi') ?>">Kebijakan Privasi</a></li>
                <li><a href="<?= url_to('syarat') ?>">Syarat &amp; Ketentuan</a></li>
                <li><a href="<?= url_to('petugas.masuk') ?>">Masuk Admin</a></li>
            </ul>
        </div>
    </footer>
</div>

<nav class="navbawah" aria-label="Navigasi">
    <ul>
        <li><a href="<?= url_to('beranda') ?>"<?= $aktif('beranda') ?>><?= ikon('home') ?>Beranda</a></li>
        <?php if ($warga !== null): ?>
            <li><a href="<?= url_to('warga.beranda') ?>"<?= $aktif('laporan-saya') ?>><?= ikon('file-text') ?>Laporan Saya</a></li>
        <?php else: ?>
            <li><a href="<?= url_to('lacak') ?>"<?= $aktif('lacak') ?>><?= ikon('search') ?>Lacak</a></li>
        <?php endif ?>
        <li><a class="navbawah__lapor" href="<?= url_to('warga.laporan.baru') ?>"><span class="navbawah__fab"><?= ikon('plus') ?></span>Lapor</a></li>
        <?php if ($warga !== null): ?>
            <li><a href="<?= url_to('warga.profil') ?>"<?= $aktif('profil') ?>><?= ikon('user') ?>Akun</a></li>
        <?php else: ?>
            <li><a href="<?= url_to('masuk') ?>"<?= $aktif('masuk') ?>><?= ikon('user') ?>Masuk</a></li>
        <?php endif ?>
    </ul>
</nav>
<?= komponen('components/dialog_konfirmasi') ?>
</body>
</html>
