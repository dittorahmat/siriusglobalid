# Design

## Context

Lihat `proposal.md` untuk motivasi. State saat ini: situs PHP/JSON vanilla (`templates/layout.php`, `header.php`, `content-*.php`, CSS tokens/base/components/pages, `includes/store_json.php` dengan `flock` + atomic rename, data runtime `$HOME/data/*.json`, seed `data/seed/*.json` bilingual `{id,en}`). Target: WordPress di root `public_html`, MySQL via Softaculous, PHP 8.2 per-domain (sudah aktif), theme ID-only. Lihat `specs/wp-theme-id/spec.md` dan `specs/wp-migrate/spec.md` untuk kontrak perilaku.

## Goals / Non-Goals

**Goals:**
- Satu theme `sirius-custom` yang mem-port visual 1:1 tanpa builder, tanpa i18n runtime.
- CPT `layanan` terbuka (tambah/ubah/hapus via wp-admin, tanpa daftar slug kunci) + taksonomi portfolio + pages dengan batas list yang sama seperti validasi JSON lama.
- Importer idempoten seed sisi `id` (menerima slug apa pun) dan redirect 301 + sitemap dinamis yang menjaga SEO saat layanan bertambah/berkurang.
- Admin `mudita` + alur deploy theme-only yang tidak menimpa DB/uploads.

**Non-Goals:**
- Tidak ada dukungan EN/Polylang/mesin translate; sisi `en` diabaikan permanen di change ini.
- Tidak ada SQLite, tidak ada migrasi bertahap coexistence `/wp/`; cutover root sekali.
- Tidak ada redesign visual, tidak ada perubahan copy ID, tidak ada page builder.

## Decisions

- **MySQL Softaculous atas SQLite.** Rationale: operasional standar (backup cPanel, kredensial terkelola), tanpa plugin `sqlite-database-integration`. Alternatif SQLite ditolak user karena ribet.
- **Port template manual, bukan block theme.** Rationale: `content-home.php` (hero/kpi/stats/cases) dan `content-service.php` (tiers/steps/faq/cta) punya struktur bespoke; port PHP klasik (`front-page.php`, `single-layanan.php`, `archive`, `page-*.php`, `header/footer.php`, `functions.php` enqueue CSS lama) menjaga pixel-parity. Alternatif block/FSE ditolak karena risiko merusak layout.
- **Hapus i18n runtime.** Rationale: `cms_e()/cms_t()` + `data-i18n` + `js/i18n.js` tidak ada gunanya di ID-only; theme echo string ID langsung dari postmeta/options. Toggle header dihapus.
- **Sisi `id` sebagai sumber, validasi batas dipertahankan.** Rationale: batas (`tiers<=6`, `faqs<=20`, dst dari `cms_limits()`) adalah proteksi layout; importer dan `save_post` validation menegakkannya di WP walau storage pindah ke MySQL.
- **Permalink dinamis + redirect 301 eksplisit untuk URL lama.** Rationale: `.htaccess` lama (`RewriteRule ^layanan\.html$` dsb) diganti aturan WP + redirect map `.html`/`.php` ke kanonis; sitemap memakai sitemap inti WP yang otomatis mengikuti CPT sehingga layanan baru/hilang ikut tanpa edit manual; menghindari 404 massal.
- **Kredensial di luar repo.** Rationale: repo publik; `mudita` dibuat di Dashboard, DB creds milik Softaculous/`wp-config.php` (tidak di-commit); importer baca seed dari checkout lokal, bukan dari server.

## Risks / Trade-offs

- [Timpa root menghapus situs lama] → Mitigasi: user backup `public_html` + `$HOME/data` + DB list sebelum install; verifikasi backup dapat diunduh; cutover hanya setelah theme+importer siap di staging/local.
- [Drift DB vs git] → Mitigasi: git hanya theme+importer; konten adalah data; rilis theme tidak menyentuh DB/uploads; backup terjadwal cPanel/Softaculous.
- [EN kosong diabaikan bisa mengejutkan] → Mitigasi: dinyatakan eksplisit di proposal/specs; tidak ada fallback EN; switcher dihapus agar tidak ada dead-end bahasa.
- [Update WP dashboard merusak theme] → Mitigasi: pin versi di staging dulu, theme tanpa dependensi builder, uji 200 + visual check tiap update minor.
- [Media picsum seed vs uploads] → Mitigasi: importer memakai URL seed apa adanya tahap 1; sideload ke Media Library sebagai follow-up agar tidak memblokir cutover.

## Migration Plan

1. Bekukan konten JSON; backup dan catat hash seed.
2. User install WP di root via Softaculous (PHP 8.2, MySQL baru); biarkan theme default.
3. Pasang `sirius-custom`, jalankan importer di staging, verifikasi counts (settings/home/semua layanan seed/portfolio/tentang/kontak/dok).
4. Terapkan redirect 301 + sitemap/robots, uji semua URL lama dan baru 200.
5. Buat `mudita`, paksa ganti password, hapus/amankan user install default.
6. Lolos `better-interface` + `design-taste-frontend` + mobile check, lalu cutover DNS/prod bila staging OK.
7. Rollback: restore backup file+DB cPanel/Softaculous; situs PHP lama dapat dihidupkan kembali dari backup karena seed di git.

## Open Questions

- Struktur permalink final (`/%postname%/` vs `/layanan/%postname%/`) diputuskan saat apply agar cocok dengan redirect map; tidak mengubah specs.
- Media di-sideload saat import atau tahap 2; default tahap 2 agar cutover cepat.
