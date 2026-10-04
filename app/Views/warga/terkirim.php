<?= $this->extend('layouts/publik') ?>
<?= $this->section('konten') ?>

<div class="sukses" role="status">
    <div class="sukses__ikon" aria-hidden="true"><?= ikon('check-circle') ?></div>
    <h1>Laporan terkirim!</h1>
    <p class="lead" style="margin-inline:auto">Terima kasih atas keberanian Anda. Admin LaporKan akan memeriksa laporan Anda dalam <strong>1–3 hari kerja</strong>.</p>

    <div class="nomor-laporan">
        <span class="teks-muted teks-kecil">Nomor laporan</span>
        <strong><?= esc($pengaduan->nomor_laporan) ?></strong>
        <button type="button" class="tombol tombol--teks tombol--kecil" data-salin="<?= esc($pengaduan->nomor_laporan, 'attr') ?>" hidden><?= ikon('copy') ?> Salin</button>
    </div>

    <p class="teks-kecil teks-muted" style="margin-inline:auto">Simpan nomor ini untuk Anda sendiri. Demi keamanan Anda, jangan beri tahu orang lain bahwa Anda yang melapor.</p>

    <div class="tombol-grup" style="justify-content:center">
        <a class="tombol tombol--utama tombol--besar" href="<?= url_to('warga.laporan.detail', $pengaduan->id_pengaduan) ?>">Lacak laporan ini</a>
    </div>
</div>
<span data-hapus-draf="laporan-baru" hidden></span>

<section class="blok" style="margin-top: var(--space-6)" aria-labelledby="judul-selanjutnya">
    <h2 id="judul-selanjutnya" class="blok__judul">Apa yang terjadi selanjutnya</h2>
    <ol class="mb-0">
        <li>Admin memverifikasi laporan dan bukti Anda, dan mungkin menghubungi Anda jika perlu keterangan tambahan.</li>
        <li>Jika laporan cukup lengkap, admin meneruskannya ke instansi yang berwenang, misalnya KPK, kejaksaan, kepolisian, atau inspektorat.</li>
        <li>Setiap perkembangan bisa Anda pantau di <a href="<?= url_to('warga.beranda') ?>">Laporan Saya</a>, atau lewat nomor laporan di halaman <a href="<?= url_to('lacak') ?>">Lacak</a>.</li>
    </ol>
</section>

<?= $this->endSection() ?>
