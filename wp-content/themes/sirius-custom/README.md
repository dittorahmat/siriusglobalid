# Sirius Custom — pemetaan template lama ke WordPress

Theme ID-only, port 1:1 dari situs PHP/JSON. Copy section Bahasa Indonesia
di-bake dari `data/seed/i18n.id.json` (bukan i18n runtime).

| File lama | Template WP | Catatan |
|---|---|---|
| `templates/layout.php` + `header.php` | `header.php` + `footer.php` (`wp_head`/`wp_footer`) | Toggle ID/EN dihapus |
| `templates/content-home.php` | `front-page.php` (renderer, baca opsi `sgi_home`) | Teks/kartu/urutan via Tampilan > Konten Situs, tanpa edit PHP |
| `templates/content-layanan.php` | `archive-layanan.php` (query CPT dinamis) | Layanan baru otomatis muncul |
| `templates/content-service.php` | `single-layanan.php` (baca postmeta) | Berlaku untuk slug apa pun |
| `templates/content-portfolio.php` | `page-portofolio.php` + CPT `portfolio` | Filter 8 kategori dipertahankan |
| `templates/content-tentang.php` | `page-tentang.php` | values/timeline/team dari meta JSON |
| `templates/content-kontak.php` | `page-kontak.php` (form WA deep link) | Info dari opsi `sgi_settings`, hero + dropdown dari opsi `sgi_kontak` |
| `templates/content-doc.php` | `page.php` generik (privasi/syarat/halaman baru) | Isi via editor blok |
| `layanan/*.php` (9 file) | 1 CPT `layanan` + `single-layanan.php` | Slug bebas, tanpa daftar kunci |
| `admin/` + `includes/` + `tools/setup-admin.php` | wp-admin + Tampilan > Konten Situs + meta boxes | Pensiun setelah cutover |
| `js/i18n.js` + `data-i18n` | Dihapus total | Grep verifikasi: nol temuan |
| `js/main.js` + `js/site-config.js` | `assets/js/main.js` + `wp_localize_script(SGI_SETTINGS)` | Menu/reveal/counter/FAQ/filter/form WA sama |
| `css/*.css` | `assets/css/*.css` | Blok `.lang-toggle` mati dibuang |
| `.htaccess` redirect `.html` | `inc/redirects.php` (301) | Detail layanan dinamis via `template_redirect` |
| `sitemap.xml` statis | Sitemap inti WP (otomatis ikut CPT) | Tanpa URL `.html`/`.php` lama |

Struktur konten di wp-admin:

- Menu utama: Tampilan > Menu, lokasi Navigasi utama (Beranda, Layanan,
  Kontak). Header + mobile-menu membaca Menu ini; kosong = fallback 3 item
  bawaan. Seed otomatis saat aktivasi theme + Tools > Sirius Import.
  Label (judul item) bisa diubah tanpa membuka editor menu: Tampilan >
  Konten Situs > tab Header & navigasi. Tambah/urutkan/hapus item tetap di
  Tampilan > Menu. Bila lokasi primary kosong, tab itu menampilkan tiga
  label bawaan + tombol "Buat menu bawaan".
- Layanan: menu Layanan (CPT) — tambah/ubah/hapus bebas, slug apa pun.
- Portofolio: menu Portofolio + taksonomi 8 kategori valid.
- Halaman: Tentang / Layanan / Portofolio / Kontak (pilih Template) + Privasi / Syarat (generik).
- Artikel: menu Posts memakai `index.php` bila suatu hari butuh blog.
- Konten situs: Tampilan > Konten Situs (kapabilitas `edit_theme_options`;
  halaman ini menggantikan Settings > Sirius yang sudah tidak ada). Empat
  tab: Header & navigasi (label nav, tombol CTA header), Hero & angka,
  Section beranda, Kontak & situs (termasuk Halaman Kontak).

## Konvensi penyimpanan (satu aturan untuk semua opsi)

- **Editan admin selalu menang.** Import ulang (Tools > Sirius Import) hanya
  mengisi kunci yang belum ada; tidak menimpa yang sudah disimpan.
- **Kosong = bawaan.** Field pengaturan yang dikosongkan kembali ke nilai
  bawaan kode saat dibaca (lihat `sgi_home()`, `sgi_kontak()`,
  `sgi_settings()`, `sgi_default_nav()`). Form menampilkan nilai tersimpan mentah.
- Berlaku untuk: opsi `sgi_home`, `sgi_kontak`, `sgi_settings` (termasuk
  `ctaLabel`/`ctaHref`), Menu utama (seed tidak menimpa Menu yang sudah
  disusun editor).

## Beranda tanpa edit PHP (Tampilan > Konten Situs)

- **Tombol header:** label (`ctaLabel`, bawaan "Konsultasi Gratis") dan
  tautan (`ctaHref`, bawaan `/kontak/`) dipakai tombol desktop dan seluler.
  Kosong = kembali ke bawaan.
- **Teks section:** Hero (eyebrow, judul boleh `<span>`, lead, 2 tombol + link,
  3 baris status), judul/deskripsi Layanan, Keunggulan, Alur, Studi kasus,
  Testimoni, CTA.
- **Tambah/kurang kartu:** textarea satu-baris-per-item format
  `judul | deskripsi` (Why, Alur maks 8, Testimoni maks 6) atau
  `kategori | judul | deskripsi` (Studi kasus maks 6). KPI/Statistik format
  `angka | label` (3 / 4 baris). Klien satu per baris (maks 12).
- **Kartu layanan otomatis** dari menu Layanan (publish, urut judul):
  deskripsi diambil dari meta `_sgi_lead` (fallback excerpt). Atur
  `Jumlah kartu` (1-8), `Sembunyikan slug` (satu per baris), dan toggle
  kartu kombinasi. Bila CPT kosong, tampil 5 kartu bawaan.
- **Susun/tutup section:** field Urutan, pisah koma. Nama valid:
  `hero,stats,layanan,why,alur,cases,testi,cta`. Hapus nama = sembunyikan;
  tukar posisi = tukar urutan tampil. Section list yang dikosongkan
  otomatis disembunyikan.
- Import ulang (Tools > Sirius Import) hanya mengisi kunci yang belum ada;
  editan admin tidak ditimpa.

## Verifikasi perubahan

- **Lokal (tanpa WordPress):** `php -l` per file + `php tools/sgi-smoke.php`
  (stub WP: fallback nav, merge opsi, parse pasangan, render header, render
  halaman Konten Situs + handler simpan), `php tools/sgi-static-check.php`
  (aturan mekanis AGENTS), dan `python tools/verify-seed.py`.
  Semua jalan di CI (`php-lint.yml`: job `php-lint`, `php-smoke`,
  `static-check`, `openspec`).
- **Cek UI admin:** `php tools/sgi-admin-harness.php header > sgi-admin.html`,
  lalu buka di browser atau Chrome headless
  `--headless=new --screenshot=sgi-admin.png --window-size=390,1200`.
  Ulangi untuk tab `hero`, `sections`, `kontak` dan argumen `nojs`.
  Ini pengganti sah "serve check" lokal; serve sungguhan butuh runtime WP.
- **Pasca-deploy (produksi):** cek status via curl, contoh:
  `curl -s -o NUL -w '%{http_code}' https://siriusglobal.id/` (harap 200),
  `/layanan/`, `/layanan/app-development/`, `/kontak/`,
  `/kontak/?layanan=odoo-erp` (dropdown terpraseleksi), `/tentang/`,
  `/portofolio/` (tetap hidup meski tak di nav). Satu URL per panggilan
  (lihat `docs/agents/shell-notes.md`).
