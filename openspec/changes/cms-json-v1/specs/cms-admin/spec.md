# Spec Delta

## Purpose

Memberi editor tunggal panel admin yang aman dan sederhana untuk menyunting semua konten situs, mengunggah foto, dan mencadangkan data tanpa menyentuh kode.

## ADDED Requirements

### Requirement: Login admin tunggal yang aman

Sistem SHALL melindungi `/admin/` dengan login 1 user (hash `password_hash`, session regenerasi saat login, rate-limit percobaan gagal, logout eksplisit).

#### Scenario: Login berhasil

- **WHEN** kredensial benar dimasukkan
- **THEN** session dibuat ulang id-nya, admin masuk dashboard, dan halaman login tidak bisa diakses ulang tanpa logout

#### Scenario: Brute force

- **WHEN** 5 percobaan login gagal dalam 10 menit dari IP yang sama
- **THEN** login dikunci sementara dan menampilkan pesan generik tanpa membocorkan user mana yang salah

### Requirement: Form edit per section situs

Sistem SHALL menyediakan form untuk settings, home (hero/stats/why/process/cases/testimoni/CTA), 9 layanan, portofolio, dan i18n, dengan tiap field teks memiliki input ID + EN berdampingan dan validasi wajib.

#### Scenario: Edit hero beranda

- **WHEN** admin mengubah judul/lead/CTA hero ID+EN lalu menyimpan
- **THEN** perubahan langsung tampil di halaman publik, `updated_at` bertambah, dan backup otomatis dibuat

#### Scenario: Validasi gagal

- **WHEN** field wajib dikosongkan atau email/telepon tidak valid
- **THEN** form menolak dengan pesan per-field, fokus ke field pertama yang salah, dan tidak ada file yang tertulis

### Requirement: Upload foto tervalidasi

Sistem SHALL menerima upload `jpeg/png/webp` maksimal 2 MB, memverifikasi MIME asli, me-rename acak, menyimpan di `assets/uploads/`, dan menonaktifkan eksekusi PHP di folder tersebut.

#### Scenario: Upload valid

- **WHEN** admin mengunggah foto `webp` 800KB untuk card portofolio
- **THEN** file tersimpan dengan nama acak, preview tampil, dan path tercatat di JSON konten terkait

#### Scenario: Upload berbahaya

- **WHEN** file berekstensi ganda (mis. `foto.php.jpg`) atau MIME tidak sesuai diunggah
- **THEN** upload ditolak, file tidak tersimpan, dan admin melihat alasan penolakan

### Requirement: Backup dan restore data

Sistem SHALL menyediakan tombol download backup (ZIP berisi `data/*.json` + manifest) dan restore yang memvalidasi isi sebelum menimpa, plus backup otomatis tiap save yang berhasil.

#### Scenario: Restore file rusak

- **WHEN** admin mengunggah file restore yang bukan ZIP valid atau berisi JSON tidak valid
- **THEN** restore dibatalkan, data aktif tidak berubah, dan pesan error menjelaskan berkas mana yang bermasalah
