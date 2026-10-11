<?php
$mode ??= 'bola';
$judul ??= null;
$daftar ??= true;
$teratas ??= config('PetaSebaran')->daftarTeratas;
$skala = $peta['skala'];
$rentang = [];

foreach ($skala as $i => $batas) {
    $rentang[$i + 1] = isset($skala[$i + 1]) ? ($batas === $skala[$i + 1] - 1 ? (string) $batas : $batas . '–' . ($skala[$i + 1] - 1)) : $batas . '+';
}

$skrip = [aset('vendor/d3-array/d3-array.min.js'), aset('vendor/d3-geo/d3-geo.min.js'), aset('vendor/topojson-client/topojson-client.min.js')];
$data  = ['rotasi' => $peta['rotasi'], 'tautan' => $peta['tautan'], 'samar' => $peta['samar'], 'provinsi' => $peta['provinsi']];
?>
<div class="peta peta--<?= esc($mode, 'attr') ?>">
    <div class="peta__bola"
         data-peta="<?= esc($mode, 'attr') ?>"
         data-sebaran="<?= esc(json_encode($data), 'attr') ?>"
         data-skrip="<?= esc(json_encode($skrip), 'attr') ?>"
         data-modul="<?= esc(aset('js/peta.js'), 'attr') ?>"
         data-geo-indonesia="<?= esc(aset('geo/indonesia-provinsi.json'), 'attr') ?>"
         data-geo-dunia="<?= esc(aset('geo/dunia-110m.json'), 'attr') ?>"
         data-ringkasan="<?= esc($id, 'attr') ?>-ringkasan">
        <p id="<?= esc($id, 'attr') ?>-ringkasan" class="sr-only"><?= esc($peta['ringkasan']) ?></p>
        <div class="peta__kanvas" data-peta-kanvas></div>
        <div class="peta__legenda">
            <p class="peta__legenda-judul"><?= $peta['samar'] ? 'Laporan terverifikasi' : 'Jumlah laporan' ?></p>
            <ul class="peta__skala">
                <li><span class="peta__swatch peta__swatch--0"></span>0</li>
                <?php foreach ($rentang as $kelas => $label): ?>
                    <li><span class="peta__swatch peta__swatch--<?= $kelas ?>"></span><?= $kelas === 1 && $peta['samar'] ? '&lt;' . (int) $peta['ambang'] : esc($label) ?></li>
                <?php endforeach ?>
            </ul>
            <?php if ($mode === 'bola'): ?>
                <p class="peta__legenda-titik"><span class="peta__titik-contoh"></span>Laporan baru (<?= config('PetaSebaran')->hariTerbaru ?> hari)</p>
            <?php endif ?>
        </div>
        <?php if ($mode === 'bola'): ?>
            <p class="peta__petunjuk" data-peta-petunjuk hidden><?= ikon('move') ?> Geser untuk memutar</p>
            <button type="button" class="tombol tombol--kaca tombol--kecil peta__reset" data-peta-reset hidden><?= ikon('undo') ?> Kembali ke Indonesia</button>
        <?php endif ?>
        <div class="peta__tip" data-peta-tip hidden></div>
    </div>

    <?php if ($daftar): ?>
        <div class="peta__panel">
            <?php if ($judul !== null): ?><h3 class="peta__judul"><?= esc($judul) ?></h3><?php endif ?>
            <?php $baris = static function (array $p) use ($peta): string {
                return '<li><a href="' . esc($peta['tautan'] . $p['id'], 'attr') . '" data-kode="' . esc($p['kode'], 'attr') . '">'
                    . '<span class="peta__swatch peta__swatch--' . (int) $p['kelas'] . '" aria-hidden="true"></span>'
                    . '<span class="peta__nama">' . esc($p['nama']) . '</span>'
                    . '<span class="peta__jumlah">' . esc($p['label']) . '<span class="sr-only"> laporan</span></span></a></li>';
            } ?>
            <ul class="peta__daftar">
                <?php foreach (array_slice($peta['provinsi'], 0, $teratas) as $p): ?><?= $baris($p) ?><?php endforeach ?>
            </ul>
            <?php if (count($peta['provinsi']) > $teratas): ?>
                <details class="peta__lainnya">
                    <summary>Lihat semua <?= count($peta['provinsi']) ?> provinsi</summary>
                    <ul class="peta__daftar">
                        <?php foreach (array_slice($peta['provinsi'], $teratas) as $p): ?><?= $baris($p) ?><?php endforeach ?>
                    </ul>
                </details>
            <?php endif ?>
        </div>
    <?php endif ?>
</div>
