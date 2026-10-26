# Spec Delta: theme-customizer

## Purpose

Menyediakan pengaturan visual dinamis tema Sirius Custom melalui WordPress Customizer, meliputi pilihan warna latar footer dan foto hero utama dengan fallback yang aman.

## ADDED Requirements

### Requirement: Footer background color setting
Sistem SHALL menyediakan pengaturan Customizer `sgi_footer_bg` untuk memilih warna latar footer dan menginjeksikannya sebagai CSS inline saat bernilai valid hex/rgba.

#### Scenario: Admin mengubah warna background footer
- **WHEN** admin memilih warna hex baru pada kontrol `sgi_footer_bg` di Customizer dan mempublikasikannya
- **THEN** elemen `.site-footer` di frontend menampilkan warna latar yang dipilih tersebut

#### Scenario: Fallback warna background footer
- **WHEN** nilai `sgi_footer_bg` kosong atau belum pernah diset
- **THEN** elemen `.site-footer` tetap memakai warna bawaan `--navy-900` (#0b2447)

### Requirement: Hero photo media upload setting
Sistem SHALL menyediakan pengaturan Media Control di Customizer `sgi_hero_photo` untuk memilih attachment gambar hero dari Media Library.

#### Scenario: Admin memilih foto hero custom
- **WHEN** admin mengunggah atau memilih attachment gambar pada kontrol `sgi_hero_photo` di Customizer
- **THEN** elemen `.hero-visual .photo` di beranda menampilkan URL gambar attachment tersebut dengan tag width dan height yang proporsional

#### Scenario: Fallback foto hero jika belum diatur
- **WHEN** kontrol `sgi_hero_photo` belum diatur oleh admin
- **THEN** beranda menggunakan fallback gambar default dari konfigurasi tema
