# Technical Design: Theme Customizer, Hero, Service Enhancements, and Bilingual Locale Support

## Context

Situs menggunakan WordPress dengan tema kustom `wp-content/themes/sirius-custom/`. Sebelumnya tema dikunci sebagai "ID-only" dengan teks statis antarmuka ditulis hardcode dalam bahasa Indonesia di file PHP. Saat Site Language diubah ke English (`en_US`), label antarmuka form kontak tetap muncul dalam bahasa Indonesia. Selain itu, footer color dan hero photo belum memiliki Customizer control, kartu hero belum memiliki link, kartu KPI hero tidak memiliki logika kondisional saat kosong, dan validasi JSON tiers melempar fatal error saat string kosong.

## Goals / Non-Goals

**Goals:**
- Memperluas WP Customizer untuk mendukung kontrol warna footer (`sgi_footer_bg`) dan unggah media foto hero (`sgi_hero_photo`).
- Menambahkan field tautan (`sgi_hero_card1_href`) di admin Konten Situs > Hero dan me-render kartu status sebagai `<a>` saat URL tersedia.
- Membuat render kartu KPI hero (`.dash-card`) kondisional agar tidak muncul kotak putih kosong saat data tidak diisi.
- Mengaktifkan dukungan thumbnail / featured image pada kartu layanan di beranda dengan fallback simbol unicode.
- Memperbaiki parser JSON `_sgi_tiers` di `inc/meta.php` agar toleran terhadap input kosong (`""`).
- Menyediakan mekanisme translasi antarmuka tema (`sgi_t()`) yang membaca `get_locale()`, sehingga label form kontak, placeholder, tombol, error, dan breadcrumb otomatis berganti ke English saat Site Language adalah English.

**Non-Goals:**
- Memasang plugin translasi berat seperti WPML atau Polylang (tema membaca pengaturan native Site Language bawaan WP).
- Mengubah database copy yang diisi secara manual di Konten Situs (judul hero/form diisi admin sesuai bahasa yang diinginkan).

## Decisions

### 1. Integrasi Kontrol Warna & Foto di WP Customizer
- **Keputusan:** Menempatkan setting `sgi_footer_bg` (Color Control) dan `sgi_hero_photo` (Media Control) di WordPress Customizer.
- **Rasional:** Core WordPress sudah memiliki UI Color Picker dan Media Library uploader bawaan yang teruji.

### 2. Output Warna Dinamis via CSS Variable / Inline Style
- **Keputusan:** Menginjeksi CSS inline di footer/head menggunakan `wp_add_inline_style` untuk `.site-footer { background-color: ... !important; }`.
- **Rasional:** Cepat, aman, dan tanpa modifikasi file aset statis.

### 3. Rendering Link Kartu Hero
- **Keputusan:** Jika `sgi_hero_card1_href` terisi dan valid, tag pembungkus `.hero-photo-card` berubah dari `<div>` menjadi `<a>` dengan styling hover.

### 4. Pengecekan KPI Hero
- **Keputusan:** Melewati render blok `<div class="dash-card">...</div>` jika tidak ada KPI bernilai non-nol atau berlabel.

### 5. Toleransi Tiers JSON di `inc/meta.php`
- **Keputusan:** Menganggap string kosong atau whitespace pada `_sgi_tiers` sebagai array kosong `[]`.

### 6. Bilingual Locale Helper (`sgi_t()`)
- **Keputusan:** Membuat dictionary ringan bilingual (`inc/i18n.php`) dengan fungsi `sgi_t(string $key, ?string $fallback = null): string`. Fungsi ini mendeteksi apakah `str_starts_with(get_locale(), 'en')`.
  - Jika `en`: kembalikan translasi Inggris (misal `"Full name"`, `"Work email"`, `"Send via WhatsApp"`).
  - Jika bukan: kembalikan translasi Indonesia default (misal `"Nama lengkap"`, `"Email kerja"`, `"Kirim via WhatsApp"`).
- **Rasional:** Sangat ringan, nol dependensi eksternal, langsung bekerja seketika admin mengubah Site Language di Settings WP.

## Risks / Trade-offs

- **[Risk]** Text input di admin Konten Situs (seperti nama kebutuhan dropdown) mungkin masih bernilai teks Indonesia jika belum disesuaikan admin.
  - *Mitigasi:* Berikan fallback bilingual bawaan di `page-kontak.php` jika opsi admin masih menggunakan nilai default.
