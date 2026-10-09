# Spec Delta

## Purpose

Memungkinkan seluruh teks situs siriusglobal.id disunting editor tunggal lewat JSON store dan dirender PHP tanpa mengubah visual, URL, dan perilaku bilingual yang ada.

## ADDED Requirements

### Requirement: Halaman publik dirender dari JSON tanpa ubah visual

Sistem SHALL merender 7 halaman top + 9 halaman layanan dari JSON melalui template PHP bersama, dengan markup, class CSS, dan pola `data-i18n` yang identik dengan HTML existing.

#### Scenario: Pengunjung buka beranda ID

- **WHEN** pengunjung membuka `/index.php` (atau `/index.html` via rewrite) dengan bahasa ID
- **THEN** hero, stats, layanan 5 kartu, why, process, cases, testimoni, CTA tampil dengan teks ID dari JSON dan layout tidak berubah

#### Scenario: File JSON konten hilang sebagian

- **WHEN** satu file section JSON tidak ada atau tidak valid saat halaman diminta
- **THEN** halaman tetap merespons 200 dengan fallback konten bawaan terakhir yang valid dan mencatat error ke log, tanpa white screen

### Requirement: Setiap field konten bilingual ID dan EN

Sistem SHALL menyimpan tiap string user-facing sebagai pasangan ID/EN dan memilih bahasa aktif (default ID, toggle ID/EN persis seperti `i18n.js` sekarang).

#### Scenario: Toggle bahasa

- **WHEN** pengunjung menekan toggle EN
- **THEN** seluruh string yang punya pasangan EN berganti ke EN tanpa reload struktur halaman, dan pilihan tersimpan untuk kunjungan berikut

#### Scenario: Field EN kosong

- **WHEN** field EN belum diisi admin
- **THEN** sistem menampilkan versi ID sebagai fallback, bukan string kosong

### Requirement: Tulis konten atomik dan tervalidasi

Sistem SHALL memvalidasi struktur JSON sebelum menyimpan (key wajib ada, kategori portofolio sesuai allowlist, slug layanan unik) dan menulis secara atomik (`LOCK_EX` + tulis ke file `.tmp` + `rename`).

#### Scenario: Save dengan kategori salah

- **WHEN** admin menyimpan card portofolio dengan `cat` di luar allowlist (`erp/ai/fleet/app/iot/infra/sec/bi`)
- **THEN** penyimpanan ditolak dengan pesan field yang salah dan file di disk tidak berubah

#### Scenario: Dua tab menyimpan bersamaan

- **WHEN** dua form dari revision yang sama disimpan hampir bersamaan
- **THEN** tulisan kedua yang berbasis revision basi ditolak (optimistic-lock `updated_at`) dan admin diminta muat ulang sebelum menyimpan ulang

### Requirement: Data produksi tidak tertimpa deploy

Sistem SHALL memisahkan data konten produksi dari repo git sehingga deploy (`cpanel.yml`, `gh-deploy.php`) tidak pernah menimpa `data/*.json` produksi dengan versi repo.

#### Scenario: Push ke main

- **WHEN** ada push ke `main` dan deploy berjalan
- **THEN** file PHP/template/CSS/JS terupdate di `public_html` sedangkan `data/*.json` produksi dan `assets/uploads/` tetap utuh

#### Scenario: Fresh install tanpa data

- **WHEN** deploy pertama kali dan folder data kosong
- **THEN** sistem menyalin `data/seed/*.json` sebagai data awal dan halaman langsung bisa dirender
