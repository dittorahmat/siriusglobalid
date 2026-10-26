# Tasks: Theme Customizer, Hero, Service Enhancements, and Bilingual Locale Support

## 1. Customizer Visual Controls (Footer Color & Hero Photo)

- [x] 1.1 Daftarkan setting & kontrol Customizer `sgi_footer_bg` (Color Control) dan `sgi_hero_photo` (Media Control) di `wp-content/themes/sirius-custom/inc/logo.php`, dan verifikasi dengan `php -l`.
- [x] 1.2 Tambahkan injeksi inline style untuk warna latar `.site-footer` di `footer.php` / `header.php` saat `sgi_footer_bg` terisi, dan verifikasi tampilan output HTML.
- [x] 1.3 Perbarui pemanggilan `$hp` di `front-page.php` agar membaca ID attachment `sgi_hero_photo` dengan fallback ke konfigurasi default, dan verifikasi URL gambar hero ter-render dengan benar.

## 2. Hero Section Enhancements (Clickable Card & Conditional KPI)

- [x] 2.1 Tambahkan field input `sgi_hero_card1_href` pada panel Hero di `inc/options-fields.php` dan sanitasi di `inc/options-save.php`, lalu verifikasi form admin tersimpan dengan benar.
- [x] 2.2 Perbarui markup `.hero-photo-card` di `front-page.php` agar terbungkus tag `<a>` jika link terisi (lengkap dengan styling hover di `pages.css`), dan verifikasi fungsionalitas klik.
- [x] 2.3 Tambahkan logika kondisional pada blok `.dash-card` di `front-page.php` agar tidak di-render jika seluruh KPI bernilai 0 / kosong, dan verifikasi tidak ada kotak putih kosong yang tersisa saat dikosongkan.

## 3. Service Card & Admin Meta Fixes

- [x] 3.1 Perbarui parser meta `_sgi_tiers` di `inc/meta.php` agar memperlakukan input kosong sebagai array kosong `[]`, dan verifikasi post layanan berhasil disimpan tanpa error "Tiers bukan JSON valid".
- [x] 3.2 Dukung Featured Image pada kartu layanan di `front-page.php`: jika post layanan memiliki thumbnail, tampilkan tag `<img>` di dalam `.card-icon` dengan fallback simbol teks jika belum ada.
- [x] 3.3 Tambahkan styling CSS untuk `.card-icon img` di `components.css` agar gambar ikon tertata proporsional (`object-fit: contain`).

## 4. Bilingual Locale Support (English / Indonesian)

- [x] 4.1 Buat helper translasi `inc/i18n.php` dengan fungsi `sgi_t()` yang memeriksa `get_locale()` untuk mengembalikan string Inggris atau Indonesia.
- [x] 4.2 Terapkan `sgi_t()` pada `page-kontak.php` untuk seluruh label form (Nama lengkap, Perusahaan, Email kerja, Nomor WhatsApp, Kebutuhan, Rentang budget, Target waktu, Ceritakan singkat, tombol submit, pesan persetujuan privasi, dan info kontak).
- [x] 4.3 Terapkan `sgi_t()` pada teks antarmuka bersama (breadcrumb, footer copyright, dan link privasi/syarat) serta verifikasi pergantian bahasa saat locale diset ke `en_US`.

## 5. Verification & Static Analysis

- [x] 5.1 Jalankan `php tools/sgi-static-check.php` dan `php tools/sgi-admin-harness.php` untuk memastikan tidak ada pelanggaran standar atau regresi CSS/HTML.
- [x] 5.2 Lakukan verifikasi visual dan responsivitas pada viewport desktop dan mobile.

## Workflow follow-up

- Review kode dan antarmuka sesuai guardrail repo (`better-interface` & `design-taste-frontend`).
- Siapkan arsip change OpenSpec setelah verifikasi selesai.
