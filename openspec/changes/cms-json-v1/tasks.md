# Tasks

## 1. Template + store baca

- [x] 1.1 Pecah header/footer 16 halaman jadi `templates/header.php`, `footer.php`, `layout.php` dan verifikasi `diff` render beranda vs HTML lama identik (kecuali sumber teks)
- [x] 1.2 Implementasi `includes/store.php` + `store_json.php` (load per-domain/per-bahasa, fallback ID, fallback seed) dan verifikasi via `python -m http.server` + skrip baca tiap getter mengembalikan string benar
- [x] 1.3 Migrasi `site-config.js` + `i18n.js` ke `data/seed/settings.json`, `i18n.id/en.json` dan verifikasi toggle ID/EN beranda menampilkan pasangan bahasa yang benar

## 2. Tulis aman + validasi

- [x] 2.1 Implementasi tulis atomik (`LOCK_EX` + tmp+rename) + validasi skema (key wajib, allowlist `cat`, slug unik) + optimistic-lock `updated_at` dan verifikasi save invalid ditolak tanpa mengubah file di disk
- [x] 2.2 Migrasi isi awal 9 layanan + portofolio + home/tentang/kontak ke `data/seed/*.json` via skrip sekali jalan dan verifikasi tiap halaman render dari seed tanpa fallback
- [x] 2.3 Uji fallback konten rusak (JSON korup/hilang) dan verifikasi halaman tetap 200 + error tercatat di log

## 3. Admin auth + form

- [x] 3.1 Implementasi login/logout 1 user (`password_hash`, regenerate session, rate-limit 5/10 mnt, cookie HttpOnly+SameSite) dan verifikasi login salah 5x terkunci + login benar masuk dashboard
- [x] 3.2 Bangun form settings + home (hero/stats/why/process/cases/testimoni/CTA) ID+EN + CSRF per form dan verifikasi save tampil langsung di publik + `updated_at` bertambah + backup otomatis dibuat
- [x] 3.3 Bangun form 9 layanan + portofolio (termasuk filter `data-cat`) + editor i18n dan verifikasi kategori invalid ditolak + slug duplikat ditolak

## 4. Upload + backup

- [x] 4.1 Implementasi upload (`finfo`+`getimagesize`, allowlist jpeg/png/webp, maks 2MB, tolak ekstensi ganda, rename acak, `.htaccess` noexec) dan verifikasi upload valid tampil + upload `.php.jpg` ditolak
- [x] 4.2 Implementasi backup otomatis per save + tombol download ZIP + restore tervalidasi dan verifikasi restore rusak dibatalkan tanpa mengubah data aktif

## 5. URL, deploy, verifikasi akhir

- [x] 5.1 Tambah `.htaccess` rewrite `*.html`->`*.php` 301 + update `sitemap.xml` dan verifikasi URL lama + `?layanan=` tetap jalan
- [x] 5.2 Update `.cpanel.yml` + patch `gh-deploy.php` (lindungi `data/*.json` produksi + `uploads/`, seed saat fresh install) dan verifikasi simulasi deploy tidak menimpa data produksi
- [x] 5.3 Jalankan review `better-interface` + taste check `design-taste-frontend` + cek mobile (360/768/1280, hamburger, tap target, input 16px, zoom 200%) + serve check 200 dan verifikasi tidak ada temuan HIGH yang tersisa

## Workflow follow-up

- Arsipkan change setelah review proyek terpenuhi.
- Verifikasi hasil arsip dan sinkronisasi spec bila diperlukan.
