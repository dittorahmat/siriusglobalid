# Spec Delta: service-management

## Purpose

Mengatur fleksibilitas tampilan visual kartu layanan dengan dukungan ikon gambar unggulan (Featured Image) dan toleransi validasi saat penyimpanan data meta layanan di admin WordPress.

## ADDED Requirements

### Requirement: Service card custom image icon support
Sistem SHALL mendukung penayangan gambar thumbnail unggulan (`Featured Image`) pada kartu layanan di beranda sebagai pengganti ikon teks simbol bawaan.

#### Scenario: Post layanan memiliki Featured Image
- **WHEN** post layanan memiliki Featured Image yang diatur oleh admin
- **THEN** kartu layanan terkait di beranda me-render tag `<img>` thumbnail di dalam `.card-icon`

#### Scenario: Post layanan tidak memiliki Featured Image
- **WHEN** post layanan tidak memiliki Featured Image
- **THEN** kartu layanan tetap me-render ikon simbol teks unicode bawaan sebagai fallback

### Requirement: Tolerant service tiers validation
Sistem SHALL menerima input field `_sgi_tiers` yang kosong atau whitespace tanpa menghentikan eksekusi dengan pesan fatal "Tiers bukan JSON valid".

#### Scenario: Admin menyimpan post layanan dengan tiers kosong
- **WHEN** admin menyimpan atau memperbarui post layanan dengan textarea `_sgi_tiers` dibiarkan kosong
- **THEN** sistem menyimpan meta `_sgi_tiers` sebagai array kosong `[]` tanpa memicu `wp_die` fatal error

#### Scenario: Admin menyimpan post layanan dengan JSON tiers valid
- **WHEN** admin menyimpan data format JSON array yang valid pada `_sgi_tiers`
- **THEN** sistem menyimpan data tiers tersebut ke database secara normal
