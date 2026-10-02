<?php?>
<?= $this->extend('layouts/publik') ?>
<?= $this->section('konten') ?>

<h1>Tentang Korupsi</h1>
<p class="lead">Korupsi adalah tindakan menyalahgunakan kekuasaan yang dipercayakan untuk mendapatkan keuntungan pribadi. Korupsi tidak hanya terjadi di kantor pemerintahan, tetapi juga di kehidupan sehari-hari di sekitar kita.</p>

<h2>Tujuh kelompok tindak pidana korupsi</h2>
<p>Undang-Undang No. 31 Tahun 1999 jo. Undang-Undang No. 20 Tahun 2001 tentang Pemberantasan Tindak Pidana Korupsi merumuskan 30 jenis tindak pidana korupsi, yang dikelompokkan menjadi tujuh:</p>
<ol>
    <li><strong>Kerugian keuangan negara</strong>: memperkaya diri sendiri atau orang lain secara melawan hukum, atau menyalahgunakan kewenangan, sehingga merugikan keuangan negara.</li>
    <li><strong>Suap-menyuap</strong>: memberi atau menerima sesuatu agar pejabat berbuat atau tidak berbuat sesuatu dalam jabatannya.</li>
    <li><strong>Penggelapan dalam jabatan</strong>: menggelapkan uang, surat berharga, atau barang yang dikelola karena jabatan, termasuk memalsukan pembukuan.</li>
    <li><strong>Pemerasan</strong>: pejabat memaksa orang lain memberi sesuatu atau membayar lebih dari seharusnya, misalnya pungutan liar dalam layanan publik.</li>
    <li><strong>Perbuatan curang</strong>: kecurangan dalam pembangunan atau pengadaan barang yang dapat membahayakan orang lain atau keuangan negara.</li>
    <li><strong>Benturan kepentingan dalam pengadaan</strong>: pejabat ikut serta dalam pengadaan yang diurusnya sendiri.</li>
    <li><strong>Gratifikasi</strong>: pemberian kepada pejabat yang berhubungan dengan jabatannya dan tidak dilaporkan kepada KPK.</li>
</ol>

<h2>Peran masyarakat</h2>
<p>Pemberantasan korupsi bukan hanya tugas penegak hukum. Masyarakat dapat berperan dengan menolak memberi atau meminta suap, mencari dan menyampaikan informasi tentang dugaan korupsi, serta memantau tindak lanjutnya. Peran serta masyarakat ini dijamin dalam undang-undang pemberantasan korupsi.</p>

<h2>Apa yang dilakukan LaporKan</h2>
<ul>
    <li>Menerima laporan dugaan korupsi dari masyarakat, dengan pilihan merahasiakan identitas pelapor.</li>
    <li>Memverifikasi laporan, lalu meneruskannya ke instansi yang berwenang menyelidikinya.</li>
    <li>Menampilkan ringkasan laporan yang sudah diverifikasi, tanpa identitas, agar masyarakat lebih waspada.</li>
</ul>
<p><strong>LaporKan bukan lembaga penegak hukum.</strong> Laporan yang masuk adalah dugaan; pembuktian dan penindakan menjadi wewenang instansi penegak hukum.</p>

<h2>Sebelum melapor, siapkan</h2>
<ul>
    <li><strong>Apa</strong> yang terjadi, dan <strong>bagaimana</strong> caranya.</li>
    <li><strong>Siapa</strong> yang terlibat: nama atau jabatannya, jika Anda tahu.</li>
    <li><strong>Kapan</strong> dan <strong>di mana</strong>: instansi, kantor, dan wilayahnya.</li>
    <li><strong>Bukti</strong> bila ada: foto, kuitansi, surat, atau dokumen lain.</li>
</ul>
<p>Laporkan dengan jujur dan apa adanya. Laporan yang sengaja dipalsukan atau menuduh tanpa dasar dapat berakibat hukum bagi pelapornya.</p>
<p><a class="tombol tombol--utama" href="<?= url_to('warga.laporan.baru') ?>"><?= ikon('plus') ?> Buat Laporan</a></p>

<h2>Saluran resmi lainnya</h2>
<ul>
    <li><strong>Komisi Pemberantasan Korupsi (KPK)</strong>: KPK Whistleblower's System di <a href="https://kws.kpk.go.id" rel="noopener" target="_blank">kws.kpk.go.id</a>, atau call center 198.</li>
    <li><strong>SP4N-LAPOR!</strong> untuk pengaduan layanan publik secara umum: <a href="https://www.lapor.go.id" rel="noopener" target="_blank">lapor.go.id</a>.</li>
</ul>
<p>Untuk keadaan darurat atau kejahatan yang sedang terjadi, hubungi 112.</p>

<?= $this->endSection() ?>
