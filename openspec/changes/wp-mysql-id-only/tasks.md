# Tasks

## 1. Pra-syarat server (user di cPanel)

- [x] 1.1 Backup `public_html` lama + `$HOME/data` dan verifikasi arsip dapat diunduh dan dibuka.
- [x] 1.2 Install WordPress via Softaculous di root domain dengan PHP 8.2 per-domain dan laporkan URL wp-admin + nama DB.
- [x] 1.3 Kunci WP bawaan: biarkan theme default, jangan tambah page builder/plugin bahasa, dan verifikasi halaman default WP 200.

## 2. Scaffold theme sirius-custom

- [x] 2.1 Buat kerangka theme (`style.css`, `functions.php` enqueue CSS port, `header.php` tanpa toggle bahasa, `footer.php`, `front-page.php`, `single-layanan.php`, `page-*.php`, `404.php`) dan verifikasi theme aktif tanpa PHP error.
- [x] 2.2 Port CSS `tokens/base/components/pages` + `js/main.js` (tanpa `js/i18n.js`) dan verifikasi tidak ada referensi `data-i18n` tersisa via grep.
- [x] 2.3 Dokumentasikan pemetaan template lama ke template WP di `wp-content/themes/sirius-custom/README.md` dan verifikasi setiap file lama punya padanan.

## 3. CPT, taksonomi, dan field ID-only

- [x] 3.1 Daftarkan CPT `layanan` terbuka (slug bebas, tambah/ubah/hapus via wp-admin tanpa ubah kode; 9 slug seed sebagai data awal), taksonomi kategori portfolio (8 nilai), dan pages inti, lalu verifikasi layanan baru berslug bebas tampil 200 dan yang dihapus menjadi 404.
- [x] 3.2 Tambahkan meta fields layanan (judul/lead/h1/meta/intro/checklist/metrik/tiers/steps/faq/cta) dengan batas list lama dan verifikasi kelebihan batas ditolak tanpa merusak data.
- [x] 3.3 Render halaman detail layanan (breadcrumb, hero, checklist, metrik, pricing featured, steps, FAQ, CTA) dan verifikasi 1 layanan contoh tampil utuh 200.

## 4. Importer seed sisi ID

- [x] 4.1 Tulis importer sekali jalan dari `data/seed/*.json` + `services/*.json` sisi `id` (terima slug apa pun) dan verifikasi counts sama dengan seed (settings/home/semua layanan seed/portfolio/tentang/kontak/dok).
- [x] 4.2 Buat importer idempoten (run kedua nol duplikat) dan verifikasi dengan dua run berurutan.
- [x] 4.3 Tangani seed rusak (JSON invalid/field wajib hilang) tanpa menimpa data baik dan verifikasi lewat fixture rusak + cek log.

## 5. URL, SEO, dan admin

- [x] 5.1 Terapkan redirect 301 `.html`/`.php` lama ke permalink dan verifikasi tiap URL lama 301 ke tujuan 200.
- [x] 5.2 Terbitkan `sitemap.xml` + `robots.txt` kanonis tanpa URL lama dan verifikasi lolos validasi parser.
- [x] 5.3 Buat admin `mudita` di Dashboard, paksa ganti password awal, pindai repo bebas kredensial, dan verifikasi login pertama meminta password baru.

## 6. Verifikasi akhir dan serah terima

- [x] 6.1 Jalankan review `better-interface` + taste check `design-taste-frontend` + cek mobile (360/768/1280, hamburger, tap target, input 16px, zoom 200%) dan verifikasi HIGH bersih + serve check 200 semua halaman utama.
- [x] 6.2 Sesuaikan `AGENTS.md`, `.cpanel.yml`, `gh-deploy.php` ke scope theme-only dan verifikasi deploy theme tidak menyentuh DB/uploads.
- [x] 6.3 Serahkan runbook backup/restore cPanel+DB dan verifikasi restore staging tampil utuh. (DITUNDA atas persetujuan user 10-Okt-2026; runbook di `docs/wordpress-runbook.md` terserah terima, latihan restore menyusul.)
