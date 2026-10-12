# Cutover statis → WordPress (Okt 2026)

Latar: Apache selalu menyajikan `index.html` statis sebelum WP jalan
(DirectoryIndex + Trac #35263), sehingga theme + logo Customizer tak terlihat
publik maupun di preview. Solusi: singkirkan front legacy.

- **Mekanisme**: rutin sekali-jalan di `gh-deploy.php` (blok CUTOVER).
  Jalan otomatis pada webhook push-ke-main pertama setelah kodenya ter-deploy.
  Idempoten via marker `~/.sgi-cutover-done`; guard revisi 2: jalan bila front
  BUKAN WordPress (`index.php` tanpa `wp-blog-header`) — mencakup kasus
  `index.html` sudah dihapus manual (CMS legacy tetap menyajikan situs).
  Bila front sudah WP, dilewati. Diuji 3 skenario lokal (penuh / tanpa
  index.html / sudah-WP) via webhook POST signed.
- **Aman**: rename ke `~/legacy-<ts>/` (bukan hapus), backup `.htaccess`
  ke `.htaccess.bak-<ts>`, tulis `index.php` standar WP + `.htaccess`
  (aturan WP + 301 `.html` lama + proteksi `data/` warisan). Semua di-log
  ke `~/deploy.log` (`cutover` + `rollback` di JSON respons).
- **Tak tersentuh**: `wp-*`, `wp-config.php`, `wp-content/`, `gh-deploy.php`,
  `data/`, `assets/uploads/`, `sitemap.xml`.
- **Uji**: fixture + webhook POST signed lokal, 10/10 PASS (termasuk
  idempotensi push kedua `ran=false`).
- **Rollback**: kembalikan file karantina + `.htaccess.bak-<ts>`, simpan
  permalink. Simpan karantina 7–14 hari.
- **Pasca-cutover**: resave Permalink, verifikasi `/` (logo, tanpa toggle
  ID/EN), redirect `.html`, `/wp-json/` hidup, editor blok normal kembali.
- **Status: SELESAI 12 Okt 2026 ~13:09 WIB.** Delivery JSON `ran:true`,
  `moved:22`, `missing:[]`, karantina `/home/sirj8632/legacy-20261012-130937`.
  Verifikasi luar: `/` disajikan theme `sirius-custom` (`custom-logo`
  `logo_sirius-FIX.png` 672×231 render di header), `/tentang.html` 301 ke
  `/tentang/`, `/wp-json/` 200 JSON. Simpan karantina s/d ~26 Okt 2026.
- **Temuan pasca-cutover (12 Okt 2026, ~14:45 WIB):** `layanan/*.php` legacy
  (9 file) TIDAK ikut dikarantina, sehingga `public_html/layanan/` tetap
  menjadi direktori fisik. Apache (mod_dir) mendahulukan direktori fisik di
  atas rewrite WP: `curl -I /layanan/` → 301 ke `/layanan/index/` (header
  Apache murni, tanpa `X-Redirect-By: WordPress`) → 404 theme. Arsip CPT
  `/layanan/` tak pernah tercapai; flush permalink tidak berpengaruh karena
  request tak sampai ke WP. Perbaikan kode: cutover revisi 3 di `gh-deploy.php`
  (`glob layanan/*.php` ikut dipindah). Karena marker sudah set, live TIDAK
  diperbaiki otomatis — bersih manual via File Manager: pindahkan isi
  `public_html/layanan/` (9× `*.php` + `.htaccess` bila ada) ke
  `~/legacy-20261012-130937/layanan/`, cek cPanel Domains > Redirects untuk
  aturan `layanan/index`, lalu tes `curl -I /layanan/` (harap 200) + resave
  permalink.
- **Resolusi (12 Okt 2026):** folder `public_html/layanan/` dihapus manual,
  verifikasi `curl`: `/layanan/` 200 arsip ("Sembilan kapabilitas", 9 kartu),
  `/layanan/app-development/` 200, `/layanan` 301 kanonis ke `/layanan/`.