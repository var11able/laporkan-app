
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
  document.querySelectorAll('[data-tema-alih]').forEach((t) => t.addEventListener('click', () => {
    const kini = document.documentElement.dataset.theme || (matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    const tema = kini === 'dark' ? 'light' : 'dark';
    simpan.taruh('tema', tema);
    terapkan(tema);
  }));
}

function aturAppbar() {
  const appbar = document.querySelector('.ada-hero [data-appbar]');
  const hero = document.querySelector('.hero');
  if (!appbar || !hero || !('IntersectionObserver' in window)) return;
  new IntersectionObserver(([e]) => appbar.classList.toggle('appbar--melayang', !e.isIntersecting), { rootMargin: '-80px 0px 0px 0px' }).observe(hero);
}

function aturHitungNaik() {
  const angka = [...document.querySelectorAll('[data-hitung-naik]')];
  if (!angka.length || matchMedia('(prefers-reduced-motion: reduce)').matches || !('IntersectionObserver' in window)) return;
  const format = new Intl.NumberFormat('id-ID');
  const amati = new IntersectionObserver((entri) => entri.forEach(({ isIntersecting, target }) => {
    if (!isIntersecting) return;
    amati.unobserve(target);
    const akhir = Number(target.dataset.hitungNaik);
    const asli = target.textContent;
    const mulai = performance.now();
    const tik = (kini) => {
      const t = Math.min(1, (kini - mulai) / 900);
      target.textContent = format.format(Math.round(akhir * (1 - (1 - t) ** 3)));
      if (t < 1) requestAnimationFrame(tik); else target.textContent = asli;
    };
    requestAnimationFrame(tik);
  }), { threshold: 0.6 });
  angka.forEach((el) => { if (Number(el.dataset.hitungNaik) > 0) amati.observe(el); });
}

function aturVideo() {
  const kurangiGerak = matchMedia('(prefers-reduced-motion: reduce)').matches;
  document.querySelectorAll('video[data-video-otomatis]').forEach((v) => {
    const tombol = v.parentElement.querySelector('[data-video-alih]');
    const label = tombol?.getAttribute('aria-label').replace(/^Jeda /, '') ?? '';
    let dijedaPengguna = kurangiGerak;
    let terlihat = false;
    const perbarui = () => {
      if (!tombol) return;
      tombol.classList.toggle('is-jalan', !v.paused);
      tombol.setAttribute('aria-label', (v.paused ? 'Putar ' : 'Jeda ') + label);
    };
    v.addEventListener('play', perbarui);
    v.addEventListener('pause', perbarui);
    if (tombol) {
      tombol.hidden = false;
      tombol.addEventListener('click', () => {
        if (v.paused) { dijedaPengguna = false; v.play().catch(() => {}); } else { dijedaPengguna = true; v.pause(); }
      });
    }
    perbarui();
    if (!('IntersectionObserver' in window)) return;
    new IntersectionObserver(([e]) => {
      terlihat = e.isIntersecting;
      if (terlihat && !dijedaPengguna) v.play().catch(() => {});
      else if (!terlihat && !v.paused) v.pause();
    }, { threshold: 0.4 }).observe(v);
  });
}

function aturPeta() {
  const semua = [...document.querySelectorAll('[data-peta]')];
  if (!semua.length) return;
  const muatSkrip = (src) => new Promise((ok, gagal) => {
    const s = document.createElement('script');
    s.src = src;
    s.onload = ok;
    s.onerror = gagal;
    document.head.append(s);
  });
  const ambil = (url) => fetch(url).then((r) => { if (!r.ok) throw new Error(url); return r.json(); });
  let pustaka = null;
  const siapkan = (el) => {
    pustaka ||= (async () => {
      for (const src of JSON.parse(el.dataset.skrip)) await muatSkrip(src);
      const [indonesia, dunia, modul] = await Promise.all([ambil(el.dataset.geoIndonesia), ambil(el.dataset.geoDunia), import(el.dataset.modul)]);
      return { indonesia, dunia, pasang: modul.default };
    })();
    return pustaka;
  };
  const pasang = (el) => siapkan(el)
    .then(({ indonesia, dunia, pasang: p }) => p(el, { indonesia, dunia }))
    .catch(() => { el.hidden = true; });
  if (!('IntersectionObserver' in window)) { semua.forEach(pasang); return; }
  const amati = new IntersectionObserver((entri) => entri.forEach(({ isIntersecting, target }) => {
    if (!isIntersecting) return;
    amati.unobserve(target);
    pasang(target);
  }), { rootMargin: '400px 0px' });
  semua.forEach((el) => amati.observe(el));
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

aturSnackbar();
aturTema();
aturAppbar();
aturHitungNaik();
aturPeta();
aturVideo();
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
document.querySelectorAll('details[data-buka-desktop]').forEach((el) => {
  const mq = matchMedia('(min-width: 768px)');
  const atur = () => { if (mq.matches) el.open = true; };
  atur();
  mq.addEventListener('change', atur);
  if (location.search.length > 1) el.open = true;
});
