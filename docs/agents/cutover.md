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
