# Proposal: Theme Customizer, Hero, Service Enhancements, and Bilingual Locale Support

## Why

Pengelola situs saat ini menghadapi keterbatasan dalam mengkustomisasi visual dan lokalisasi bahasa:
1. Warna latar footer dan foto hero utama masih di-hardcode (foto hero default mengarah ke gambar alam picsum yang tidak relevan).
2. Kartu status hero "Latest Technology Fact" belum bisa diberi tautan (*not clickable*), dan kartu KPI hero di bawahnya tetap meninggalkan kotak putih kosong saat teks di admin dikosongkan.
3. Kartu layanan pada beranda hanya mendukung ikon simbol teks unicode tanpa opsi menggunakan gambar/ikon upload sendiri via featured image.
4. Pengeditan post layanan di admin gagal disimpan dengan pesan fatal "Tiers bukan JSON valid" ketika field tiers dikosongkan.
5. Antarmuka tema dibuat ID-only dengan string label form, placeholder, breadcrumb, dan tombol yang di-hardcode dalam bahasa Indonesia. Ketika pengaturan bahasa situs diubah ke English (`en_US`), antarmuka kontak dan komponen tema tetap memunculkan teks bahasa Indonesia.

Perubahan ini memberikan kontrol visual mandiri melalui WordPress Customizer dan Admin, serta menyediakan dukungan lokalisasi dinamis sesuai pengaturan bahasa situs (`id_ID` vs `en_US`).

## What Changes

- **Customizer Color Controls**: Menambahkan kontrol Color Picker di WP Customizer untuk warna latar footer (`sgi_footer_bg`) dengan default `--navy-900` (#0b2447).
- **Hero Photo Media Control**: Menambahkan kontrol upload gambar di WP Customizer untuk Foto Hero beranda (`sgi_hero_photo`), menggantikan fallback hardcoded `picsum.photos`.
- **Clickable Hero Status Card**: Menambahkan opsi field link (`sgi_hero_card1_href`) di menu Konten Situs > Hero sehingga kartu status hero dapat diklik menuju halaman tujuan.
- **Dynamic / Conditional Hero KPI Card**: Membungkus render container KPI hero (`.dash-card`) dengan pengecekan kelayakan; jika nilai KPI dikosongkan, kotak putih tidak akan di-render.
- **Service Featured Image Support**: Memperbarui kartu layanan beranda agar memprioritaskan Featured Image / thumbnail upload jika tersedia, dengan fallback simbol teks bawaan.
- **Resilient Service Tiers Validation**: Memperbaiki penanganan `_sgi_tiers` di `inc/meta.php` agar string kosong diperlakukan sebagai array kosong `[]` yang valid tanpa fatal error.
- **Theme Bilingual / Locale Support**: Memperkenalkan helper i18n tema (`sgi_t()`) atau integrasi get_locale() agar label form (Nama lengkap, Email kerja, Perusahaan, Nomor WhatsApp, Kebutuhan, Rentang budget, Target waktu, Ceritakan singkat, tombol submit, pesan error validasi, dan breadcrumb) otomatis berganti antara bahasa Indonesia dan Inggris mengikuti pilihan bahasa situs di **Settings > General > Site Language**.

## Capabilities

### New Capabilities
- `theme-customizer`: Pengaturan visual dinamis (warna footer, foto hero) melalui WordPress Customizer dengan sanitasi dan output CSS inline yang aman.
- `hero-section`: Pengaturan interaktif hero beranda mencakup gambar hero dinamis, link kartu status, dan kondisional rendering kartu KPI.
- `service-management`: Manajemen konten layanan mencakup dukungan thumbnail gambar upload pada kartu layanan dan validasi meta field tiers yang toleran terhadap input kosong.
- `theme-i18n`: Lokalisasi antarmuka tema (label, placeholder, error, tombol) yang merespons pengaturan locale/bahasa situs WordPress (`id_ID` vs `en_US`).

### Modified Capabilities
<!-- Tidak ada spesifikasi eksisting sebelumnya di openspec/specs/ -->

## Impact

- **Files**:
  - `wp-content/themes/sirius-custom/inc/logo.php` / `inc/customizer.php` (kontrol Customizer)
  - `wp-content/themes/sirius-custom/front-page.php` (render hero photo, hero card link, kpi conditional, service image)
  - `wp-content/themes/sirius-custom/footer.php` (style warna background dinamis, i18n teks footer)
  - `wp-content/themes/sirius-custom/inc/meta.php` (perbaikan validasi tiers JSON)
  - `wp-content/themes/sirius-custom/inc/options-fields.php` & `inc/options-save.php` (field link kartu hero)
  - `wp-content/themes/sirius-custom/page-kontak.php` (lokalisasi form label, placeholder, error message)
  - `wp-content/themes/sirius-custom/inc/i18n.php` (dictionary & helper translasi antarmuka tema)
- **Dependencies & DB**: Menggunakan API standar WordPress (`WP_Customize_Manager`, `get_locale()`, `has_post_thumbnail()`). Tanpa dependensi eksternal baru.
