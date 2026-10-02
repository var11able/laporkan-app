<dialog class="dialog" id="dialog-konfirmasi" aria-labelledby="dialog-konfirmasi-judul">
    <div class="dialog__isi">
        <div class="dialog__ikon" aria-hidden="true"><?= ikon('trash') ?></div>
        <h2 id="dialog-konfirmasi-judul">Anda yakin?</h2>
        <p data-pesan class="teks-muted mb-0"></p>
    </div>
    <div class="dialog__aksi">
        <button type="button" class="tombol" data-batal>Batal</button>
        <button type="button" class="tombol tombol--bahaya" data-lanjut>Ya, hapus</button>
    </div>
</dialog>
