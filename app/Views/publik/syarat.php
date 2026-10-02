<?= $this->extend('layouts/publik') ?>
<?= $this->section('konten') ?>

<h1>Syarat &amp; Ketentuan</h1>
<p class="lead">Dengan memakai LaporKan, Anda menyetujui ketentuan berikut.</p>

<h2>Tentang LaporKan</h2>
<p>LaporKan adalah wadah bagi masyarakat untuk melaporkan dugaan korupsi di Indonesia. Admin LaporKan memverifikasi laporan lalu meneruskannya ke instansi yang berwenang. <strong>LaporKan bukan lembaga penegak hukum</strong> dan tidak dapat menjamin hasil penyelidikan instansi tersebut.</p>

<h2>Laporan yang boleh dikirim</h2>
<ul>
    <li>Laporan berisi dugaan korupsi yang Anda ketahui, alami, atau saksikan sendiri, seperti suap, pungutan liar, gratifikasi, penggelapan, atau kecurangan pengadaan.</li>
    <li>Tuliskan dengan jujur dan apa adanya. Laporan yang sengaja dipalsukan, menyesatkan, atau menuduh orang tanpa dasar akan ditolak dan dapat berakibat hukum bagi pelapornya.</li>
    <li>Jangan mengirim konten yang mengandung SARA, ujaran kebencian, atau pornografi.</li>
    <li>Pengaduan layanan publik yang bukan dugaan korupsi dapat disampaikan melalui <a href="https://www.lapor.go.id" rel="noopener" target="_blank">SP4N-LAPOR!</a>.</li>
</ul>

<h2>Keadaan darurat</h2>
<p><strong>LaporKan bukan untuk keadaan darurat.</strong> Untuk ancaman keselamatan atau kejahatan yang sedang terjadi, hubungi 112.</p>

<h2>Penanganan laporan</h2>
<ul>
    <li>Admin memverifikasi setiap laporan. Laporan yang tidak dapat ditindaklanjuti diberi alasan yang jelas.</li>
    <li>Laporan yang cukup lengkap diteruskan ke instansi yang berwenang. Waktu tindak lanjut bergantung pada instansi tersebut.</li>
    <li>Setelah diverifikasi, admin dapat menampilkan ringkasan laporan tanpa identitas di halaman publik. Ringkasan tersebut adalah dugaan, bukan putusan.</li>
    <li>Laporan hanya dapat diubah atau dihapus oleh pelapor sebelum diperiksa admin. Admin dapat menghapus laporan palsu atau spam.</li>
</ul>

<h2>Akun Anda</h2>
<p>Jaga kerahasiaan kata sandi Anda. Anda bertanggung jawab atas laporan yang dikirim melalui akun Anda.</p>

<p>Baca juga <a href="<?= url_to('privasi') ?>">Kebijakan Privasi</a> dan <a href="<?= url_to('tentang_korupsi') ?>">Tentang Korupsi</a>.</p>

<p class="teks-muted teks-kecil">Terakhir diperbarui: <?= tanggal('2026-10-01', false) ?></p>

<?= $this->endSection() ?>
