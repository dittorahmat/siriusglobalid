# Spec Delta: theme-i18n

## Purpose

Menyediakan lokalisasi antarmuka tema otomatis untuk label formulir, placeholder, pesan kesalahan, tombol, dan teks navigasi statis agar berganti bahasa antara Indonesia dan Inggris mengikuti pengaturan bahasa situs WordPress (`get_locale()`).

## ADDED Requirements

### Requirement: Automatic locale-aware interface labels
Sistem SHALL menampilkan label antarmuka tema dalam bahasa Inggris ketika pengaturan bahasa situs WordPress diset ke bahasa Inggris (`en_US` atau locale berawalan `en`), dan menggunakan bahasa Indonesia untuk `id_ID` atau default.

#### Scenario: Situs diatur ke bahasa Inggris
- **WHEN** pengaturan bahasa situs WordPress diset ke English (`en_US`)
- **THEN** label formulir di halaman kontak menampilkan "Full name", "Company", "Work email", "WhatsApp number", "Needs", "Budget range", "Target timeline", dan "Brief description"

#### Scenario: Situs diatur ke bahasa Indonesia
- **WHEN** pengaturan bahasa situs WordPress diset ke bahasa Indonesia (`id_ID`)
- **THEN** label formulir di halaman kontak menampilkan "Nama lengkap", "Perusahaan", "Email kerja", "Nomor WhatsApp", "Kebutuhan", "Rentang budget", "Target waktu", dan "Ceritakan singkat"

### Requirement: Localized validation and submit controls
Sistem SHALL menyesuaikan teks tombol kirim ("Kirim via WhatsApp" / "Send via WhatsApp") dan pesan validasi form dengan bahasa aktif.

#### Scenario: Validasi form pada locale Inggris
- **WHEN** bahasa aktif adalah Inggris dan pengguna mengirim form dengan field kosong yang wajib diisi
- **THEN** pesan validasi yang muncul adalah "Required field." atau "Please enter a valid work email."

#### Scenario: Validasi form pada locale Indonesia
- **WHEN** bahasa aktif adalah Indonesia dan pengguna mengirim form dengan field kosong yang wajib diisi
- **THEN** pesan validasi yang muncul adalah "Wajib diisi." atau "Masukkan email kerja yang valid."
