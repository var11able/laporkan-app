<?= $this->extend('layouts/publik') ?>
<?= $this->section('konten') ?>

<div class="halaman-judul baris" style="gap: var(--space-4)">
    <span class="avatar" style="width:56px;height:56px;font-size:1.375rem" aria-hidden="true"><?= esc(mb_strtoupper(mb_substr($warga->nama, 0, 1))) ?></span>
    <div>
        <h1 class="mb-0">Akun</h1>
        <p class="teks-muted mb-0">@<?= esc($warga->username) ?></p>
    </div>
</div>

<ul class="pengaturan" aria-label="Menu akun">
    <li><a href="<?= url_to('warga.beranda') ?>"><?= ikon('file-text') ?> Laporan Saya <?= ikon('chevron-right') ?></a></li>
    <li><a href="<?= url_to('warga.rekap') ?>"><?= ikon('printer') ?> Rekap &amp; cetak laporan <?= ikon('chevron-right') ?></a></li>
    <li><a href="<?= url_to('warga.password') ?>"><?= ikon('lock') ?> Ganti kata sandi <?= ikon('chevron-right') ?></a></li>
</ul>

<section class="kartu" aria-labelledby="judul-data" style="margin-bottom: var(--space-5)">
    <h2 id="judul-data" class="kartu__judul">Data diri</h2>
    <?= komponen('components/ringkasan_error') ?>
    <form action="<?= url_to('warga.profil') ?>" method="post" novalidate>
        <?= csrf_field() ?>
        <input type="hidden" name="_method" value="PUT">
        <?= komponen('components/field', ['nama' => 'nama', 'label' => 'Nama lengkap', 'nilai' => $warga->nama, 'atribut' => ['autocomplete' => 'name', 'maxlength' => 100]]) ?>
        <?= komponen('components/field', ['nama' => 'no_telepon', 'label' => 'Nomor HP / WhatsApp', 'tipe' => 'tel', 'nilai' => $warga->no_telepon, 'atribut' => ['autocomplete' => 'tel', 'inputmode' => 'tel']]) ?>
        <?= komponen('components/field', ['nama' => 'alamat', 'label' => 'Alamat', 'tipe' => 'textarea', 'nilai' => $warga->alamat, 'atribut' => ['rows' => 2, 'maxlength' => 500]]) ?>
        <button type="submit" class="tombol tombol--utama tombol--penuh-mobile">Simpan</button>
    </form>
</section>

<section class="kartu" style="margin-bottom: var(--space-5)">
    <?= komponen('components/pilihan_tema') ?>
</section>

<ul class="pengaturan pengaturan--bahaya">
    <li>
        <form action="<?= url_to('keluar') ?>" method="post">
            <?= csrf_field() ?>
            <button type="submit"><?= ikon('log-out') ?> Keluar</button>
        </form>
    </li>
</ul>

<?= $this->endSection() ?>
