<?= $this->extend('layouts/publik') ?>
<?= $this->section('konten') ?>

<h1>Kebijakan Privasi</h1>
<p class="lead">Melaporkan korupsi butuh keberanian. Halaman ini menjelaskan data apa yang kami kumpulkan, siapa yang dapat melihatnya, dan bagaimana kami melindungi Anda sebagai pelapor.</p>

<h2>Data yang kami kumpulkan</h2>
<ul>
    <li><strong>Data akun</strong>: nama, username, nomor telepon, dan alamat yang Anda isi saat mendaftar.</li>
    <li><strong>Isi laporan</strong>: jenis dugaan korupsi, kronologi, instansi dan pihak yang terlibat, wilayah, waktu kejadian, perkiraan nilai, dan bukti yang Anda lampirkan.</li>
    <li><strong>Kritik dan saran</strong>: isi masukan Anda, serta nama, nomor telepon, dan alamat jika Anda mengisinya.</li>
</ul>
<p>Kata sandi Anda disimpan dalam bentuk terenkripsi sehingga admin pun tidak dapat melihatnya.</p>

<h2>Siapa yang dapat melihat apa</h2>
<ul>
    <li><strong>Publik</strong> hanya dapat melihat laporan yang sudah diverifikasi, dalam bentuk <strong>ringkasan yang ditulis admin</strong>: jenis dugaan korupsi, provinsi, tanggal, dan perkembangan statusnya. Nama Anda, kontak Anda, kronologi lengkap, nama instansi, pihak yang terlibat, pesan admin, dan bukti <strong>tidak pernah ditampilkan</strong>.</li>
    <li><strong>Siapa pun yang memegang nomor laporan</strong> dapat melihat status dan tanggal perkembangannya di halaman Lacak, tanpa isi laporan.</li>
    <li><strong>Operator</strong> dapat membaca isi laporan dan bukti untuk memverifikasinya. Jika Anda memilih <strong>Rahasiakan identitas saya</strong>, operator tidak dapat melihat nama dan nomor telepon Anda.</li>
    <li><strong>Administrator</strong> dapat membuka identitas pelapor yang dirahasiakan hanya bila perlu menghubungi Anda. Setiap pembukaan identitas tercatat di log aktivitas.</li>
    <li><strong>Instansi yang berwenang</strong>, seperti KPK, kejaksaan, kepolisian, atau inspektorat, menerima isi laporan dan bukti saat laporan Anda diteruskan untuk ditindaklanjuti.</li>
</ul>
<p>Kami tidak menjual atau membagikan data Anda kepada pihak lain di luar keperluan penanganan laporan.</p>

<h2>Bukti yang Anda lampirkan</h2>
<p>Bukti disimpan di luar folder publik dan hanya dapat dibuka oleh Anda dan admin. Data lokasi, jenis ponsel, dan waktu pengambilan di dalam foto dihapus otomatis saat diunggah. Dokumen PDF tidak diubah dan dapat menyimpan nama pembuatnya; jika itu nama Anda, simpan ulang dokumennya sebagai PDF baru atau lampirkan foto dokumennya.</p>

<h2>Menjaga diri Anda</h2>
<ul>
    <li>Simpan nomor laporan untuk Anda sendiri, dan jangan beri tahu orang lain bahwa Anda yang melapor.</li>
    <li>Jangan melapor dari perangkat atau jaringan milik instansi yang Anda laporkan.</li>
    <li>Gunakan kata sandi yang kuat dan tidak dipakai di tempat lain.</li>
</ul>

<h2>Cookie dan penyimpanan di perangkat</h2>
<p>Kami hanya memakai satu cookie untuk menjaga Anda tetap masuk selama sesi berlangsung. Isian kronologi dan pilihan tema tampilan disimpan di perangkat Anda sendiri agar tidak hilang saat koneksi terputus. Jika Anda memakai perangkat bersama, hapus data peramban setelah selesai.</p>

<h2>Menghapus data Anda</h2>
<p>Laporan yang belum diperiksa admin dapat Anda hapus sendiri dari Laporan Saya. Untuk menghapus akun beserta seluruh laporannya, sampaikan permintaan melalui <a href="<?= url_to('saran') ?>">Kritik &amp; Saran</a>.</p>

<p class="teks-muted teks-kecil">Terakhir diperbarui: <?= tanggal('2026-10-01', false) ?></p>

<?= $this->endSection() ?>
