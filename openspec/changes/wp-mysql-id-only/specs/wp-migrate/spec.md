# Spec Delta

## Purpose

Memindahkan konten JSON seed ke WordPress MySQL dengan aman, menjaga URL/SEO, dan menetapkan alur admin serta deploy baru setelah root diganti WP.

## ADDED Requirements

### Requirement: Importer seed sisi ID sekali jalan
The system SHALL menyediakan importer yang membaca `data/seed/*.json` dan `data/seed/services/*.json` dari repo dan membuat/memperbarui post, meta, taxonomy, dan options WP hanya dari sisi `id`, mengabaikan sisi `en`.

#### Scenario: Import penuh berhasil
- **WHEN** importer dijalankan terhadap seed utuh
- **THEN** tercipta settings, home (hero_photo, kpi 3, stats 4, clients <= 12, case_imgs <= 6), seluruh layanan dari `services/*.json` (minimal 9 seed awal), portfolio items, tentang, layanan cards, kontak, privasi, syarat dengan jumlah sama seperti seed.

#### Scenario: Idempoten
- **WHEN** importer dijalankan dua kali tanpa perubahan seed
- **THEN** tidak ada duplikat post dan run kedua melaporkan nol perubahan.

#### Scenario: Seed rusak ditolak aman
- **WHEN** sebuah file JSON tidak valid atau field wajib hilang
- **THEN** importer membatalkan file itu, mencatat alasannya, dan data WP yang sudah ada tidak tertimpa.

### Requirement: URL lama tetap hidup dan SEO terjaga
The system SHALL mengalihkan URL lama `.html` dan `.php` (beranda, tentang, layanan, portofolio, kontak, privasi, syarat, dan semua detail layanan yang ada saat migrasi) ke permalink WP dengan status 301, serta menerbitkan `sitemap.xml` dan `robots.txt` yang valid dan dibuat dinamis dari konten (mendukung layanan yang bertambah/berkurang).

#### Scenario: Redirect lama
- **WHEN** pengunjung membuka `/layanan.html` atau `/layanan/app-development.html`
- **THEN** server menjawab 301 ke URL WP yang setara dan halaman tujuan 200.

#### Scenario: Sitemap valid
- **WHEN** crawler meminta `/sitemap.xml`
- **THEN** tersedia daftar URL WP kanonis dan tidak ada URL `.html`/`.php` lama yang masih didaftarkan.

### Requirement: Admin default dan keamanan dasar
The system SHALL memiliki akun administrator `mudita` yang dibuat di Dashboard (bukan di repo), password awal sekali pakai wajib diganti saat login pertama, tanpa kredensial di git, dengan proteksi login standar WP.

#### Scenario: Login pertama wajib ganti password
- **WHEN** `mudita` login pertama dengan password awal
- **THEN** WP memaksa penggantian password sebelum dapat mengelola konten.

#### Scenario: Tanpa secret di repo
- **WHEN** repo dipindai
- **THEN** tidak ditemukan password, salt, atau kredensial DB di file terlacak.

### Requirement: Deploy dan backup baru
The system SHALL menetapkan git sebagai source of truth hanya untuk theme `sirius-custom` dan importer; core, plugin, uploads, dan DB MySQL dikelola via Softaculous/cPanel backup, dan setiap rilis theme lolos review `better-interface`, taste check `design-taste-frontend`, cek mobile, dan serve check 200.

#### Scenario: Rilis theme aman
- **WHEN** theme baru di-deploy
- **THEN** konten DB dan uploads tidak tertimpa dan semua halaman utama menjawab 200.

#### Scenario: Backup dapat dipulihkan
- **WHEN** backup cPanel/Softaculous (file + DB) dipulihkan ke staging
- **THEN** situs tampil utuh dengan konten dan media yang sama.
