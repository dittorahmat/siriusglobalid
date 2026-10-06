# AGENTS.md — Sirius Global Indonesia (siriusglobal.id rebuild)

## Stack

- Vanilla HTML + CSS + JS. No framework, no build step, no bundler.
- Static hosting (cPanel / Nginx / GitHub Pages). Test locally with:
  `python -m http.server 8000` lalu buka `http://localhost:8000/`
- Fonts via Google Fonts. Bilingual ID (default) / EN via `js/i18n.js` (`data-i18n`, `data-i18n-html`).
- Satu-satunya config yang boleh diedit untuk konten placeholder: `js/site-config.js`.

## WAJIB: review frontend via better-interface

Setiap perubahan frontend (HTML, CSS, JS yang menyentuh tampilan/perilaku UI,
termasuk copy) **HARUS** melalui skill `better-interface` sebelum dianggap selesai:

1. Load skill `better-interface` (ia mengorkestrasi `better-accessibility`,
   `better-layout`, `better-writing`, `better-typography`, `better-colors`, `better-ui`).
2. Scope = file/halaman yang diubah + komponen bersama yang terdampak
   (`css/*.css`, `js/main.js`, `js/i18n.js`, header/footer).
3. Kumpulkan bukti (`file:line`), ukur kontras dari nilai token aktual
   (jangan estimasi), dan laporkan dalam format review skill tersebut.
4. `Block` = ada temuan HIGH yang belum diperbaiki. Jangan ship sebelum HIGH bersih.
5. Implementasikan temuan yang disetujui, lalu verifikasi ulang
   (ukur ulang kontras + serve check 200).

## WAJIB: taste check via design-taste-frontend

Selain review `better-interface`, setiap perubahan yang menyentuh desain
visual/komposisi (hero, section baru, kartu, foto, tipografi, warna, motion)
**HARUS** lolos skill `design-taste-frontend` (mode redesign-preserve,
stack vanilla tetap, tanpa migrasi framework):

1. Nyatakan design read satu baris + nilai dial
   (`DESIGN_VARIANCE/MOTION_INTENSITY/VISUAL_DENSITY`).
2. Jalankan FINAL PRE-FLIGHT CHECK skill tersebut; yang paling sering
   kena di proyek ini: nol em-dash/en-dash di UI, eyebrow ≤ 1 per
   3 section, satu label per intent CTA, foto real (picsum seed) bukan
   fake dashboard div, tanpa pill/label di atas foto, tanpa step bernomor
   generik, tanpa status dot dekoratif, accent tunggal.
3. Jangan langgar yang dikunci: struktur URL/slug, label nav utama,
   `js/site-config.js` sebagai sumber placeholder, copy voice yang ada.
4. Ketidakpatuhan yang disengaja (mis. tanpa dark mode, tanpa React/Motion)
   harus dicatat eksplisit di laporan, bukan diabaikan diam-diam.

## WAJIB: mobile-friendly & responsive check

Setiap perubahan frontend juga **HARUS** lolos cek mobile-friendly ini
(lensa `better-layout` + `better-accessibility`):

1. **Viewport & scroll:** `meta viewport` ada di semua halaman; tidak ada
   horizontal scroll di 360px, 768px, 1280px (cek via devtools/serve).
2. **Grid runtuh:** dilarang `style="grid-template-columns:...` inline yang
   fixed. Pakai class `.grid-2/.grid-3/.grid-4` di `css/base.css`
   (otomatis 2 kolom di ≤960px, 1 kolom di ≤620px).
3. **Navigasi seluler:** menu hamburger + `.mobile-menu` harus bisa dibuka,
   semua link terjangkau, `aria-expanded` terupdate.
4. **Tap target:** kontrol interaktif min 24×24px (target 36–44px):
   `.menu-btn` 46px, `.lang-toggle button` min-height 36px, link nav/pill/FAQ
   mengandalkan padding.
5. **Form & teks:** input/select/textarea min 16px di mobile (anti iOS zoom),
   tidak ada teks terpotong/overlap di layar kecil, `.kpi-row` wrap.
6. **Zoom:** layout tetap utuh di zoom 200%.

Kegagalan poin 1–3 = `Block`. Sisanya `MEDIUM/LOW` di tabel review.

Pengecualian: perubahan non-frontend murni (`sitemap.xml`, `robots.txt`,
`site-config.js` saja) tidak wajib review, tapi tetap wajib serve check.

## CI/CD: deploy via cPanel Git Version Control (GitHub = source of truth)

- Alur resmi: push ke `main` di GitHub → di cPanel buka Git Version Control →
  clone `https://github.com/dittorahmat/siriusglobalid.git` ke
  `~/siriusglobalid` (JANGAN langsung ke `public_html` agar `.git` tidak
  terekspos) → klik **Update from Remote** lalu **Deploy**.
- File `.cpanel.yml` di repo root mengatur deploy: hanya file situs
  (`*.html`, `css/`, `js/`, `assets/`, `layanan/`, `robots.txt`,
  `sitemap.xml`) yang disalin ke `$HOME/public_html/`.
- Workflow FTP (`.github/workflows/deploy.yml`) sudah dihapus karena
  firewall hosting memblokir IP GitHub runner (`ETIMEDOUT` port 21).
  Jangan kembalikan tanpa hasil tes konektivitas dari runner.
- Opsi otomatis penuh: `gh-deploy.php` (di repo root, ikut ter-copy ke
  `public_html`) menerima GitHub webhook (push ke `main`, validasi
  `X-Hub-Signature-256`) lalu `git pull` + salin file situs. Setup sekali:
  ganti `GH_WEBHOOK_SECRET` di file itu via File Manager, lalu di GitHub
  repo Settings → Webhooks → Payload URL
  `https://siriusglobal.id/gh-deploy.php` + Secret yang sama. Cek hasil di
  `~/deploy.log`. Butuh `shell_exec` aktif di hosting.
