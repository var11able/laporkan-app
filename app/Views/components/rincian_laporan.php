<dl class="rincian">
    <dt>Jenis</dt>
    <dd><?= esc($laporan->kategori ?? 'Belum ditentukan') ?></dd>
    <dt>Instansi</dt>
    <dd><?= esc($laporan->instansi_terlapor ?? 'Tidak disebutkan') ?></dd>
    <?php if ($laporan->pihak_terlapor !== null): ?>
        <dt>Pihak yang terlibat</dt>
        <dd><?= esc($laporan->pihak_terlapor) ?></dd>
    <?php endif ?>
    <dt>Wilayah</dt>
    <dd><?= esc($laporan->lokasi() ?? 'Tidak disebutkan') ?><?= $laporan->detail_lokasi ? '<br><span class="teks-muted">' . esc($laporan->detail_lokasi) . '</span>' : '' ?></dd>
    <?php if ($laporan->waktu_kejadian !== null): ?>
        <dt>Waktu kejadian</dt>
        <dd><?= tanggal($laporan->waktu_kejadian, false) ?></dd>
    <?php endif ?>
    <?php if ($laporan->kerugianRupiah() !== null): ?>
        <dt>Perkiraan nilai</dt>
        <dd class="angka-tabular"><?= esc($laporan->kerugianRupiah()) ?></dd>
    <?php endif ?>
    <dt>Dikirim</dt>
    <dd><?= tanggal($laporan->tgl_laporan) ?></dd>
    <?php if ($laporan->updated_at !== null): ?>
        <dt>Diubah</dt>
        <dd><?= tanggal($laporan->updated_at) ?></dd>
    <?php endif ?>
    <dt>Identitas pelapor</dt>
    <dd><?= $laporan->rahasia ? ikon('lock') . ' Dirahasiakan dari operator' : 'Terlihat oleh admin' ?></dd>
</dl>
