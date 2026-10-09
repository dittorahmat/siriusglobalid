# Design

## Context

Lihat `proposal.md` Why. Kondisi kini: 16 file HTML duplikat header/footer, konten hardcode, i18n di `js/i18n.js` (~190 keys), config di `js/site-config.js`, form kontak WA deep link tanpa backend, deploy cPanel via `.cpanel.yml` + `gh-deploy.php` (ada jalur tanpa `shell_exec`). Batasan: PHP shared hosting saja, tanpa composer di server, tanpa proses persisten, editor tunggal, form tetap WA link.

## Goals / Non-Goals

**Goals:**
- Satu template PHP bersama, visual 1:1 dengan HTML kini.
- Storage diabstraksi (`store.php`) agar v1 JSON bisa diganti SQLite/MySQL tanpa bongkar template/admin.
- Tulis aman untuk 1 editor (lock + atomik + validasi + backup).
- Deploy tidak pernah menghapus data produksi.

**Non-Goals:**
- Tanpa inbox pesan, draft/preview/versioning, multi-user/role, page builder, penambah section dinamis.
- Tanpa framework/composer di server, tanpa migrasi ke WordPress/headless.
- Tanpa ubah desain, copy voice, URL/slug, label nav.

## Decisions

1. **JSON flat, bukan SQLite (v1).** Rasional: editor tunggal + tulis jarang membuat transaksi SQL tidak dibutuhkan; JSON tanpa ekstensi, diff git kebaca, backup copy file. Alternatif SQLite ditolak untuk v1 karena `pdo_sqlite` belum terbukti aktif dan file binary tidak terdif. Jalan migrasi dijaga via interface `store.php` (`store_json.php` kini, `store_sqlite.php` kelak drop-in).
2. **Pecah JSON per domain + per bahasa.** `settings.json`, `home.json`, `tentang.json`, `services/<slug>.json` x9, `portfolio.json`, `i18n.id.json`/`i18n.en.json`. Alternatif satu file raksasa ditolak: susah diff, berisiko konflik tulis. Seed awal di `data/seed/` untuk fresh install.
3. **Template: `layout.php` + `header.php` + `footer.php` + partial per section.** Halaman publik (`public/*.php`) hanya memanggil `render()` + partial. Pola `data-i18n` dan class CSS dipertahankan agar JS existing (`main.js`, toggle bahasa, filter `data-cat`) tetap jalan tanpa ubah.
4. **Semua echo via helper escape; hanya field `_html` yang boleh HTML dan lewat sanitasi allowlist.** Mencegah XSS yang baru muncul saat konten dari DB/JSON. Alternatif editor HTML bebas ditolak.
5. **Admin minimal: 1 user, session file/cookie PHP native, `password_hash`, CSRF token per form, rate-limit file-based, tanpa lib auth.** Upload: cek `finfo` MIME + `getimagesize`, tolak ekstensi ganda, rename `bin2hex(random_bytes(12))`, simpan `assets/uploads/`, `.htaccess` matikan eksekusi PHP di sana.
6. **Deploy aman: `.cpanel.yml` + `gh-deploy.php` hanya menyalin kode (`*.php`, `templates/`, `includes/`, `css/`, `js/`, `assets/` kecuali `uploads/`, `layanan/` dialihkan ke template); `data/*.json` produksi + `uploads/` tidak pernah ditimpa.** Fresh install menyalin dari `data/seed/` bila kosong. Ini pelajaran dari CMS git-based yang editan hilang tiap push.
7. **URL dijaga: `.htaccess` rewrite `*.html` -> `*.php` (301), `sitemap.xml` update, breadcrumb dan deep link `kontak.php?layanan=` tetap.**

## Risks / Trade-offs

- [Risk] Dua tab save bersamaan menimpa -> Mitigasi: `flock LOCK_EX` + tmp+rename + optimistic-lock `updated_at` (lihat spec).
- [Risk] JSON korup karena edit manual via File Manager -> Mitigasi: validasi skema sebelum tulis + tolak save invalid + backup otomatis tiap save + tombol validasi/download.
- [Risk] Upload disalahgunakan -> Mitigasi: allowlist MIME, 2MB, rename acak, noexec `.htaccess`, tidak ada URL eksekusi.
- [Risk] Session fixation/hijack di shared hosting -> Mitigasi: `session_regenerate_id` saat login, cookie `HttpOnly`+`SameSite=Lax` (+`Secure` bila HTTPS), idle timeout.
- [Risk] Review desain gagal (AGENTS.md wajib) -> Mitigasi: tidak ada perubahan visual; verifikasi `better-interface` + `design-taste-frontend` + mobile 360/768/1280 + serve 200 sebelum merge.
- [Trade-off] Tanpa draft: save langsung live. Diterima untuk v1; editor tunggal + backup otomatis sebagai jaring pengaman.

## Migration Plan

1. Potong HTML jadi template + `store.php` baca JSON (halaman publik jalan dua mode: baca JSON bila ada, fallback konten hardcode kini bila tidak).
2. Tambah `.htaccess` rewrite + `sitemap.xml`; update `.cpanel.yml`; patch `gh-deploy.php` (daftar file + lindungi `data/`/`uploads/`).
3. Bangun `/admin/` di belakang login; migrasi isi awal dari HTML/`i18n.js`/`site-config.js` ke `data/seed/*.json` via skrip sekali jalan (tidak ikut deploy).
4. Dry-run di staging/subdomain: edit tiap form, cek ID/EN, upload, backup/restore, rewrite URL lama, serve check 200.
5. Go-live: salin seed -> data produksi sekali, serahkan kredensial + panduan backup. Rollback: kembalikan `public_html` ke commit sebelumnya; data JSON produksi tidak tersentuh.

## Open Questions

- Batas upload final 2MB atau ikut limit hosting (perlu cek `upload_max_filesize` aktual)? Tidak mengubah spec/tasks, diputuskan saat implementasi upload.
- Kredensial admin awal via env/file di HOME atau dibuat oleh skrip setup sekali jalan? Diputuskan saat implementasi auth (opsi default: file secret di HOME seperti pola `gh-deploy.php`).
