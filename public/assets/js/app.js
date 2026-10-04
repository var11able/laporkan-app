
const simpan = {
  ambil(kunci) { try { return localStorage.getItem(kunci); } catch { return null; } },
  taruh(kunci, nilai) { try { localStorage.setItem(kunci, nilai); } catch { } },
  hapus(kunci) { try { localStorage.removeItem(kunci); } catch { } },
};

function snackbar(pesan) {
  let wadah = document.querySelector('.snackbar-wadah');
  if (!wadah) {
    wadah = document.createElement('div');
    wadah.className = 'snackbar-wadah';
    wadah.setAttribute('role', 'status');
    document.body.append(wadah);
  }
  const el = document.createElement('div');
  el.className = 'snackbar';
  el.innerHTML = '<p></p><button type="button" aria-label="Tutup">✕</button>';
  el.querySelector('p').textContent = pesan;
  el.querySelector('button').addEventListener('click', () => el.remove());
  wadah.append(el);
  setTimeout(() => el.remove(), 5000);
}

function aturSnackbar() {
  document.querySelectorAll('.snackbar').forEach((el) => {
    el.querySelector('button')?.addEventListener('click', () => el.remove());
    setTimeout(() => el.remove(), 5000);
  });
  document.querySelectorAll('[data-tutup-banner]').forEach((b) => b.addEventListener('click', () => b.closest('.banner')?.remove()));
}

function aturTema() {
  const tombol = document.querySelectorAll('[data-tema]');
  const terapkan = (tema) => {
    if (tema === 'light' || tema === 'dark') document.documentElement.dataset.theme = tema;
    else delete document.documentElement.dataset.theme;
    tombol.forEach((t) => t.setAttribute('aria-pressed', String(t.dataset.tema === (tema || 'system'))));
  };
  terapkan(simpan.ambil('tema'));
  tombol.forEach((t) => t.addEventListener('click', () => {
    const tema = t.dataset.tema;
    if (tema === 'system') simpan.hapus('tema'); else simpan.taruh('tema', tema);
    terapkan(tema === 'system' ? null : tema);
  }));
}

function aturLaci() {
  document.querySelectorAll('[data-buka]').forEach((tombol) => {
    const target = document.getElementById(tombol.getAttribute('aria-controls'));
    const latar = document.getElementById(tombol.dataset.latar || '');
    if (!target) return;
    const atur = (buka) => {
      tombol.setAttribute('aria-expanded', String(buka));
      target.toggleAttribute('data-terbuka', buka);
      if (latar) latar.hidden = !buka;
    };
    tombol.addEventListener('click', () => atur(tombol.getAttribute('aria-expanded') !== 'true'));
    latar?.addEventListener('click', () => atur(false));
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && tombol.getAttribute('aria-expanded') === 'true') { atur(false); tombol.focus(); }
    });
  });
}

function aturSidebar() {
  const dasbor = document.querySelector('.dasbor');
  const tombol = document.querySelector('[data-ciutkan]');
  if (!dasbor || !tombol) return;
  const atur = (ciut) => {
    dasbor.toggleAttribute('data-ciut', ciut);
    tombol.setAttribute('aria-pressed', String(ciut));
    tombol.setAttribute('aria-label', ciut ? 'Lebarkan menu' : 'Ciutkan menu');
  };
  atur(simpan.ambil('sidebar') === 'ciut');
  tombol.addEventListener('click', () => {
    const ciut = !dasbor.hasAttribute('data-ciut');
    simpan.taruh('sidebar', ciut ? 'ciut' : 'lebar');
    atur(ciut);
  });
}

function aturMenuAkun() {
  document.addEventListener('click', (e) => {
    document.querySelectorAll('details.menu-akun[open]').forEach((d) => { if (!d.contains(e.target)) d.open = false; });
  });
}

function aturKonfirmasi() {
  const dialog = document.getElementById('dialog-konfirmasi');
  if (!dialog || typeof dialog.showModal !== 'function') return;
  const pesan = dialog.querySelector('[data-pesan]');
  const lanjut = dialog.querySelector('[data-lanjut]');
  let formAktif = null;

  document.addEventListener('submit', (e) => {
    const form = e.target.closest('form[data-konfirmasi]');
    if (!form || form.dataset.dikonfirmasi) return;
    e.preventDefault();
    formAktif = form;
    pesan.textContent = form.dataset.konfirmasi;
    lanjut.textContent = form.dataset.tombol || 'Ya, hapus';
    dialog.showModal();
  }, true);
  lanjut.addEventListener('click', () => {
    if (!formAktif) return;
    formAktif.dataset.dikonfirmasi = '1';
    dialog.close();
    formAktif.requestSubmit();
  });
  dialog.querySelectorAll('[data-batal]').forEach((b) => b.addEventListener('click', () => dialog.close()));
}

function aturKirim() {
  document.addEventListener('submit', (e) => {
    const form = e.target;
    if (e.defaultPrevented || form.dataset.konfirmasi && !form.dataset.dikonfirmasi) return;
    const tombol = e.submitter;
    if (!tombol || !tombol.matches('.tombol--utama') || form.method.toLowerCase() === 'get') return;
    setTimeout(() => {
      tombol.setAttribute('aria-disabled', 'true');
      tombol.disabled = true;
      tombol.dataset.teksAsli = tombol.textContent;
      tombol.textContent = tombol.dataset.memuat || 'Mengirim…';
    }, 0);
  });
  window.addEventListener('pageshow', () => document.querySelectorAll('button[data-teks-asli]').forEach((b) => {
    b.disabled = false; b.removeAttribute('aria-disabled'); b.textContent = b.dataset.teksAsli; delete b.dataset.teksAsli;
  }));
}

function aturSandi() {
  document.querySelectorAll('[data-lihat-sandi]').forEach((tombol) => {
    const input = document.getElementById(tombol.dataset.lihatSandi);
    if (!input) return;
    tombol.hidden = false;
    tombol.addEventListener('click', () => {
      const lihat = input.type === 'password';
      input.type = lihat ? 'text' : 'password';
      tombol.setAttribute('aria-pressed', String(lihat));
      tombol.setAttribute('aria-label', lihat ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
      tombol.querySelector('use')?.setAttribute('href', tombol.querySelector('use').getAttribute('href').replace(/#.*$/, lihat ? '#eye-off' : '#eye'));
    });
  });
}

function aturSalin() {
  document.querySelectorAll('[data-salin]').forEach((tombol) => {
    if (!navigator.clipboard) return;
    tombol.hidden = false;
    tombol.addEventListener('click', async () => {
      try { await navigator.clipboard.writeText(tombol.dataset.salin); snackbar('Nomor laporan disalin.'); } catch { snackbar('Gagal menyalin. Salin secara manual.'); }
    });
  });
}

function aturFoto() {
  document.querySelectorAll('input[type="file"][data-pratinjau]').forEach((input) => {
    const wadah = document.getElementById(input.dataset.pratinjau);
    input.addEventListener('change', async () => {
      const asli = [...(input.files || [])];
      if (!asli.length) { if (wadah) wadah.hidden = true; return; }
      const hasil = await Promise.all(asli.map(async (file) => {
        if (!file.type.startsWith('image/')) return file;
        const kecil = await kompres(file).catch(() => null);
        return kecil && kecil.size < file.size ? kecil : file;
      }));
      if (hasil.some((f, i) => f !== asli[i]) && typeof DataTransfer !== 'undefined') {
        const dt = new DataTransfer();
        hasil.forEach((f) => dt.items.add(f));
        input.files = dt.files;
      }
      if (!wadah) return;
      wadah.hidden = false;
      wadah.innerHTML = '';
      hasil.forEach((file) => {
        if (file.type.startsWith('image/')) {
          const img = document.createElement('img');
          img.alt = `Pratinjau ${file.name}`;
          img.src = URL.createObjectURL(file);
          wadah.append(img);
        } else {
          const p = document.createElement('p');
          p.className = 'pratinjau-foto__berkas';
          p.textContent = file.name;
          wadah.append(p);
        }
      });
    });
  });
}

async function kompres(file, maks = 1600) {
  if (file.type === 'image/gif') return null;
  const bitmap = await createImageBitmap(file);
  const skala = Math.min(1, maks / Math.max(bitmap.width, bitmap.height));
  if (skala === 1 && file.size < 1.5 * 1024 * 1024) return null;
  const canvas = document.createElement('canvas');
  canvas.width = Math.round(bitmap.width * skala);
  canvas.height = Math.round(bitmap.height * skala);
  canvas.getContext('2d').drawImage(bitmap, 0, 0, canvas.width, canvas.height);
  const blob = await new Promise((r) => canvas.toBlob(r, 'image/jpeg', 0.82));
  return blob ? new File([blob], file.name.replace(/\.\w+$/, '') + '.jpg', { type: 'image/jpeg' }) : null;
}

function aturWilayah() {
  document.querySelectorAll('select[data-anak]').forEach((induk) => {
    const anak = document.getElementById(induk.dataset.anak);
    if (!anak) return;
    induk.addEventListener('change', async () => {
      anak.disabled = true;
      anak.innerHTML = '<option value="">Memuat…</option>';
      if (!induk.value) { anak.innerHTML = '<option value="">Pilih provinsi dulu</option>'; anak.disabled = false; return; }
      try {
        const res = await fetch(induk.dataset.url.replace('__ID__', encodeURIComponent(induk.value)), { headers: { Accept: 'application/json' } });
        const data = await res.json();
        anak.innerHTML = '<option value="">Pilih kabupaten/kota</option>';
        data.forEach((w) => anak.add(new Option(w.nama, w.id)));
        anak.disabled = false;
        if (data.length === 1) anak.value = String(data[0].id);
      } catch {
        anak.innerHTML = '<option value="">Gagal memuat. Muat ulang halaman.</option>';
      }
    });
  });
}

function aturPenghitung() {
  document.querySelectorAll('textarea[data-hitung]').forEach((ta) => {
    const out = document.getElementById(ta.dataset.hitung);
    const maks = Number(ta.getAttribute('maxlength'));
    if (!out || !maks) return;
    const perbarui = () => { out.textContent = `${ta.value.length} / ${maks}`; };
    ta.addEventListener('input', perbarui);
    perbarui();
  });
}

function aturDraf() {
  document.querySelectorAll('form[data-draf]').forEach((form) => {
    const kunci = `draf:${form.dataset.draf}`;
    const bidang = [...form.querySelectorAll('textarea[name], input[type="text"][name]')];
    const tanda = form.querySelector('[data-tersimpan]');
    let isi = {};
    try { isi = JSON.parse(simpan.ambil(kunci) || '{}'); } catch { isi = {}; }
    bidang.forEach((b) => { if (!b.value && isi[b.name]) b.value = isi[b.name]; });
    form.addEventListener('input', () => {
      const data = {};
      bidang.forEach((b) => { data[b.name] = b.value; });
      simpan.taruh(kunci, JSON.stringify(data));
      if (tanda) tanda.hidden = false;
    });
  });
  document.querySelectorAll('[data-hapus-draf]').forEach((el) => simpan.hapus(`draf:${el.dataset.hapusDraf}`));
}

function aturPilihSemua() {
  document.querySelectorAll('input[data-pilih-semua]').forEach((semua) => {
    const nama = semua.dataset.pilihSemua;
    const kotak = [...document.querySelectorAll(`input[type="checkbox"][name="${nama}"]`)];
    const panel = document.getElementById(semua.dataset.panel || '');
    const jumlah = panel?.querySelector('[data-jumlah]');
    const perbarui = () => {
      const n = kotak.filter((k) => k.checked).length;
      semua.checked = n > 0 && n === kotak.length;
      semua.indeterminate = n > 0 && n < kotak.length;
      if (panel) panel.hidden = n === 0;
      if (jumlah) jumlah.textContent = String(n);
      kotak.forEach((k) => k.closest('tr')?.classList.toggle('baris-aktif', k.checked));
    };
    semua.addEventListener('change', () => { kotak.forEach((k) => { k.checked = semua.checked; }); perbarui(); });
    kotak.forEach((k) => k.addEventListener('change', perbarui));
    perbarui();
  });
}

function aturAutoSubmit() {
  document.querySelectorAll('form[data-auto-submit] select').forEach((s) => s.addEventListener('change', () => s.form.requestSubmit()));
}

function aturPintasan() {
  if (!document.querySelector('.dasbor')) return;
  const baris = () => [...document.querySelectorAll('.tabel .tautan-baris')];
  document.addEventListener('keydown', (e) => {
    const t = e.target;
    if (e.ctrlKey || e.metaKey || e.altKey || t.matches('input, textarea, select, [contenteditable]')) return;
    if (e.key === '/') {
      const cari = document.getElementById('cari-cepat');
      if (cari) { e.preventDefault(); cari.focus(); }
    } else if (e.key === 'j' || e.key === 'k') {
      const daftar = baris();
      if (!daftar.length) return;
      const i = daftar.indexOf(document.activeElement);
      const j = e.key === 'j' ? Math.min(daftar.length - 1, i + 1) : Math.max(0, i === -1 ? 0 : i - 1);
      daftar[j].focus();
      daftar.forEach((a, n) => a.closest('tr')?.classList.toggle('baris-aktif', n === j));
    } else if (e.key === 't') {
      const tanggapan = document.getElementById('isi_tanggapan');
      if (tanggapan) { e.preventDefault(); tanggapan.focus(); tanggapan.scrollIntoView({ block: 'center' }); }
    }
  });
}

function aturFokusError() {
  document.querySelector('.ringkasan-error[data-fokus]')?.focus();
}

function aturDemoCara() {
  const akar = document.querySelector('[data-demo-cara]');
  if (!akar) return;
  const info = JSON.parse(akar.dataset.status);
  const alur = JSON.parse(akar.dataset.alur);
  const [baru] = alur;
  const ditolak = Object.keys(info).find((k) => !alur.includes(k));
  const diteruskan = akar.querySelector('[data-demo-bidang]:not([data-demo-bidang="' + ditolak + '"])')?.dataset.demoBidang;
  const ikon = (id) => `<svg class="ikon" aria-hidden="true" focusable="false"><use href="${akar.dataset.ikon}#${id}"></use></svg>`;
  const el = (sel) => akar.querySelector(sel);
  const semua = (sel) => [...akar.querySelectorAll(sel)];
  const tahun = new Date().getFullYear();
  const kirim = el('[data-demo-kirim]');
  const teksKirim = kirim.querySelector('span');
  const tracker = el('.tracker');
  const tahap = tracker.querySelector('.tahap');
  const opsi = el('[data-demo-opsi]');
  let urut = 123;
  let state;

  const awal = () => ({
    status: baru,
    nomor: `LP-${tahun}-${String(urut).padStart(6, '0')}`,
    jenis: el('input[name="demo_jenis"]:checked')?.value || '',
    rahasia: el('[data-demo-rahasia]').checked,
    tujuan: null,
  });

  const tampilBidang = () => {
    const pilih = opsi.querySelector('input:checked')?.value;
    semua('[data-demo-bidang]').forEach((b) => { b.hidden = b.dataset.demoBidang !== pilih; });
  };

  const gambarAdmin = () => {
    const s = info[state.status];
    semua('[data-demo-nomor]').forEach((n) => { n.textContent = state.nomor; });
    el('[data-demo-lencana]').innerHTML = `<span class="status status--${s.slug}">${ikon(s.ikon)}</span>`;
    el('[data-demo-lencana] .status').append(s.petugas);
    el('[data-demo-jenis]').textContent = state.jenis;
    el('[data-demo-pelapor]').textContent = state.rahasia ? 'Dirahasiakan' : 'Pelapor Demo';
    opsi.replaceChildren(...s.lanjut.map((v, i) => {
      const item = document.createElement('div');
      item.className = 'pilihan__item';
      item.innerHTML = `<input type="radio" id="demo-status-${v}" name="demo_status" value="${v}"${i === 0 ? ' checked' : ''}><label for="demo-status-${v}">${ikon(info[v].ikon)} </label>`;
      item.querySelector('label').append(info[v].petugas);
      return item;
    }));
    el('[data-demo-form]').hidden = s.lanjut.length === 0;
    el('[data-demo-tuntas]').hidden = s.lanjut.length !== 0;
    tampilBidang();
  };

  const gambarTracker = () => {
    const s = info[state.status];
    const posisi = alur.indexOf(state.status);
    tracker.className = `tracker tracker--${s.slug}`;
    tracker.querySelector('.tracker__ikon').innerHTML = ikon(s.ikon);
    tracker.querySelector('.tracker__judul').textContent = s.warga;
    tracker.querySelector('.tracker__penjelasan').textContent = s.penjelasan;
    tracker.querySelector('.tracker__update time').textContent = 'Baru saja';
    tracker.querySelector('[data-demo-tujuan]')?.remove();
    if (state.tujuan) {
      const p = document.createElement('p');
      p.className = 'tracker__penjelasan';
      p.dataset.demoTujuan = '';
      p.innerHTML = `${ikon('send')} Diteruskan ke <strong></strong>`;
      p.querySelector('strong').textContent = state.tujuan;
      tracker.querySelector('.tracker__update').before(p);
    }
    tahap.querySelectorAll('li').forEach((li, i) => {
      li.toggleAttribute('data-lewat', i < posisi);
      li.toggleAttribute('data-kini', i === posisi);
      if (i === posisi) li.setAttribute('aria-current', 'step'); else li.removeAttribute('aria-current');
      li.querySelector('.sr-only')?.remove();
      if (i < posisi) li.insertAdjacentHTML('beforeend', '<span class="sr-only">(selesai)</span>');
    });
    tahap.hidden = state.status === ditolak;
    tracker.querySelector('.alasan')?.remove();
    if (state.status === ditolak) {
      const alasan = document.createElement('div');
      alasan.className = 'alasan';
      alasan.innerHTML = '<p class="tebal">Alasan dari admin</p><p></p>';
      alasan.lastChild.textContent = el('#demo-alasan').value.trim();
      tracker.append(alasan);
    }
    el('[data-demo-ulang]').hidden = info[state.status].lanjut.length !== 0;
  };

  const gambar = () => { gambarAdmin(); gambarTracker(); };

  kirim.addEventListener('click', () => {
    urut += 1;
    state = awal();
    el('[data-demo-terkirim]').hidden = false;
    teksKirim.textContent = 'Kirim laporan lain';
    gambar();
  });

  opsi.addEventListener('change', tampilBidang);

  el('[data-demo-simpan]').addEventListener('click', () => {
    const pilih = opsi.querySelector('input:checked')?.value;
    if (!pilih) return;
    const wajib = el(`[data-demo-bidang="${pilih}"] input, [data-demo-bidang="${pilih}"] textarea`);
    if (wajib && !wajib.value.trim()) { wajib.focus(); return; }
    if (pilih === diteruskan) state.tujuan = wajib.value.trim();
    state.status = pilih;
    gambar();
  });

  el('[data-demo-ulang]').addEventListener('click', () => {
    urut = 123;
    state = awal();
    el('[data-demo-terkirim]').hidden = true;
    teksKirim.textContent = 'Kirim laporan';
    gambar();
    kirim.focus();
  });

  state = awal();
  gambar();
}

aturSnackbar();
aturTema();
aturLaci();
aturSidebar();
aturMenuAkun();
aturKonfirmasi();
aturKirim();
aturSandi();
aturSalin();
aturFoto();
aturWilayah();
aturPenghitung();
aturDraf();
aturPilihSemua();
aturAutoSubmit();
aturPintasan();
aturFokusError();
aturDemoCara();
document.querySelectorAll('details[data-buka-desktop]').forEach((el) => {
  const mq = matchMedia('(min-width: 768px)');
  const atur = () => { if (mq.matches) el.open = true; };
  atur();
  mq.addEventListener('change', atur);
  if (location.search.length > 1) el.open = true;
});
