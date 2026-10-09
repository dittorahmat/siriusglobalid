# Proposal

## Why

Situs siriusglobal.id saat ini statis (16 halaman HTML, konten hardcode + `js/i18n.js` ~190 keys + `js/site-config.js`). Setiap perubahan teks harus edit file + push git, tidak bisa dilakukan editor non-teknis. Perlu CMS ringan yang jalan di cPanel shared hosting (PHP saja, tanpa proses persisten), dengan editor tunggal dan form kontak tetap WA deep link tanpa inbox.

## What Changes

- Refactor 16 halaman `.html` menjadi template PHP (`templates/layout.php`, `header.php`, `footer.php`) dengan render konten dari JSON, visual/CSS/JS tidak berubah.
- Tambah JSON content store di luar `public_html` (`settings.json`, `home.json`, `services/*.json`, `portfolio.json`, `i18n.id/en.json`) + lapisan `includes/store.php` (`store_json.php`: `flock` + atomic rename + validasi skema minimal).
- Tambah panel admin `/admin/` (login session + CSRF + rate-limit, 1 user): form edit per section (hero, stats, why, process, cases, testimoni, 9 layanan, portofolio, i18n, settings), upload foto (allowlist MIME, batas size, rename acak), tombol backup/download + restore validasi.
- Jaga URL/SEO: rewrite `.html` -> `.php`, update `sitemap.xml`, deploy `.cpanel.yml` + `gh-deploy.php` tidak pernah menimpa `data/*.json` produksi.
- Bilingual ID/EN tetap: tiap field ada pasangan ID/EN, pola `data-i18n` di frontend tidak berubah.

## Capabilities

### New Capabilities
- `cms-content`: JSON content store + render PHP + i18n ID/EN + proteksi deploy (data produksi tidak tertimpa).
- `cms-admin`: autentikasi admin tunggal + CRUD konten via form + upload media + backup/restore + validasi anti-korup.

### Modified Capabilities
- Tidak ada (belum ada spec existing; inventaris `openspec list --specs` kosong).

## Impact

- Terpengaruh: `index.html`, `tentang.html`, `layanan.html`, `portofolio.html`, `kontak.html`, `privasi.html`, `syarat.html`, `layanan/*.html` (9 file), `js/site-config.js` (diganti `settings.json`), `js/i18n.js` (sumber jadi JSON), `.cpanel.yml`, `gh-deploy.php`, `sitemap.xml`, `.htaccess` (baru).
- Tidak berubah: desain visual, CSS (`tokens/base/components/pages`), voice copy, struktur URL/slug, label nav utama.
- Wajib lolos sebelum ship (AGENTS.md): review `better-interface`, taste check `design-taste-frontend` (redesign-preserve, vanilla tetap), cek mobile-friendly (360/768/1280, hamburger, tap target, 16px input, zoom 200%), serve check 200.
