<?phpuse App\Entities\Pengaduan;
use App\Enums\StatusPengaduan;

$query = static function (array $ubah) use ($filter): string {
    $q = array_filter(array_merge($filter, $ubah), static fn ($v) => $v !== '' && $v !== null);

    return $q === [] ? '' : '?' . http_build_query($q);
};
$statusAktif = StatusPengaduan::tryFrom($filter['status']);
?>
<?= $this->extend('layouts/petugas') ?>
<?= $this->section('konten') ?>

<div class="halaman-judul baris baris--antara">
    <div>
        <h1 class="mb-0">Kotak Masuk</h1>
        <p class="pintasan mb-0" style="margin-top: 4px"><span><kbd>/</kbd> cari</span><span><kbd>J</kbd> <kbd>K</kbd> pindah baris</span><span><kbd>Enter</kbd> buka</span></p>
    </div>
    <a class="tombol" href="<?= url_to('petugas.laporan.baru') ?>"><?= ikon('plus') ?> Buat atas nama pelapor</a>
</div>

<nav aria-label="Filter status">
    <ul class="tab">
        <li><a href="<?= url_to('petugas.laporan') . $query(['status' => '']) ?>"<?= $statusAktif === null ? ' aria-current="page"' : '' ?>>Semua <span class="tab__jumlah"><?= array_sum($jumlah) ?></span></a></li>
        <?php foreach (StatusPengaduan::cases() as $s): ?>
            <li><a href="<?= url_to('petugas.laporan') . $query(['status' => $s->value]) ?>"<?= $statusAktif === $s ? ' aria-current="page"' : '' ?>><?= esc($s->labelPetugas()) ?> <span class="tab__jumlah"><?= $jumlah[$s->value] ?></span></a></li>
        <?php endforeach ?>
    </ul>
</nav>

<details class="lipat" data-buka-desktop>
<summary class="tombol tombol--kecil"><?= ikon('filter') ?> Saring & cari</summary>
<form class="filter" method="get" action="<?= url_to('petugas.laporan') ?>">
    <input type="hidden" name="status" value="<?= esc($filter['status'], 'attr') ?>">
    <?= komponen('components/field', ['nama' => 'q', 'label' => 'Cari isi, instansi, atau nomor', 'tipe' => 'search', 'nilai' => $filter['q']]) ?>
    <?= komponen('components/field', ['nama' => 'kategori', 'label' => 'Jenis', 'tipe' => 'select', 'opsi' => $opsiKategori, 'kosong' => 'Semua', 'nilai' => $filter['kategori']]) ?>
    <?= komponen('components/field', ['nama' => 'provinsi', 'label' => 'Provinsi', 'tipe' => 'select', 'opsi' => $opsiProvinsi, 'kosong' => 'Semua', 'nilai' => $filter['provinsi']]) ?>
    <?= komponen('components/field', ['nama' => 'dari', 'label' => 'Dari tanggal', 'tipe' => 'date', 'nilai' => $filter['dari']]) ?>
    <?= komponen('components/field', ['nama' => 'sampai', 'label' => 'Sampai tanggal', 'tipe' => 'date', 'nilai' => $filter['sampai']]) ?>
    <div class="baris"><button type="submit" class="tombol"><?= ikon('filter') ?> Terapkan</button> <a href="<?= url_to('petugas.laporan') ?>">Reset</a></div>
</form>
</details>

<?php if ($laporan === []): ?>
    <div class="kosong"><?= ikon('inbox') ?><p>Tidak ada laporan yang cocok.</p></div>
<?php else: ?>
    <form action="<?= url_to('petugas.laporan.massal') ?>" method="post">
        <?= csrf_field() ?>
        <div class="aksi-massal" id="aksi-massal" hidden>
            <p class="tebal mb-0" style="flex-basis:100%"><span data-jumlah>0</span> laporan dipilih</p>
            <div class="field">
                <label for="massal-status">Ubah status ke</label>
                <select id="massal-status" name="status_tanggapan" required>
                    <?php foreach (StatusPengaduan::cases() as $s): ?>
                        <?php if ($s !== StatusPengaduan::Baru): ?><option value="<?= $s->value ?>"><?= esc($s->labelPetugas()) ?></option><?php endif ?>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="field" style="flex-basis: 260px">
                <label for="massal-instansi">Instansi tujuan <span class="teks-muted teks-kecil">(untuk Diteruskan)</span></label>
                <input type="text" id="massal-instansi" name="instansi_tujuan" maxlength="255" list="daftar-instansi" autocomplete="off">
                <?= komponen('components/daftar_instansi') ?>
            </div>
            <div class="field" style="flex-basis: 320px">
                <label for="massal-isi">Pesan untuk pelapor</label>
                <input type="text" id="massal-isi" name="isi_tanggapan" required maxlength="5000">
            </div>
            <button type="submit" class="tombol tombol--utama">Terapkan</button>
        </div>

        <div class="tabel-wadah">
            <table class="tabel tabel--kartu">
                <caption class="sr-only">Daftar laporan<?= $statusAktif ? ' berstatus ' . esc($statusAktif->labelPetugas()) : '' ?></caption>
                <thead>
                    <tr>
                        <th scope="col" class="kolom-cek"><input type="checkbox" data-pilih-semua="id[]" data-panel="aksi-massal" aria-label="Pilih semua laporan di halaman ini"></th>
                        <th scope="col">Nomor</th>
                        <th scope="col">Laporan</th>
                        <th scope="col">Pelapor</th>
                        <th scope="col">Jenis &amp; wilayah</th>
                        <th scope="col">Status</th>
                        <th scope="col">Umur</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($laporan as $p): ?>
                        <tr>
                            <td class="kolom-cek"><input type="checkbox" name="id[]" value="<?= $p->id_pengaduan ?>" aria-label="Pilih <?= esc($p->nomor_laporan, 'attr') ?>"></td>
                            <td class="nowrap" data-label="Nomor"><a class="tautan-baris" href="<?= url_to('petugas.laporan.detail', $p->id_pengaduan) ?>"><?= esc($p->nomor_laporan) ?></a><br><span class="teks-muted"><?= tanggal($p->tgl_pengaduan, false) ?></span></td>
                            <td data-label="Laporan"><?= esc(penggalan($p->isi_laporan, 90)) ?></td>
                            <td data-label="Pelapor"><?= $p->rahasia ? ikon('lock') . ' <span class="teks-muted">Dirahasiakan</span>' : esc($p->nama_pelapor) ?></td>
                            <td data-label="Jenis"><?= esc($p->kategori ?? '–') ?><br><span class="teks-muted"><?= esc($p->provinsi ?? '–') ?></span></td>
                            <td data-label="Status"><?= status_badge($p->status(), 'petugas') ?></td>
                            <td data-label="Umur" class="nowrap<?= $p->melewatiBatas() ? ' umur--lama' : '' ?>">
                                <?php if ($p->status()->selesaiDiproses()): ?>
                                    <span class="teks-muted">–</span>
                                <?php else: ?>
                                    <?= $p->melewatiBatas() ? ikon('alert') . ' Menunggu ' : '' ?><?= umur_laporan($p->umurHari()) ?>
                                <?php endif ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </form>
    <p class="teks-muted teks-kecil">Laporan baru yang menunggu lebih dari <?= Pengaduan::BATAS_HARI_TANGGAPAN ?> hari ditandai <?= ikon('alert') ?>.</p>
    <?= $pager->links('default', 'laporkan') ?>
<?php endif ?>

<?= $this->endSection() ?>
