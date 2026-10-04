<?php
$instansi = [
    'Komisi Pemberantasan Korupsi (KPK)',
    'Kejaksaan Agung Republik Indonesia',
    'Kejaksaan Tinggi',
    'Kejaksaan Negeri',
    'Kepolisian Negara Republik Indonesia (Polri)',
    'Inspektorat Jenderal kementerian terkait',
    'Inspektorat provinsi',
    'Inspektorat kabupaten/kota',
    'Badan Pengawasan Keuangan dan Pembangunan (BPKP)',
    'Badan Pemeriksa Keuangan (BPK)',
    'Ombudsman Republik Indonesia',
];
?>
<datalist id="daftar-instansi">
    <?php foreach ($instansi as $i): ?>
        <option value="<?= esc($i, 'attr') ?>"></option>
    <?php endforeach ?>
</datalist>
