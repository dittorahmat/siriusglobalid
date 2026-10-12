# Sirius Global Indonesia

Rebuild WordPress ID-only siriusglobal.id: theme kustom + importer sebagai source of truth di git.

## Language

**Menu**:
Navigasi header yang dikelola editor via Tampilan > Menu.
_Avoid_: nav hardcode, daftar link statis

**Halaman**:
Konten statis satu URL via Page WP (Tentang, Kontak, Privasi, Syarat).
_Avoid_: page generik, post

**Layanan**:
Kapabilitas yang dijual; CPT `layanan` dengan arsip `/layanan/` dan detail `/layanan/{slug}/`.
_Avoid_: page layanan, post layanan, kategori layanan

**Portofolio**:
Studi kasus; CPT `portfolio` dengan taksonomi `portfolio_cat`.
_Avoid_: page portofolio, post portofolio

**Pos**:
Artikel blog WP (`post`). Saat ini tidak dipakai untuk konten situs.
_Avoid_: page, layanan

**Info kontak**:
Email, WA/telepon, alamat, jam, embed peta di Settings > Sirius.
_Avoid_: halaman kontak

**Halaman Kontak**:
Halaman `/kontak/` (Template Kontak): hero + formulir WA + Info kontak.
_Avoid_: info kontak
