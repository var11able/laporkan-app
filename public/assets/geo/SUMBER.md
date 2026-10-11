# Sumber data peta

| Berkas | Sumber | Lisensi |
| :-- | :-- | :-- |
| `indonesia-provinsi.json` | geoBoundaries IDN ADM2 (gbOpen, commit `9469f09`), asal data: BPS, WFP, OCHA ROAP (2020) | CC BY 3.0 IGO |
| `dunia-110m.json` | `world-atlas@2.0.2` `land-110m.json` (Natural Earth) | ISC / domain publik |

geoBoundaries ADM1 untuk Indonesia baru berisi 34 provinsi, jadi 38 provinsi dibentuk dari batas kabupaten/kota (ADM2) yang digabung per provinsi memakai `app/Database/Seeds/data/wilayah.csv`. Kunci gabungan adalah kode BPS dua digit, sama dengan kolom `provinsi.kode`.

## Membuat ulang

1. Unduh `geoBoundaries-IDN-ADM2_simplified.geojson` dari tautan di atas.
2. Jalankan skrip di bawah: `node siapkan.js adm2.geojson app/Database/Seeds/data/wilayah.csv prep.geojson`.
3. `npx mapshaper@0.7.83 prep.geojson -dissolve2 kode copy-fields=nama -filter-islands min-area=15km2 remove-empty -simplify 3% keep-shapes -o indonesia-provinsi.json format=topojson quantization=1e5`
4. Ganti nama objek `prep` menjadi `provinsi`.

Nama kabupaten/kota di ADM2 dicocokkan ke CSV setelah normalisasi. Nama lama dipetakan manual (Toba Samosir → 12, Mahakam Hulu → 64, Maluku Tenggara Barat → 81, Mamuju Utara → 76, Siau Tagulandang Biaro → 71, kota administrasi Jakarta dan Kepulauan Seribu → 31, enklave "Hutan" → 33). Badan air (`Danau`, `Danau Toba`, `Waduk Cirata`, `Wadung Kedungombo`) dibuang.

```js
const fs = require('fs');
const [adm2Path, csvPath, outPath] = process.argv.slice(2);
const j = JSON.parse(fs.readFileSync(adm2Path, 'utf8'));
const rows = fs.readFileSync(csvPath, 'utf8').trim().split(/\r?\n/).slice(1).map(l => {
  const m = l.match(/^(\d+),("?)(.*?)\2,([\d.]+),"?(.*?)"?$/);
  return { kp: m[1], prov: m[3], nama: m[5] };
});
const norm = s => s.toLowerCase().replace(/^kabupaten\s+/, '').replace(/[^a-z]/g, '');
const idx = {};
for (const r of rows) (idx[norm(r.nama)] ||= []).push(r);
const nama = Object.fromEntries(rows.map(r => [r.kp, r.prov]));
const manual = {
  'Kepulauan Seribu': '31', 'Kota Jakarta Barat': '31', 'Kota Jakarta Pusat': '31', 'Kota Jakarta Selatan': '31',
  'Kota Jakarta Timur': '31', 'Kota Jakarta Utara': '31', 'Mahakam Hulu': '64', 'Maluku Tenggara Barat': '81',
  'Mamuju Utara': '76', 'Siau Tagulandang Biaro': '71', 'Toba Samosir': '12', 'Hutan': '33',
};
const air = new Set(['Danau', 'Danau Toba', 'Waduk Cirata', 'Wadung Kedungombo']);
const keluar = [];
for (const f of j.features) {
  const n = f.properties.shapeName;
  if (air.has(n)) continue;
  const c = idx[norm(n)];
  const kode = manual[n] ?? (c && c.length === 1 ? c[0].kp : null);
  if (!kode) throw new Error('Tidak cocok: ' + n);
  keluar.push({ type: 'Feature', properties: { kode, nama: nama[kode] }, geometry: f.geometry });
}
const jumlah = new Set(keluar.map(f => f.properties.kode)).size;
if (jumlah !== 38) throw new Error('Provinsi: ' + jumlah);
fs.writeFileSync(outPath, JSON.stringify({ type: 'FeatureCollection', features: keluar }));
console.log('fitur', keluar.length, 'provinsi', jumlah);
```
