# Tasks

## 1. Pindahkan halaman ke Tampilan > Konten Situs

- [x] 1.1 Ganti registrasi halaman di `inc/options.php` dari `add_options_page` ke `add_theme_page` dengan label "Konten Situs", slug `sgi` tetap, dan kapabilitas `edit_theme_options`; samakan cek izin di dalam renderer. Verifikasi: `php -l` lolos dan pasca-deploy submenu muncul di Tampilan serta hilang dari Pengaturan.
- [x] 1.2 Tambah `inc/admin.php` berisi redirect `admin_init` untuk `options-general.php?page=sgi` ke `themes.php?page=sgi`, dengan guard `$pagenow` dan query agar tidak loop. Verifikasi: buka URL lama, mendarat di halaman baru tanpa redirect berulang.
- [x] 1.3 Daftarkan `inc/admin.php` di `functions.php` dan tambahkan tiga entry point: `theme_action_links` untuk stylesheet `sirius-custom`, notice di `themes.php`, dan node admin bar "Edit konten situs" dengan cek kapabilitas. Verifikasi: ketiganya terlihat dan menuju `themes.php?page=sgi`.
- [x] 1.4 Perbarui `README.md:24-34` agar menyebut lokasi baru, kapabilitas `edit_theme_options`, dan catatan bahwa `Pengaturan > Sirius` sudah tidak ada. Verifikasi: langkah di README cocok dengan kondisi wp-admin hasil deploy.

## 2. Tombol CTA header menjadi setting

- [x] 2.1 Tambah default `ctaLabel` = "Konsultasi Gratis" dan `ctaHref` = `/kontak/` di `sgi_settings()` (`functions.php:49`), masukkan kedua key ke daftar simpan di `inc/options.php:38`, dan normalkan label kosong kembali ke default saat dibaca. Verifikasi: smoke memastikan `sgi_settings()` tanpa option mengembalikan dua nilai default.
- [x] 2.2 Ubah `header.php:41` dan `header.php:49` agar membaca `ctaLabel` dan `ctaHref` lewat `sgi_home_href()`, tanpa mengubah markup dan gaya yang ada. Verifikasi: `php tools/sgi-smoke.php` mengecek label CTA mengikuti `sgi_settings` dan URL absolut tidak ditambahi domain.
- [x] 2.3 Tambah input label dan tautan CTA pada tab Header & navigasi, termasuk placeholder contoh `/kontak/` dan `https://...`. Verifikasi: simpan nilai baru lalu header desktop dan seluler menampilkan nilai yang sama.
- [x] 2.4 Catat field CTA baru di `README.md` bagian pengaturan situs. Verifikasi: README menyebut kedua key dan perilaku "kosong = bawaan".

## 3. Edit label navigasi dari halaman pengaturan

- [x] 3.1 Tambah helper di `inc/admin.php` untuk mengambil item menu lokasi `primary` dan menyusun argumen update lengkap (judul, url, status, position, parent, target, classes, description) dari item existing. Verifikasi: unit smoke memastikan rename hanya mengubah judul, sementara url, target, classes, dan parent tetap.
- [x] 3.2 Render satu input judul per item menu pada tab Header & navigasi, simpan lewat `wp_update_nav_menu_item()` di handler POST dengan nonce dan sanitasi `sanitize_text_field`. Verifikasi: ubah "Beranda" menjadi "Home", simpan, lalu header dan menu seluler menampilkan "Home" dengan tautan yang sama.
- [x] 3.3 Tangani menu kosong: tampilkan tiga label bawaan sebagai teks read-only, keterangan "memakai menu bawaan", dan tombol "Buat menu bawaan" yang memanggil `sgi_seed_primary_menu()` via POST bertanda nonce (bukan saat GET). Verifikasi: pada staging dengan lokasi `primary` kosong, tombol membuat menu berisi tiga item dan input rename muncul setelah reload.
- [x] 3.4 Tambah tautan "Atur struktur menu" ke `nav-menus.php` di samping daftar label, dengan penegasan bahwa tambah/urutkan/hapus tetap di Tampilan > Menu. Verifikasi: tautan membuka editor menu dan tidak ada kontrol struktur di halaman ini.
- [x] 3.5 Perbarui `README.md` bagian "Struktur konten di wp-admin" agar menyebut rename label dari halaman pengaturan. Verifikasi: instruksi README diikuti tanpa membuka file kode.

## 4. Tab dan alur simpan

- [x] 4.1 Kelompokkan field ke empat tab (Header & navigasi, Hero & angka, Section beranda, Kontak & situs) dengan semua panel dirender di DOM dan tanpa `hidden` dari server; validasi nilai `?tab=` terhadap daftar tab dan jatuh ke tab pertama bila asing. Verifikasi: setiap field lama tetap ada di DOM (hitung field sebelum/sesudah) dan tab asing tidak menimbulkan error.
- [x] 4.2 Tambah hidden input `sgi_tab` dan redirect setelah simpan ke `themes.php?page=sgi&tab=<tab>&updated=1`, lalu tampilkan notice "Tersimpan" berdasarkan query tersebut. Verifikasi: simpan dari tab ke-3 mengembalikan ke tab ke-3, dan refresh tidak memunculkan dialog resubmit.
- [x] 4.3 Pastikan handler simpan tetap menulis seluruh key `sgi_home`, `sgi_kontak`, dan `sgi_settings`, dan tambahkan cek bahwa field tab lain tidak kosong setelah simpan dari satu tab. Verifikasi: ubah satu field di tab Header, simpan, lalu nilai hero/why/cases/kontak tetap sama seperti sebelumnya.
- [x] 4.4 Tambah tombol "Lihat beranda" (`home_url('/')`, `target="_blank"`, `rel="noopener"`) di kepala halaman. Verifikasi: tombol membuka beranda di tab baru dan halaman pengaturan tetap terbuka.
- [x] 4.5 Pertahankan validasi email lama dan pastikan pesan "Email tidak valid. Data tidak disimpan." tampil tanpa mengubah option. Verifikasi: simpan email `bukan-email`, option `sgi_settings` tidak berubah.

## 5. Aset admin, aksesibilitas, dan responsif

- [x] 5.1 Buat `assets/css/admin.css` dan `assets/js/admin.js`, enqueue hanya pada hook halaman `sgi` (`admin_enqueue_scripts` dengan `get_plugin_page_hookname('sgi','themes.php')`), semua class berprefiks `.sgi-`. Verifikasi: halaman lain di wp-admin tidak memuat kedua file ini.
- [x] 5.2 Implementasikan tab progressive enhancement: JS menambahkan `.sgi-tabs--js` pada wrapper, CSS hanya menyembunyikan panel non-aktif di bawah class itu; tanpa JS semua panel tampil. Verifikasi: matikan JavaScript, semua panel terlihat dan form tetap bisa disimpan.
- [x] 5.3 Tambah save bar sticky dengan padding bawah agar tidak menutupi field terakhir, dan pastikan tab memakai `.nav-tab-wrapper` bawaan. Verifikasi: field terakhir tab Kontak tetap terlihat penuh saat di-scroll.
- [x] 5.4 Lengkapi aksesibilitas tab: `role="tab"`/`role="tabpanel"`, `aria-selected`, `aria-controls`, fokus terlihat, navigasi panah kiri/kanan, dan target klik minimal 24x24px. Verifikasi: seluruh tab dapat dioperasikan dengan keyboard saja.
- [x] 5.5 Verifikasi responsif: 360px, 768px, 1280px, dan zoom 200% tanpa scroll horizontal pada tab, input, textarea, maupun tombol simpan. Verifikasi: catat hasil tiap lebar di laporan review.

## 6. Integrasi dan review wajib

- [x] 6.1 Jalankan `php -l` untuk setiap file yang berubah dan `php tools/sgi-smoke.php`; pastikan job `php-lint` + `php-smoke` di CI hijau. Verifikasi: keluaran kedua perintah tanpa error.
- [x] 6.2 Jalankan review `better-interface` (scope `inc/options.php`, `inc/admin.php`, `assets/css/admin.css`, copy `header.php`), kumpulkan bukti `file:line`, ukur kontras dari nilai token aktual, dan perbaiki semua temuan HIGH sebelum lanjut. Verifikasi: tabel review tanpa status Block.
- [x] 6.3 Jalankan FINAL PRE-FLIGHT `design-taste-frontend` dan catat bahwa tidak ada perubahan komposisi front-end (label CTA hanya menjadi dinamis). Verifikasi: catatan non-delta ditulis di laporan review.

## 7. Remediasi review dua sumbu (2026-10-21)

- [x] 7.1 CTA menampilkan nilai mentah (`sgi_settings_raw`) + placeholder, agar field kosong terlihat kosong dan tidak bertentangan dengan konvensi README. Verifikasi: smoke `admin CTA menampilkan nilai mentah (kosong)`.
- [x] 7.2 Perbarui `GLOSSARY.md` (Konten Situs, Label navigasi, CTA header, Info kontak) dan komentar "Settings > Sirius" di `front-page.php`, `page-kontak.php`, `inc/helpers.php`, `tools/sgi-importer.php`. Verifikasi: `grep -r "Settings > Sirius"` hanya menyisakan catatan historis.
- [x] 7.3 Pecah `inc/options.php` menjadi `options.php` + `options-fields.php` + `options-save.php`; pakai `sgi_admin_page_url()`, `sgi_settings_fields()`, `sgi_settings_raw()`, `sgi_header_cta()`, dan args nav tanpa `raw`/`menu_name`. Verifikasi: smoke + render harness tanpa duplicate id.
- [x] 7.4 Tab menjadi `<button>` + `<noscript>` (bar tab disembunyikan, semua panel tampil); JS menambahkan role/aria. Verifikasi: screenshot JS-off tanpa bar tab, metrik 320-1280 tanpa overflow, `roleTabs=4` saat JS aktif.
- [x] 7.5 Importer hanya mengisi kunci `sgi_settings` yang belum ada lewat `sgi_fill_missing_keys()`, sehingga `ctaLabel`/`ctaHref` tidak terhapus saat import ulang. Verifikasi: smoke `importer hanya mengisi key settings yang belum ada` + cek pasca-deploy.
- [x] 7.6 Seed mengembalikan bool dan rename nav melaporkan `saved`/`failed`; handler menampilkan error yang sesuai, bukan "Tersimpan" palsu. Verifikasi: smoke handler + pesan `sgi_err=nav|seed`.
- [x] 7.7 Tambah cakupan smoke untuk render halaman admin (semua field lintas tab, placeholder, tanpa duplicate id) dan handler simpan (email invalid, tulis settings/home/kontak, pertahankan key lain). Verifikasi: `php tools/sgi-smoke.php` semua PASS.
- [x] 7.8 Tulis `openspec/changes/admin-content-ux/review.md` dan mandat review dua sumbu di `AGENTS.md`. Verifikasi: file ada, `openspec validate --strict` lolos.

## Workflow follow-up

- Pasca-deploy: jalankan Tools > Sirius Import ulang, lalu pastikan editan admin (teks hero, judul menu, CTA) tidak tertimpa.
- Pasca-deploy: curl satu per URL untuk `/`, `/layanan/`, `/layanan/app-development/`, `/kontak/`, `/tentang/`, `/portofolio/` dan pastikan semuanya 200.
- Pasca-deploy: uji rollback dengan revert commit theme; halaman kembali ke Pengaturan > Sirius dan data hasil editan tetap tersimpan.
- Arsipkan change setelah `review.md` ada, temuan HIGH/HARD bersih, dan cek mobile dinyatakan lolos.
- Implementasi sudah dijalankan; sisa pekerjaan hanya checklist pasca-deploy di atas.
