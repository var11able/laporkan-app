<?php?>
<dl class="rincian">
    <dt>Jenis</dt>
    <dd><?= esc($pengaduan->kategori ?? 'Belum ditentukan') ?></dd>
    <dt>Instansi</dt>
    <dd><?= esc($pengaduan->instansi_terlapor ?? 'Tidak disebutkan') ?></dd>
    <?php if ($pengaduan->pihak_terlapor !== null): ?>
        <dt>Pihak yang terlibat</dt>
        <dd><?= esc($pengaduan->pihak_terlapor) ?></dd>
    <?php endif ?>
    <dt>Wilayah</dt>
    <dd><?= esc($pengaduan->lokasi() ?? 'Tidak disebutkan') ?><?= $pengaduan->detail_lokasi ? '<br><span class="teks-muted">' . esc($pengaduan->detail_lokasi) . '</span>' : '' ?></dd>
    <?php if ($pengaduan->waktu_kejadian !== null): ?>
        <dt>Waktu kejadian</dt>
        <dd><?= tanggal($pengaduan->waktu_kejadian, false) ?></dd>
    <?php endif ?>
    <?php if ($pengaduan->kerugianRupiah() !== null): ?>
        <dt>Perkiraan nilai</dt>
        <dd class="angka-tabular"><?= esc($pengaduan->kerugianRupiah()) ?></dd>
    <?php endif ?>
    <dt>Dikirim</dt>
    <dd><?= tanggal($pengaduan->tgl_pengaduan) ?></dd>
    <?php if ($pengaduan->updated_at !== null): ?>
        <dt>Diubah</dt>
        <dd><?= tanggal($pengaduan->updated_at) ?></dd>
    <?php endif ?>
    <dt>Identitas pelapor</dt>
    <dd><?= $pengaduan->rahasia ? ikon('lock') . ' Dirahasiakan dari operator' : 'Terlihat oleh admin' ?></dd>
</dl>
