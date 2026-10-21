# Proposal

## Why

Admin situs tidak menemukan tempat mengubah label yang tampil di header. Tiga label nav (Beranda, Layanan, Kontak) tersimpan di Tampilan > Menu, satu label tombol (Konsultasi Gratis) di-hardcode di template, dan halaman pengaturan konten berada di Pengaturan > Sirius sebagai satu form panjang tanpa preview. Akibatnya bagian yang paling sering diubah terasa tidak bisa diedit, dan user meminta page builder seperti Elementor padahal masalahnya adalah penempatan dan pengelompokan field.

## What Changes

- Pindahkan halaman pengaturan dari Pengaturan > Sirius ke **Tampilan > Konten Situs** (`add_theme_page`, kapabilitas `edit_theme_options`). Slug `sgi` dipertahankan, dan URL lama `options-general.php?page=sgi` dialihkan ke lokasi baru.
- Tambah pintu masuk yang terlihat: link aksi pada kartu theme aktif, kartu info di Tampilan > Tema, dan node admin bar "Edit konten situs".
- Tab **Header & navigasi**: rename label item menu yang sudah ada langsung dari halaman ini (hanya judul; URL, struktur, dan urutan tetap di Tampilan > Menu), plus state eksplisit "memakai menu bawaan" dan aksi membuat menu bawaan bila lokasi `primary` belum diisi.
- Tombol header "Konsultasi Gratis" menjadi setting `ctaLabel` dan `ctaHref` di `sgi_settings()` dengan nilai bawaan sama seperti sekarang, dipakai versi desktop dan seluler. Tidak ada lagi label CTA yang tidak bisa diedit.
- Pecah form menjadi empat tab: Header & navigasi, Hero & angka, Section beranda, Kontak & situs. Semua panel tetap dirender di DOM agar satu submit tetap menyimpan seluruh field; tanpa JavaScript semua panel tampil dan tetap bisa diedit.
- Simpan memakai redirect + notice `updated=1` (mencegah resubmit), plus tombol "Lihat beranda" dan indikator tab aktif yang bisa di-deep-link.
- Tidak ada perubahan storage: option `sgi_home`, `sgi_kontak`, dan `sgi_settings` beserta format input `kolom1 | kolom2` tetap sama, sehingga importer dan data existing tidak perlu migrasi.

## Capabilities

### New Capabilities

- `wp-content-admin`: perilaku wp-admin untuk mengedit konten situs (navigasi header, tombol CTA header, section beranda, kontak) tanpa menyentuh kode, termasuk lokasi menu, entry point, izin, dan integritas penyimpanan.

### Modified Capabilities

- Tidak ada. `openspec/specs/` belum berisi spec durable (seluruh change sebelumnya masih di `changes/`), jadi capability di atas dibuat baru dan tidak mengubah kontrak yang sudah ada.

## Impact

- `wp-content/themes/sirius-custom/inc/options.php`: registrasi halaman, tab, field CTA, edit label nav, alur simpan.
- `wp-content/themes/sirius-custom/inc/admin.php` (baru): link aksi theme, notice di Tampilan > Tema, node admin bar, redirect URL lama.
- `wp-content/themes/sirius-custom/functions.php`: default `ctaLabel`/`ctaHref` pada `sgi_settings()` dan require file admin baru.
- `wp-content/themes/sirius-custom/header.php`: CTA header desktop dan seluler membaca setting.
- `wp-content/themes/sirius-custom/assets/css/admin.css` dan `assets/js/admin.js` (baru, hanya dimuat di halaman ini): tab, save bar, rencana tanpa dependensi eksternal.
- `tools/sgi-smoke.php`: cek CTA header mengikuti setting dan rename nav tidak mengubah URL.
- `wp-content/themes/sirius-custom/README.md`: lokasi pengaturan baru.
- Tidak berdampak pada DB/schema, permalink, importer, `.cpanel.yml`, atau tampilan publik selain label CTA yang kini dinamis. Tanpa plugin baru dan tanpa page builder.
