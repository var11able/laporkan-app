const SVG = 'http://www.w3.org/2000/svg';
const kurangiGerak = matchMedia('(prefers-reduced-motion: reduce)');
let urutan = 0;

const buat = (tag, atribut = {}, induk = null) => {
  const el = document.createElementNS(SVG, tag);
  Object.entries(atribut).forEach(([k, v]) => el.setAttribute(k, v));
  induk?.append(el);
  return el;
};

const lerp = (a, b, t) => a + (b - a) * t;
const keluarPelan = (t) => (t === 1 ? 1 : 1 - 2 ** (-10 * t));

function animasikan(durasi, langkah) {
  return new Promise((selesai) => {
    if (kurangiGerak.matches || durasi <= 0) { langkah(1); selesai(); return; }
    const mulai = performance.now();
    const tik = (kini) => {
      const t = Math.min(1, (kini - mulai) / durasi);
      langkah(keluarPelan(t));
      if (t < 1) requestAnimationFrame(tik); else selesai();
    };
    requestAnimationFrame(tik);
  });
}

export default function pasang(akar, { indonesia, dunia }) {
  const { d3, topojson } = window;
  const data = JSON.parse(akar.dataset.sebaran);
  const bola = akar.dataset.peta === 'bola';
  const kanvas = akar.querySelector('[data-peta-kanvas]');
  const tip = akar.querySelector('[data-peta-tip]');
  const petunjuk = akar.querySelector('[data-peta-petunjuk]');
  const reset = akar.querySelector('[data-peta-reset]');
  const daftar = [...(akar.closest('.peta')?.querySelectorAll('.peta__daftar a[data-kode]') ?? [])];
  const info = new Map(data.provinsi.map((p) => [p.kode, p]));
  const fitur = topojson.feature(indonesia, indonesia.objects.provinsi).features;
  const daratan = topojson.feature(dunia, dunia.objects.land);
  const kumpulan = { type: 'FeatureCollection', features: fitur };
  const tengah = new Map(fitur.map((f) => [f.properties.kode, d3.geoCentroid(f)]));
  const awal = data.rotasi.slice(0, 2);
  const rotasi = [...awal];

  const proyeksi = bola ? d3.geoOrthographic().clipAngle(90) : d3.geoMercator();
  const jalur = d3.geoPath(proyeksi);

  const svg = buat('svg', { role: 'img', 'aria-labelledby': akar.dataset.ringkasan, preserveAspectRatio: 'xMidYMid meet' }, kanvas);
  let laut; let kilau; let tepi;
  if (bola) {
    const idKilau = `peta-kilau-${++urutan}`;
    const defs = buat('defs', {}, svg);
    const gradien = buat('radialGradient', { id: idKilau, cx: '32%', cy: '18%', r: '75%' }, defs);
    const s0 = buat('stop', { offset: '0%' }, gradien);
    const s1 = buat('stop', { offset: '100%' }, gradien);
    s0.style.stopColor = 'var(--peta-sorot)';
    s1.style.stopColor = 'var(--peta-sorot)';
    s1.style.stopOpacity = '0';
    laut = buat('path', { class: 'peta__laut' }, svg);
    kilau = buat('path', { fill: `url(#${idKilau})` }, svg);
    kilau.style.pointerEvents = 'none';
  }
  const darat = buat('path', { class: 'peta__daratan' }, svg);
  const grupProv = buat('g', {}, svg);
  const grupTitik = buat('g', {}, svg);
  if (bola) tepi = buat('path', { class: 'peta__tepi' }, svg);

  const jalurProv = new Map();
  fitur.forEach((f) => {
    const kode = f.properties.kode;
    const p = info.get(kode);
    const el = buat('path', { class: 'peta__prov peta__prov--0' }, grupProv);
    el.dataset.kode = kode;
    el.dataset.kelas = String(p?.kelas ?? 0);
    jalurProv.set(kode, { el, f });
  });

  const titik = bola
    ? data.provinsi.filter((p) => p.baru && tengah.has(p.kode)).map((p) => ({ kode: p.kode, el: buat('circle', { class: 'peta__penanda', r: 5 }, grupTitik) }))
    : [];

  let lebar = 0;
  let tinggi = 0;

  function gambar() {
    if (bola) {
      proyeksi.rotate(rotasi);
      const bulat = jalur({ type: 'Sphere' }) || '';
      laut.setAttribute('d', bulat);
      kilau.setAttribute('d', bulat);
      tepi.setAttribute('d', bulat);
    }
    darat.setAttribute('d', jalur(daratan) || '');
    jalurProv.forEach(({ el, f }) => el.setAttribute('d', jalur(f) || ''));
    const pusat = [-rotasi[0], -rotasi[1]];
    titik.forEach(({ kode, el }) => {
      const c = tengah.get(kode);
      const terlihat = d3.geoDistance(c, pusat) < Math.PI / 2 - 0.08;
      el.style.display = terlihat ? '' : 'none';
      if (!terlihat) return;
      const [x, y] = proyeksi(c);
      el.setAttribute('cx', x.toFixed(1));
      el.setAttribute('cy', y.toFixed(1));
    });
  }

  function ukur() {
    lebar = kanvas.clientWidth;
    tinggi = kanvas.clientHeight;
    if (!lebar || !tinggi) return;
    svg.setAttribute('viewBox', `0 0 ${lebar} ${tinggi}`);
    if (bola) {
      const r = Math.min(tinggi * 1.4, lebar * (lebar < 700 ? 1.1 : 0.68));
      proyeksi.scale(r).translate([lebar / 2, tinggi * 0.04 + r]);
    } else {
      proyeksi.fitExtent([[6, 6], [lebar - 6, tinggi - 6]], kumpulan);
    }
    gambar();
  }

  function warnai(langsung) {
    const urut = data.provinsi.map((p) => p.kode);
    jalurProv.forEach(({ el }) => {
      const tunda = langsung || kurangiGerak.matches ? 0 : 500 + Math.max(0, urut.indexOf(el.dataset.kode)) * 40;
      const terap = () => el.setAttribute('class', `peta__prov peta__prov--${el.dataset.kelas}`);
      if (tunda) setTimeout(terap, tunda); else terap();
    });
  }

  let sorotan = null;
  function sorot(kode) {
    if (sorotan === kode) return;
    if (sorotan) {
      jalurProv.get(sorotan)?.el.classList.remove('is-sorot');
      daftar.forEach((a) => a.classList.toggle('is-sorot', false));
    }
    sorotan = kode;
    if (!kode) return;
    const target = jalurProv.get(kode)?.el;
    if (target) { target.classList.add('is-sorot'); grupProv.append(target); }
    daftar.forEach((a) => a.classList.toggle('is-sorot', a.dataset.kode === kode));
  }

  function tampilTip(kode, x, y, tetap) {
    const p = info.get(kode);
    if (!p) { sembunyikanTip(); return; }
    tip.replaceChildren();
    const nama = document.createElement('strong');
    nama.textContent = p.nama;
    const jumlah = document.createElement('p');
    jumlah.textContent = `${p.label} laporan${data.samar ? ' terverifikasi' : ''}`;
    tip.append(nama, jumlah);
    if (tetap) {
      const tautan = document.createElement('a');
      tautan.href = data.tautan + p.id;
      tautan.textContent = 'Lihat laporan →';
      tip.append(tautan);
    }
    tip.classList.toggle('is-tetap', tetap);
    tip.hidden = false;
    const kotak = akar.getBoundingClientRect();
    const lebarTip = tip.offsetWidth;
    const tinggiTip = tip.offsetHeight;
    tip.style.left = `${Math.max(8, Math.min(kotak.width - lebarTip - 8, x + 14))}px`;
    tip.style.top = `${Math.max(8, Math.min(kotak.height - tinggiTip - 8, y - tinggiTip - 14))}px`;
  }

  function sembunyikanTip() {
    tip.hidden = true;
    tip.classList.remove('is-tetap');
  }

  const posisi = (e) => {
    const kotak = akar.getBoundingClientRect();
    return [e.clientX - kotak.left, e.clientY - kotak.top];
  };

  let seret = null;
  let tundaKembali = 0;
  let abaikanKlik = false;

  function kembali(durasi = 900) {
    clearTimeout(tundaKembali);
    const dari = [...rotasi];
    return animasikan(durasi, (t) => {
      rotasi[0] = lerp(dari[0], awal[0], t);
      rotasi[1] = lerp(dari[1], awal[1], t);
      gambar();
    }).then(() => { if (reset) reset.hidden = true; });
  }

  svg.addEventListener('pointerdown', (e) => {
    if (!bola || e.button > 0) return;
    clearTimeout(tundaKembali);
    seret = { x: e.clientX, y: e.clientY, r: [...rotasi], gerak: false, id: e.pointerId };
  });

  svg.addEventListener('pointermove', (e) => {
    if (seret && seret.id === e.pointerId) {
      const dx = e.clientX - seret.x;
      const dy = e.clientY - seret.y;
      if (!seret.gerak && Math.hypot(dx, dy) > 4) {
        seret.gerak = true;
        svg.setPointerCapture(e.pointerId);
        sembunyikanTip();
        if (petunjuk) petunjuk.hidden = true;
      }
      if (seret.gerak) {
        rotasi[0] = seret.r[0] + dx * 0.25;
        rotasi[1] = Math.max(12, Math.min(50, seret.r[1] - dy * 0.25));
        gambar();
        return;
      }
    }
    if (e.pointerType !== 'mouse') return;
    const kode = e.target.closest?.('.peta__prov')?.dataset.kode ?? null;
    sorot(kode);
    if (kode) { const [x, y] = posisi(e); tampilTip(kode, x, y, false); } else sembunyikanTip();
  });

  const akhiriSeret = (e) => {
    if (!seret || seret.id !== e.pointerId) return;
    const bergerak = seret.gerak;
    seret = null;
    if (!bergerak) return;
    abaikanKlik = true;
    setTimeout(() => { abaikanKlik = false; }, 0);
    if (reset) reset.hidden = Math.abs(rotasi[0] - awal[0]) < 5 && Math.abs(rotasi[1] - awal[1]) < 5;
    tundaKembali = setTimeout(() => kembali(), 3000);
  };
  svg.addEventListener('pointerup', akhiriSeret);
  svg.addEventListener('pointercancel', akhiriSeret);

  svg.addEventListener('pointerleave', (e) => {
    if (e.pointerType === 'mouse' && !seret) { sorot(null); sembunyikanTip(); }
  });

  svg.addEventListener('click', (e) => {
    if (abaikanKlik) return;
    const kode = e.target.closest?.('.peta__prov')?.dataset.kode;
    const p = kode ? info.get(kode) : null;
    if (e.pointerType === 'mouse' || e.pointerType === '' || e.detail === 0) {
      if (p) location.href = data.tautan + p.id;
      return;
    }
    if (p) { sorot(kode); const [x, y] = posisi(e); tampilTip(kode, x, y, true); } else { sorot(null); sembunyikanTip(); }
  });

  document.addEventListener('pointerdown', (e) => {
    if (!tip.hidden && tip.classList.contains('is-tetap') && !akar.contains(e.target)) { sorot(null); sembunyikanTip(); }
  });

  daftar.forEach((a) => {
    const nyala = () => sorot(a.dataset.kode);
    const padam = () => sorot(null);
    a.addEventListener('mouseenter', nyala);
    a.addEventListener('mouseleave', padam);
    a.addEventListener('focus', nyala);
    a.addEventListener('blur', padam);
  });

  reset?.addEventListener('click', () => kembali());

  new ResizeObserver(ukur).observe(kanvas);
  ukur();

  if (bola && !kurangiGerak.matches) {
    const mulaiLambda = awal[0] + 70;
    rotasi[0] = mulaiLambda;
    gambar();
    warnai(false);
    animasikan(1600, (t) => { rotasi[0] = lerp(mulaiLambda, awal[0], t); gambar(); })
      .then(() => { if (petunjuk && !seret) petunjuk.hidden = false; });
  } else {
    warnai(true);
    if (bola && petunjuk) petunjuk.hidden = false;
  }
}
