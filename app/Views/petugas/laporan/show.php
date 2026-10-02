<?phpuse App\Enums\StatusPengaduan;

$transisi  = $pengaduan->status()->transisiBerikutnya();
$errorFoto = galat('foto_tanggapan');
$wa        = $identitasTerbuka ? tautan_wa($pengaduan->telepon_pelapor, 'Halo ' . $pengaduan->nama_pelapor . ', kami admin LaporKan terkait laporan ' . $pengaduan->nomor_laporan . '.') : null;

$aksiRiwayat = static function ($t, bool $terbaru) use ($petugas): string {
    $html = '';
    if ($t->id_user === $petugas->getId() || $petugas->isAdmin()) {
        $html .= '<a class="tombol tombol--teks tombol--kecil" href="' . url_to('petugas.tanggapan.ubah', $t->id_tanggapan) . '">' . ikon('edit') . ' Ubah</a>';
    }
    if ($terbaru && $petugas->isAdmin()) {
        $html .= komponen('components/tombol_hapus', [
            'aksi'       => site_url('petugas/tanggapan/' . $t->id_tanggapan),
            'konfirmasi' => 'Tanggapan ini dihapus dan status laporan kembali ke status sebelumnya.',
        ]);
    }

    return $html;
};
?>
<?= $this->extend('layouts/petugas') ?>
<?= $this->section('konten') ?>

<div class="halaman-judul">
    <a class="kembali" href="<?= url_to('petugas.laporan') ?>"><?= ikon('chevron-left') ?> Kotak masuk</a>
    <div class="baris baris--antara">
        <div>
            <p class="teks-muted tebal mb-0 angka-tabular"><?= esc($pengaduan->nomor_laporan) ?>
                <?php if ($pengaduan->kategori !== null): ?> · <?= esc($pengaduan->kategori) ?><?php endif ?>
                <?php if (! $pengaduan->status()->selesaiDiproses()): ?>
                    · <span class="<?= $pengaduan->melewatiBatas() ? 'umur--lama' : '' ?>">umur <?= umur_laporan($pengaduan->umurHari()) ?></span>
                <?php endif ?>
            </p>
            <h1 style="font-size:1.75rem"><?= esc(penggalan($pengaduan->isi_laporan, 90)) ?></h1>
        </div>
        <div class="tombol-grup">
            <a class="tombol tombol--kecil" href="<?= url_to('petugas.laporan.ubah', $pengaduan->id_pengaduan) ?>"><?= ikon('edit') ?> Ubah</a>
            <?php if ($pengaduan->tampilPublik()): ?>
                <a class="tombol tombol--kecil" href="<?= url_to('laporan.detail', $pengaduan->nomor_laporan) ?>" target="_blank"><?= ikon('eye') ?> Tampilan publik</a>
            <?php endif ?>
            <?php if ($petugas->isAdmin()): ?>
                <?= komponen('components/tombol_hapus', [
                    'aksi'       => url_to('petugas.laporan.detail', $pengaduan->id_pengaduan),
                    'konfirmasi' => 'Laporan ' . $pengaduan->nomor_laporan . ' beserta semua tanggapan dan buktinya akan dihapus permanen.',
                ]) ?>
            <?php endif ?>
        </div>
    </div>
</div>

<?= komponen('components/ringkasan_error') ?>

<div class="dua-kolom dua-kolom--lebar-kanan">
    <div class="tumpuk">
        <section class="blok" aria-labelledby="judul-isi">
            <h2 id="judul-isi" class="blok__judul">Laporan</h2>
            <p class="isi-laporan"><?= esc($pengaduan->isi_laporan) ?></p>
            <?= komponen('components/rincian_laporan', ['pengaduan' => $pengaduan]) ?>
        </section>

        <section class="blok" aria-labelledby="judul-bukti">
            <h2 id="judul-bukti" class="blok__judul">Bukti</h2>
            <?php if ($pengaduan->fotoUrl() !== null): ?>
                <figure class="foto-laporan"><a href="<?= esc($pengaduan->fotoUrl(), 'attr') ?>" target="_blank" rel="noopener"><img src="<?= esc($pengaduan->fotoUrl(), 'attr') ?>" alt="Foto dari pelapor" loading="lazy"></a></figure>
            <?php endif ?>
            <?php if ($bukti === [] && $pengaduan->fotoUrl() === null): ?>
                <p class="teks-muted">Pelapor tidak melampirkan bukti.</p>
            <?php else: ?>
                <?= komponen('components/daftar_bukti', [
                    'bukti' => $bukti,
                    'url'   => static fn (array $b): string => url_to('petugas.laporan.bukti', $pengaduan->id_pengaduan, $b['id_bukti']),
                ]) ?>
            <?php endif ?>
        </section>

        <section class="blok" aria-labelledby="judul-pelapor">
            <h2 id="judul-pelapor" class="blok__judul">Pelapor</h2>
            <?php if ($identitasTerbuka): ?>
                <p class="mb-0"><?= esc($pengaduan->nama_pelapor) ?> <span class="teks-muted"><?= esc($pengaduan->telepon_pelapor) ?></span></p>
                <div class="kontak-pelapor">
                    <?php if ($wa !== null): ?>
                        <a class="tombol tombol--kecil" href="<?= esc($wa, 'attr') ?>" target="_blank" rel="noopener"><?= ikon('whatsapp') ?> WhatsApp</a>
                    <?php endif ?>
                    <a class="tombol tombol--kecil" href="tel:<?= esc(preg_replace('/[^0-9+]/', '', (string) $pengaduan->telepon_pelapor), 'attr') ?>"><?= ikon('phone') ?> Telepon</a>
                </div>
            <?php else: ?>
                <p><?= ikon('lock') ?> <strong>Identitas dirahasiakan.</strong> Pelapor meminta namanya tidak ditampilkan.</p>
                <?php if ($petugas->isAdmin()): ?>
                    <form action="<?= url_to('petugas.laporan.identitas', $pengaduan->id_pengaduan) ?>" method="post"
                          data-konfirmasi="Buka identitas pelapor hanya jika benar-benar perlu menghubunginya. Tindakan ini tercatat di log aktivitas." data-tombol="Buka identitas">
                        <?= csrf_field() ?>
                        <button type="submit" class="tombol tombol--kecil"><?= ikon('eye') ?> Buka identitas</button>
                    </form>
                <?php else: ?>
                    <p class="teks-kecil teks-muted">Hanya administrator yang dapat membuka identitas pelapor.</p>
                <?php endif ?>
            <?php endif ?>
        </section>

        <section class="blok" aria-labelledby="judul-publik">
            <h2 id="judul-publik" class="blok__judul">Ringkasan untuk publik</h2>
            <p class="teks-kecil teks-muted">
                <?php if ($pengaduan->tampilPublik()): ?>
                    <?= ikon('eye') ?> Tampil di halaman publik.
                <?php elseif (trim((string) $pengaduan->ringkasan_publik) !== ''): ?>
                    Tersimpan, tampil setelah laporan berstatus Valid.
                <?php else: ?>
                    Belum tampil di publik. Laporan hanya tampil setelah berstatus Valid <strong>dan</strong> punya ringkasan.
                <?php endif ?>
            </p>
            <form action="<?= url_to('petugas.laporan.publik', $pengaduan->id_pengaduan) ?>" method="post" novalidate>
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="PUT">
                <?= komponen('components/field', [
                    'nama' => 'ringkasan_publik', 'label' => 'Ringkasan', 'tipe' => 'textarea',
                    'hint' => 'Tanpa nama orang, nama pelapor, atau detail yang dapat mengenali pelapor. Contoh: Dugaan pungutan liar dalam pengurusan izin usaha di sebuah kantor pelayanan di Jawa Timur.',
                    'nilai' => $pengaduan->ringkasan_publik ?? '', 'atribut' => ['rows' => 3, 'maxlength' => 1000],
                ]) ?>
                <div class="tombol-grup">
                    <button type="submit" class="tombol tombol--kecil"><?= ikon('check') ?> Simpan ringkasan</button>
                    <?php if (trim((string) $pengaduan->ringkasan_publik) !== ''): ?>
                        <button type="submit" name="hapus" value="1" class="tombol tombol--teks tombol--kecil"><?= ikon('eye-off') ?> Sembunyikan dari publik</button>
                    <?php endif ?>
                </div>
            </form>
        </section>
    </div>

    <div>
        <?= komponen('components/tracker', ['pengaduan' => $pengaduan, 'tanggapan' => $tanggapan, 'untuk' => 'petugas']) ?>

        <?php if ($transisi !== []): ?>
            <section class="kartu" aria-labelledby="judul-tanggapi" style="margin-bottom: var(--space-5)">
                <h2 id="judul-tanggapi" class="kartu__judul">Beri tanggapan <span class="teks-muted teks-kecil" style="font-weight:500">· tekan <kbd>T</kbd></span></h2>
                <form action="<?= url_to('petugas.tanggapan.tambah', $pengaduan->id_pengaduan) ?>" method="post" enctype="multipart/form-data" novalidate>
                    <?= csrf_field() ?>
                    <fieldset>
                        <legend>Ubah status menjadi</legend>
                        <div class="pilihan">
                            <?php foreach ($transisi as $s): ?>
                                <div class="pilihan__item">
                                    <input type="radio" id="status-<?= $s->value ?>" name="status_tanggapan" value="<?= $s->value ?>"<?= old('status_tanggapan', $transisi[0]->value) === $s->value ? ' checked' : '' ?>>
                                    <label for="status-<?= $s->value ?>"><?= ikon($s->ikon()) ?> <?= esc($s->labelPetugas()) ?><?= $s === StatusPengaduan::TidakValid ? ' <span class="teks-muted teks-kecil">· wajib tulis alasan</span>' : '' ?><?= $s === StatusPengaduan::Diteruskan ? ' <span class="teks-muted teks-kecil">· wajib isi instansi tujuan</span>' : '' ?></label>
                                </div>
                            <?php endforeach ?>
                        </div>
                    </fieldset>
                    <?php if (in_array(StatusPengaduan::Diteruskan, $transisi, true)): ?>
                        <?= komponen('components/field', [
                            'nama' => 'instansi_tujuan', 'label' => 'Diteruskan ke instansi',
                            'hint' => 'Tampil kepada pelapor. Contoh: Komisi Pemberantasan Korupsi (KPK)',
                            'atribut' => ['maxlength' => 255, 'list' => 'daftar-instansi', 'autocomplete' => 'off'],
                        ]) ?>
                        <?= komponen('components/daftar_instansi') ?>
                    <?php endif ?>
                    <?= komponen('components/field', [
                        'nama' => 'isi_tanggapan', 'label' => 'Pesan untuk pelapor', 'tipe' => 'textarea',
                        'hint' => 'Hanya dibaca pelapor, tidak tampil di publik. Sebutkan apa yang sudah dilakukan dan langkah berikutnya.',
                        'atribut' => ['rows' => 4, 'maxlength' => 5000],
                    ]) ?>
                    <div class="field<?= $errorFoto !== null ? ' field--error' : '' ?>">
                        <label for="foto_tanggapan">Foto pendukung (tidak wajib)</label>
                        <?php if ($errorFoto !== null): ?><span class="pesan-error"><?= ikon('alert') ?><span><?= esc($errorFoto) ?></span></span><?php endif ?>
                        <input type="file" id="foto_tanggapan" name="foto_tanggapan" accept="image/jpeg,image/png,image/webp,image/gif" data-pratinjau="pratinjau-tanggapan">
                        <div class="pratinjau-foto" id="pratinjau-tanggapan" hidden></div>
                    </div>
                    <button type="submit" class="tombol tombol--utama" data-memuat="Menyimpan…"><?= ikon('send') ?> Simpan tanggapan</button>
                </form>
            </section>
        <?php endif ?>

        <section class="blok" aria-labelledby="judul-riwayat">
            <h2 id="judul-riwayat" class="blok__judul">Riwayat</h2>
            <?= komponen('components/riwayat', ['pengaduan' => $pengaduan, 'tanggapan' => $tanggapan, 'untuk' => 'petugas', 'aksi' => $aksiRiwayat, 'identitasTerbuka' => $identitasTerbuka]) ?>
        </section>
    </div>
</div>

<?= $this->endSection() ?>
