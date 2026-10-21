# Design

## Context

Lihat `proposal.md` untuk motivasi. Kondisi saat ini yang membentuk desain:

- Halaman pengaturan didaftarkan dengan `add_options_page` (`inc/options.php:10`), kapabilitas `manage_options`, slug `sgi`, dan masih memakai pola POST manual + `check_admin_referer` (`inc/options.php:36`), bukan Settings API.
- Satu submit menulis **seluruh** field tanpa syarat: `sgi_home` (`inc/options.php:85-115`), `sgi_kontak` (`:116-124`), `sgi_settings` (`:38-44`). Tidak ada logika merge per field yang tidak dikirim.
- Navigasi header dibaca dari Menu WP lokasi `primary` (`inc/helpers.php:83-105`), dengan fallback tiga item (`inc/helpers.php:70-77`). Label CTA header di-hardcode dua kali: `header.php:41` (desktop) dan `header.php:49` (seluler).
- Konvensi proyek: kosong = bawaan (`README.md:39-43`), importer tidak menimpa nilai yang sudah ada, dan tidak ada page builder (`wp-mysql-id-only` design.md:18).
- Tidak ada runtime WordPress di mesin pengembangan; verifikasi memakai `php -l` + `tools/sgi-smoke.php` (stub WP) dan curl pasca-deploy (`README.md:65-76`).

## Goals / Non-Goals

**Goals:**

- Satu halaman, empat tab, semua field dapat dijangkau tanpa scroll ekstrem dan tanpa kehilangan data.
- Label nav dan CTA header dapat diubah dari satu tempat, dengan menu WP tetap menjadi satu-satunya sumber kebenaran navigasi.
- Nol plugin baru, nol perubahan format storage, dan halaman tetap berfungsi tanpa JavaScript.

**Non-Goals:**

- Tidak membuat editor struktur menu (tambah/hapus/urutkan item) di halaman ini; itu tetap Tampilan > Menu.
- Tidak memindahkan field ke Customizer atau membuat live preview.
- Tidak mengganti textarea `kolom1 | kolom2` menjadi repeater (kandidat change terpisah).
- Tidak menyentuh tampilan publik selain CTA header yang menjadi dinamis.

## Decisions

- **`add_theme_page` dengan slug `sgi` yang dipertahankan.** Alasan: submenu Tampilan adalah lokasi yang diminta user dan semantiknya "konten theme", sementara slug tetap membuat option, bookmark, dan tautan lama tidak perlu dipetakan ulang. Alternatif `add_options_page` + link dari Tema ditolak karena tetap menyembunyikan halaman dari tempat yang dicari user.
- **Kapabilitas `edit_theme_options`.** Alasan: sejajar dengan Customizer dan hak mengubah theme; Administrator sudah memilikinya. Alternatif mempertahankan `manage_options` ditolak karena menghalangi peran kustom yang diberi hak theme tanpa hak setelan global. Konsekuensi dicatat di Risks.
- **Redirect 302 dari URL lama di `admin_init`.** Alasan: bookmark `options-general.php?page=sgi` masih mungkin ada. Deteksi dibatasi `$pagenow === 'options-general.php'` + `$_GET['page'] === 'sgi'` agar tidak menjadi loop; target memakai `admin_url('themes.php?page=sgi')` dan `wp_safe_redirect`.
- **Semua panel tab dirender di DOM, satu form, satu submit.** Alasan: handler simpan menulis semua key tanpa merge. Kalau tab dirender server-side per permintaan, tab lain tidak terkirim dan nilainya terhapus. Alternatif "simpan per tab + merge dari option" ditolak karena menambah logika merge di 60+ field dan berisiko menghapus key secara diam-diam.
- **Progressive enhancement untuk tab: tombol, bukan link, dan JS yang mengaktifkan.** Server merender semua panel terbuka dan tab sebagai `<button type="button">`; JS menambahkan `.sgi-tabs--js`, role/aria tab, dan menyembunyikan panel non-aktif. `<noscript>` menyembunyikan bar tab dan menampilkan keterangan. Alasan: tab berupa link tanpa JS akan reload dan membuang isian yang belum disimpan (temuan review); tombol tanpa JS memang inert, tetapi semua panel tetap tampil sehingga tidak ada isian yang hilang.
- **Tab aktif disimpan di query `?tab=` dan di hidden input.** Alasan: deep link dari admin bar/notice dan redirect setelah simpan bisa mengembalikan user ke tab yang sama. Nilai tab divalidasi terhadap daftar tab; nilai asing jatuh ke tab pertama.
- **Rename nav memakai `wp_update_nav_menu_item` dengan argumen lengkap.** Alasan: fungsi core mengharapkan field item; mengirim judul saja berisiko mengosongkan url/classes/target. Implementasi membaca item existing, menyusun seluruh argumen sekali di `sgi_nav_menu_for_edit()`, lalu mengganti judul saja. Hasil update dihitung (`saved`/`failed`) agar handler bisa menampilkan kegagalan alih-alih selalu "Tersimpan". Alternatif `wp_update_post` langsung ke post `nav_menu_item` ditolak karena melewati cache menu dan meta item.
- **Importer hanya mengisi kunci settings yang belum ada.** Alasan: sebelumnya importer mengganti seluruh option `sgi_settings`, sehingga re-import menghapus `ctaLabel`/`ctaHref` dan mengembalikan CTA ke default. Sekarang `array_key_exists` menjaga editan admin, konsisten dengan konvensi README "import ulang hanya mengisi kunci yang belum ada".
- **State menu kosong ditangani eksplisit, bukan input mati.** Alasan: `sgi_primary_nav()` diam-diam memakai fallback, sehingga admin bisa mengira input tidak berfungsi. Tab Header menampilkan label bawaan sebagai teks read-only + tombol "Buat menu bawaan" (POST bertanda nonce, memanggil `sgi_seed_primary_menu()`), karena seed saat aktivasi theme tidak selalu jalan pada site yang sudah live. Helper seed juga mengisi menu yang sudah terpasang tetapi kosong, dan mengembalikan bool agar handler bisa membedakan sukses dari gagal.
- **CTA header masuk `sgi_settings()` dengan default lama.** Alasan: mengikuti pola option yang sudah ada dan otomatis ikut importer. Normalisasi: label kosong disimpan sebagai string kosong tetapi **dibaca** sebagai default; href dinormalisasi saat render lewat `sgi_home_href()` sehingga URL absolut dipakai apa adanya dan path relatif memakai `home_url()`. Form menampilkan nilai **mentah** (`sgi_settings_raw()`) agar field yang dikosongkan terlihat kosong, sementara render memakai `sgi_header_cta()` sebagai satu akses untuk pasangan label+href. Alternatif option baru (`sgi_header`) ditolak karena menambah key tanpa manfaat.
- **Tetap POST manual + nonce, bukan Settings API.** Alasan: memindahkan 60+ field ke `register_setting` berarti menulis ulang sanitasi dan semantics "kosong = bawaan" yang sudah teruji; tidak sepadan untuk perbaikan UX ini. Yang ditambahkan hanya redirect setelah simpan (`updated=1`) untuk mencegah resubmit. Handler, field/panel, dan skeleton halaman dipisah ke `options-save.php`, `options-fields.php`, `options.php` agar tiap file punya satu alasan untuk berubah; daftar field settings memakai satu map `sgi_settings_fields()` untuk simpan dan render.
- **Aset khusus halaman.** `assets/css/admin.css` + `assets/js/admin.js` di-enqueue hanya pada hook suffix halaman ini (`admin_enqueue_scripts` + `$hook === get_plugin_page_hookname('sgi','themes.php')`), dengan class berprefiks `.sgi-` agar tidak bentrok dengan CSS wp-admin.
- **Entry point tambahan.** `theme_action_links` difilter hanya untuk stylesheet `sirius-custom`; notice dirender hanya di `themes.php`; node admin bar didaftarkan lewat `admin_bar_menu` dengan cek `current_user_can('edit_theme_options')` dan `is_admin_bar_showing()`. Semua memakai `admin_url('themes.php?page=sgi')`.

## Risks / Trade-offs

- [Update nav item mengosongkan atribut] → Argumen lengkap disusun dari item existing, ditambah cek smoke yang memastikan `href` dan `target` tidak berubah setelah rename.
- [Tab tanpa JS menyembunyikan field] → Default server = semua panel terbuka dan tab berupa tombol inert; bar tab disembunyikan `<noscript>`, diuji dengan JavaScript dimatikan.
- [Importer menghapus CTA saat import ulang] → Importer hanya mengisi kunci settings yang belum ada; helper merge diuji smoke, ditambah cek import ulang pasca-deploy.
- [Payload POST lebih besar karena semua panel terkirim] → Dampak dapat diabaikan (teks, bukan file); batas `max_input_vars` jauh di atas jumlah field saat ini.
- [Perubahan kapabilitas menyembunyikan halaman dari peran kustom] → Hanya Administrator yang terdampak di instalasi ini (tidak ada peran kustom); dicatat di README agar tidak mengagetkan.
- [Redirect lama memicu loop] → Guard `$pagenow` + query spesifik; redirect hanya sekali dan tidak berjalan di halaman tujuan.
- [CSS wp-admin menimpa layout tab] → Semua selector berprefiks `.sgi-`; tab memakai `.nav-tab-wrapper` bawaan agar gaya inti tetap konsisten.
- [Rollback theme tidak mengembalikan data] → Perubahan judul menu dan CTA adalah data, bukan kode; rollback hanya mengembalikan tampilan/entry point. Dicatat di Migration Plan.

## Migration Plan

1. Deploy theme lewat alur cPanel Git yang sudah ada (`.cpanel.yml` tidak berubah, tidak ada file plugin baru).
2. Tidak ada migrasi DB: key `sgi_settings`, `sgi_home`, `sgi_kontak` dan strukturnya tetap. `ctaLabel`/`ctaHref` memakai default saat belum ada.
3. Verifikasi pasca-deploy: halaman `themes.php?page=sgi` 200, redirect `options-general.php?page=sgi` mendarat di halaman baru, header menampilkan "Konsultasi Gratis", curl 200 untuk `/`, `/layanan/`, `/kontak/`, `/tentang/`, `/portofolio/`.
4. Rollback: revert commit theme; halaman kembali ke Pengaturan > Sirius. Data yang sudah diubah admin (judul menu, CTA) tetap tersimpan dan harus dikembalikan manual bila diinginkan.
