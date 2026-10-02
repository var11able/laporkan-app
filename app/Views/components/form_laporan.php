<?phpuse App\Services\UploadService;

$bukti ??= [];
$rahasia  = $pengaduan === null ? true : $pengaduan->rahasia;
$sisa     = UploadService::MAKS_BUKTI - count($bukti);
$errBukti = galat('bukti');
?>
<?= komponen('components/field', [
    'nama' => 'id_kategori', 'label' => 'Jenis dugaan korupsi', 'tipe' => 'select', 'kelas' => 'input--sedang',
    'opsi' => $opsiKategori, 'kosong' => 'Pilih jenis', 'nilai' => (string) ($pengaduan?->id_kategori ?? ''),
]) ?>
<?= komponen('components/field', [
    'nama' => 'isi_laporan', 'label' => 'Kronologi', 'tipe' => 'textarea', 'nilai' => $pengaduan?->isi_laporan ?? '',
    'hint' => 'Apa yang terjadi, siapa yang terlibat, kapan, di mana, dan bagaimana caranya.',
    'atribut' => ['maxlength' => 5000, 'rows' => 7, 'data-hitung' => 'isi-hitung'],
]) ?>
<span class="penghitung" id="isi-hitung" aria-live="polite"></span>
<?= komponen('components/field', [
    'nama' => 'waktu_kejadian', 'label' => 'Waktu kejadian (tidak wajib)', 'tipe' => 'date', 'kelas' => 'input--sedang',
    'nilai' => $pengaduan?->waktu_kejadian ?? '', 'atribut' => ['max' => date('Y-m-d')],
]) ?>
<?= komponen('components/field', [
    'nama' => 'perkiraan_kerugian', 'label' => 'Perkiraan nilai uang yang terlibat (tidak wajib)', 'kelas' => 'input--sedang',
    'hint' => 'Dalam rupiah, angka saja.', 'nilai' => (string) ($pengaduan?->perkiraan_kerugian ?? ''), 'atribut' => ['inputmode' => 'numeric', 'maxlength' => 30],
]) ?>
<?= komponen('components/field', [
    'nama' => 'instansi_terlapor', 'label' => 'Instansi atau kantor', 'nilai' => $pengaduan?->instansi_terlapor ?? '', 'atribut' => ['maxlength' => 255],
]) ?>
<?= komponen('components/field', [
    'nama' => 'pihak_terlapor', 'label' => 'Pihak yang terlibat (tidak wajib)', 'nilai' => $pengaduan?->pihak_terlapor ?? '', 'atribut' => ['maxlength' => 255],
]) ?>
<?= komponen('components/field', [
    'nama' => 'id_provinsi', 'label' => 'Provinsi', 'tipe' => 'select', 'kelas' => 'input--sedang',
    'opsi' => $opsiProvinsi, 'kosong' => 'Pilih provinsi', 'nilai' => (string) ($pengaduan?->id_provinsi ?? ''),
    'atribut' => ['data-anak' => 'id_kabupaten_kota', 'data-url' => site_url('api/wilayah/__ID__/kabupaten-kota')],
]) ?>
<?= komponen('components/field', [
    'nama' => 'id_kabupaten_kota', 'label' => 'Kabupaten/kota', 'tipe' => 'select', 'kelas' => 'input--sedang',
    'opsi' => $opsiKabupaten, 'kosong' => $opsiKabupaten === [] ? 'Pilih provinsi dulu' : 'Pilih kabupaten/kota',
    'nilai' => (string) ($pengaduan?->id_kabupaten_kota ?? ''),
]) ?>

<fieldset class="field<?= $errBukti !== null ? ' field--error' : '' ?>">
    <legend>Bukti</legend>
    <?php if ($bukti !== [] && isset($urlBukti)): ?>
        <?= komponen('components/daftar_bukti', ['bukti' => $bukti, 'url' => $urlBukti, 'pilihHapus' => 'hapus_bukti']) ?>
    <?php endif ?>
    <?php if ($errBukti !== null): ?><span class="pesan-error"><?= ikon('alert') ?><span><?= esc($errBukti) ?></span></span><?php endif ?>
    <?php if ($sisa > 0): ?>
        <label for="bukti">Tambah foto atau PDF (sisa <?= $sisa ?> berkas, maks. 5 MB)</label>
        <input type="file" id="bukti" name="bukti[]" multiple accept="image/jpeg,image/png,image/webp,image/gif,application/pdf" data-pratinjau="pratinjau-bukti">
        <div class="pratinjau-foto" id="pratinjau-bukti" hidden></div>
    <?php else: ?>
        <p class="teks-muted teks-kecil">Sudah ada <?= UploadService::MAKS_BUKTI ?> bukti. Centang "Hapus" pada salah satunya untuk menggantinya.</p>
    <?php endif ?>
</fieldset>

<div class="field">
    <label class="centang">
        <input type="checkbox" name="rahasia" value="1"<?= old('rahasia', $rahasia ? '1' : null) !== null ? ' checked' : '' ?>>
        <span><strong>Rahasiakan identitas pelapor</strong></span>
    </label>
    <span class="hint">Nama dan nomor telepon pelapor tidak ditampilkan kepada operator.</span>
</div>
