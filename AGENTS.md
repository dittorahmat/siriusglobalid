# AGENTS.md — Sirius Global Indonesia (siriusglobal.id rebuild)

## Stack

- WordPress di `public_html` (instalasi Softaculous, MySQL) + custom theme
  `wp-content/themes/sirius-custom/` (ID-only, port 1:1 layout PHP/JSON lama).
  Git adalah source of truth HANYA untuk theme + importer (`wp-content/`,
  `tools/sgi-importer.php`, `data/seed/` read-only); core/plugin/uploads/DB
  ikut standar WP/Softaculous/cPanel backup, bukan git.
- CSS/JS theme di-port dari aset lama; i18n dan toggle bahasa dihapus (ID-only).
  Situs lama (`*.php` publik, `admin/`, `includes/`, `templates/`, `css/`,
  `js/`) dipertahankan sebagai sumber migrasi **read-only**.
- Fonts via Google Fonts. ID-only (multilanguage di-scrap di change
  `wp-mysql-id-only`; sisi `en` seed diabaikan).

## WAJIB: review frontend via better-interface

Setiap perubahan frontend (HTML, CSS, JS yang menyentuh tampilan/perilaku UI,
termasuk copy) **HARUS** melalui skill `better-interface` sebelum dianggap selesai:

1. Load skill `better-interface` (ia mengorkestrasi `better-accessibility`,
   `better-layout`, `better-writing`, `better-typography`, `better-colors`, `better-ui`).
2. Scope = file/halaman yang diubah + komponen bersama yang terdampak:
   `wp-content/themes/sirius-custom/assets/css/*.css`,
   `assets/js/{main,admin}.js`, header/footer, halaman admin.
3. Kumpulkan bukti (`file:line`), ukur kontras dari nilai token aktual
   (jangan estimasi), dan laporkan dalam format review skill tersebut.
4. `Block` = ada temuan HIGH yang belum diperbaiki. Jangan ship sebelum HIGH bersih.
5. Implementasikan temuan yang disetujui, lalu verifikasi ulang
   (kontras + `php tools/sgi-admin-harness.php` untuk halaman admin).
   Untuk change-scoped, pakai `interface-review` bila skill itu tersedia;
   jika tidak, review surface yang berubah dan catat coverage di `review.md`.

## WAJIB: taste check via design-taste-frontend

Selain review `better-interface`, setiap perubahan yang menyentuh desain
visual/komposisi (hero, section baru, kartu, foto, tipografi, warna, motion)
**HARUS** lolos skill `design-taste-frontend` (mode redesign-preserve,
stack vanilla tetap, tanpa migrasi framework):

1. Nyatakan design read satu baris + nilai dial
   (`DESIGN_VARIANCE/MOTION_INTENSITY/VISUAL_DENSITY`).
2. Jalankan FINAL PRE-FLIGHT CHECK skill. Override proyek: nol em-dash/en-dash
   di UI, eyebrow ≤ 1 per 3 section, satu label per intent CTA, foto real
   (picsum seed) bukan fake dashboard div, tanpa pill/label di atas foto,
   tanpa step bernomor generik, tanpa status dot dekoratif, accent tunggal.
3. Jangan langgar yang dikunci: struktur URL/slug, struktur/urutan/URL nav
   (teks label nav boleh diubah via Tampilan > Konten Situs),
   `js/site-config.js` sebagai sumber placeholder, copy voice yang ada.
4. Ketidakpatuhan yang disengaja (mis. tanpa dark mode, tanpa React/Motion)
   harus dicatat eksplisit di laporan, bukan diabaikan diam-diam.

## WAJIB: mobile-friendly & responsive check

Setiap perubahan frontend juga **HARUS** lolos cek mobile-friendly ini
(lensa `better-layout` + `better-accessibility`); detail generik ada di skill,
berikut tambahan proyek:

1. **Viewport & scroll:** `meta viewport` ada di `header.php`; tidak ada
   horizontal scroll di 360/768/1280. Admin: `php tools/sgi-admin-harness.php`.
2. **Grid runtuh:** tanpa inline `style="grid-template-columns:..."`; pakai
   `.grid-2/.grid-3/.grid-4` di `wp-content/themes/sirius-custom/assets/css/base.css`.
3. **Navigasi & tap target:** menu hamburger + `.mobile-menu` dapat dibuka,
   `aria-expanded` terupdate; kontrol interaktif min 24px (target 36-44px).
4. **Form & teks:** input/select/textarea min 16px di mobile; layout utuh
   pada zoom 200%.

Kegagalan poin 1-3 = `Block`. Sisanya `MEDIUM/LOW` di tabel review.

Pengecualian: perubahan non-frontend murni (`sitemap.xml`, `robots.txt`,
`site-config.js` saja) tidak wajib review, tapi tetap wajib serve check.

## WAJIB: review dua sumbu + bukti tersimpan

Setiap perubahan kode (theme, importer, tools, CSS/JS) **HARUS** melalui skill
`code-review` end to end sebelum dianggap selesai, bukan hanya perubahan frontend:

1. Tetapkan fixed point eksplisit (commit/tag/branch) dan pastikan diff non-kosong.
2. Jalankan dua sub-agent paralel: **Standards** (baca `AGENTS.md`,
   `GLOSSARY.md`, `README.md` theme, `docs/agents/*`, plus smell baseline
   Fowler ch.3) dan **Spec** (baca `proposal.md`, `specs/`, `design.md`,
   `tasks.md` di `openspec/changes/<change>/`).
3. Perbaiki semua temuan HIGH/HARD. Temuan yang sengaja tidak diperbaiki
   dicatat dengan alasan, bukan dihilangkan diam-diam.
4. Simpan laporan final di `openspec/changes/<change>/review.md`; bila tidak
   ada change OpenSpec, simpan di `docs/reviews/<YYYY-MM-DD>-<topik>.md`.
   Laporan di chat saja tidak dianggap bukti.
5. Setelah perbaikan, perbarui laporan itu dengan status per temuan dan hasil
   verifikasi (perintah + keluaran), bukan hanya "sudah diperbaiki".
6. Jangan arsipkan change atau ship sebelum `review.md` ada dan temuan
   HIGH/HARD bersih.
7. Laporan `better-interface` dan pre-flight `design-taste-frontend` untuk
   change yang sama digabung ke `review.md` itu (scope, bukti `file:line`,
   hasil verifikasi), bukan hanya dikirim di chat.

## WAJIB: YAGNI + guardrail environment

Solusi terkecil yang memenuhi acceptance; satu file/command lebih baik dari dua.
Prefer one-liner yang terbaca; pecah hanya jika dipakai >1 tempat atau sudah
tidak terbaca. Jangan tambah tool/abstraksi/check tanpa pemakai nyata.

1. Aturan mekanis baru (path/string terlarang, viewport, inline grid, em-dash)
   **HARUS** masuk `tools/sgi-static-check.php` dan job CI. Prosa saja bukan guardrail.
2. Path di AGENTS **HARUS** menunjuk theme aktif
   (`wp-content/themes/sirius-custom/assets/...`); root legacy read-only.
3. Perubahan UI/admin: jalankan `php tools/sgi-admin-harness.php` dan cek
   320-1280; catat hasilnya di `review.md`.
4. Check di `tools/` **HARUS** wired ke `.github/workflows/php-lint.yml` atau
   punya alasan manual di README. Check yang tidak pernah jalan dianggap mati.
5. Shell: PowerShell. Baca `docs/agents/shell-notes.md` sebelum loop/jaringan.
6. Usulan retro yang disetujui **HARUS** mendarat sebagai check/AGENTS di change
   yang sama, bukan hanya catatan chat.

## Agent skills

### Issue tracker

Issue dilacak di GitHub Issues repo ini via `gh` CLI. See `docs/agents/issue-tracker.md`.

### Triage labels

Label triase default lima peran kanonis. See `docs/agents/triage-labels.md`.

### Domain docs

Layout single-context (satu `GLOSSARY.md` + `docs/adr/` di root). See `docs/agents/domain.md`.

## CI/CD: deploy via cPanel Git Version Control (GitHub = source of truth)

- Alur resmi: push ke `main` di GitHub → di cPanel buka Git Version Control →
  clone `https://github.com/dittorahmat/siriusglobalid.git` ke
  `~/siriusglobalid` (JANGAN langsung ke `public_html` agar `.git` tidak
  terekspos) → klik **Update from Remote** lalu **Deploy**.
- File `.cpanel.yml` di repo root mengatur deploy: HANYA theme + importer
  (`wp-content/themes/sirius-custom/`, `tools/sgi-importer.php`) yang disalin
  ke instalasi WordPress. Core, plugin, `uploads`, `wp-config.php`, dan DB
  MySQL TIDAK PERNAH disentuh deploy; ikut backup Softaculous/cPanel.
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
- `gh-deploy.php` memuat rutin cutover sekali-jalan (Okt 2026): saat webhook
  push-ke-main pertama setelah kodenya ter-deploy, front statis legacy
  (`index.html`, `*.php` legacy, `css/`, `js/`, `admin/`, `includes/`,
  `templates/`) di-rename ke `~/legacy-<ts>/` (bukan hapus),
  `.htaccess` di-backup + ditulis ulang (aturan WP + 301 `.html` + proteksi
  `data/`), `index.php` standar WP dipulihkan. Marker
  `~/.sgi-cutover-done` membuatnya idempoten. Status di `docs/agents/cutover.md`.
