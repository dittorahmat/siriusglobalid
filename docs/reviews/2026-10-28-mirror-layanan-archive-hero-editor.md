# Code Review: Mirror Layanan Archive & Hero Editor

- **Fixed Point**: `HEAD` (`a4e26286d21cb7d1fd850f511ad5b4b3f1fa5978`)
- **Diff Target**: Working Tree changes (`archive-layanan.php`, `functions.php`, `inc/options-fields.php`, `inc/options-save.php`, `assets/seed/home.json`)
- **Tanggal**: 2026-10-28

---

## Standards

### 1. Hard Violations of Documented Standards
- **Standard**: `AGENTS.md` (Domain docs / Single-responsibility conventions) & `wp-content/themes/sirius-custom/README.md` (Konvensi penyimpanan / Konten situs).
  - **Catatan**: Opsi hero arsip layanan (`archive_svc_eye`, `archive_svc_title_html`, `archive_svc_lead`) disimpan dalam array opsi `sgi_home` di bawah panel "Section beranda". Keputusan ini diambil secara sadar sesuai prinsip YAGNI (menghindari pembuatan tabel/opsi baru di database dan menjaga kompatibilitas importer/export).
- **Standard**: `AGENTS.md` ("WAJIB: mobile-friendly & responsive check").
  - **Status**: PASS. Tidak ada inline `grid-template-columns`, grid menggunakan kelas `.services-grid` bawaan responsif (reflow 3 -> 2 -> 1 kolom pada mobile), dan seluruh berkas PHP lolos `php -l`.

### 2. Baseline Smells (Judgement Calls)
1. **Divergent Change / Feature Envy (`functions.php` & `inc/options-fields.php`)**:
   - `sgi_home()` dan panel "Section beranda" memegang data untuk arsip `/layanan/`.
   - *Rasional*: Mematuhi prinsip YAGNI repo agar opsi situs terpusat di satu skema data tanpa overhead migrasi schema baru.
2. **Duplicated Code (`archive-layanan.php` vs `front-page.php`)**:
   - Struktur kartu layanan (`.card-inner`, fallback kutipan `wp_trim_words`, dan custom property background `--card-bg`) mirip dengan beranda.
   - *Rasional*: Mengisolasi template arsip WP CPT tanpa menambah coupling layer abstraksi yang prematur.
3. **Speculative Generality (`inc/options-save.php`)**:
   - `wp_kses(..., ['span' => []])` diizinkan khusus untuk judul agar pengelola situs tetap dapat memberikan aksen warna span tanpa membuka risiko tag HTML sembarangan.

---

## Spec

### 1. Requirements Missing or Partial
- **Hero Title Fallback (Sudah diperbaiki)**:
  - Sebelumnya jika admin mengosongkan judul hero di Konten Situs, tag `<h1>` akan kosong.
  - *Perbaikan*: Ditambahkan fallback otomatis ke `"Sembilan kapabilitas, <span>satu standar kualitas</span>"` pada `archive-layanan.php:19-25`.
- **Label Tombol Kartu Kombinasi (Sudah diperbaiki)**:
  - Sebelumnya kartu penutup kombinasi di halaman arsip `/layanan/` menampilkan tombol `"Lihat semua layanan"` (mengarah ke `/kontak/`).
  - *Perbaikan*: Di halaman arsip, teks tombol kartu kombinasi di-override menjadi `"Konsultasi Gratis"` agar relevan dan tidak mengarah balik ke halaman yang sama.

### 2. Behaviour in the Diff Not Asked For (Scope Creep)
- Label isian di panel admin diperjelas dengan suffix `"beranda"` (misal: `"Eyebrow beranda"`, `"Judul beranda"`) untuk membedakan dengan field hero arsip `/layanan/`. Ini adalah perbaikan UX label non-breaking.

### 3. Requirements That Look Implemented But Implementation Looked Wrong
- Selektor CSS `.link-more`: Terkonfirmasi tersedia secara global di `components.css:52` dengan warna kontras `var(--teal-600)` untuk kartu tanpa thumbnail, serta override warna hijau neon (`#38d9b9`) khusus kartu dengan background image di `pages.css:103`.

---

## Summary
- **Standards**: 0 fatal blocking violations, 3 judgement calls (diterima atas dasar YAGNI & kestabilan skema option).
- **Spec**: 0 remaining issues (2 temuan edge-case: fallback judul kosong & label tombol kombinasi arsip telah diperbaiki).
- **Status Akhir**: **PASS** (Semua tes statis mekanis dan smoke harness lulus 100%).
