# Sirius Global Indonesia

Rebuild WordPress ID-only siriusglobal.id: theme kustom + importer sebagai source of truth di git.

## Language

**Menu**:
Navigasi header. Struktur (tambah/urutkan/hapus item, URL) diatur di Tampilan > Menu; teks label bisa diubah di Tampilan > Konten Situs.
_Avoid_: nav hardcode, daftar link statis

**Konten Situs**:
Halaman wp-admin Tampilan > Konten Situs (kapabilitas `edit_theme_options`) untuk mengubah label navigasi, tombol CTA header, section beranda, dan kontak. Menggantikan Settings > Sirius.
_Avoid_: Settings > Sirius, pengaturan tema

**Label navigasi**:
Teks item Menu yang tampil di header. Disimpan sebagai judul item Menu WP, bukan option terpisah.
_Avoid_: menu hardcode, label statis

**CTA header**:
Tombol ajakan di header desktop dan menu seluler. Label + tautan disimpan di option `sgi_settings` (`ctaLabel`, `ctaHref`).
_Avoid_: tombol kontak hardcode

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
Email, WA/telepon, alamat, jam, embed peta di Tampilan > Konten Situs.
_Avoid_: halaman kontak

**Halaman Kontak**:
Halaman `/kontak/` (Template Kontak): hero + formulir WA + Info kontak.
_Avoid_: info kontak
