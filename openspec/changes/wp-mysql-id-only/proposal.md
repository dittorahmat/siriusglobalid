# Proposal

## Why

Panel admin CMS JSON (`admin/`, commit `cb0f8a5`) fungsional tapi UX jelek: form mentah, editor ID/EN berdampingan dalam textarea, tambah/hapus item lewat query param. Editor non-teknis butuh `wp-admin` standar. SQLite dibatalkan karena menambah kompleksitas; hosting sudah terbukti punya PHP 8.2 + `pdo_sqlite`/`sqlite3`, tapi MySQL default Softaculous lebih standar untuk operasional WP.

## What Changes

- **BREAKING**: Root `public_html` diganti dari situs PHP/JSON menjadi WordPress (instalasi oleh user via Softaculous, MySQL otomatis). Kode PHP/JSON lama (`*.php` publik, `admin/`, `includes/`, `templates/`, `tools/`, `$HOME/data/*.json`) tidak lagi menjadi runtime; dipertahankan di git sebagai sumber migrasi saja.
- Theme baru `wp-content/themes/sirius-custom`: port 1:1 layout sekarang (`templates/layout.php`, `header.php` tanpa toggle ID/EN, `content-home.php`, `content-service.php`, `content-layanan/portfolio/tentang/kontak/dok`, CSS `tokens/base/components/pages` apa adanya). Tanpa page builder, tanpa Gutenberg styling bebas.
- Hapus dari port: `js/i18n.js`, atribut `data-i18n`, seluruh sisi `en` JSON, toggle ID/EN header. Situs ID-only.
- CPT `layanan` terbuka (slug bebas, tambah/ubah/hapus lewat wp-admin tanpa ubah kode). 9 slug seed awal (`app-development`, `ai-solution`, `iot`, `odoo-erp`, `fleet-management`, `infrastruktur`, `dashboard-bi`, `cybersecurity`, `training`) dipertahankan sebagai data awal + taksonomi kategori portfolio (`erp/ai/fleet/app/iot/infra/sec/bi`) + pages (`tentang/layanan/portofolio/kontak/privasi/syarat`).
- Importer sekali jalan (WP-CLI/PHP): `data/seed/*.json` sisi `id` menjadi post/meta/options. Sisi `en` diabaikan.
- Redirect `.html` lama ke permalink WP + `sitemap.xml` baru agar SEO tidak pecah.
- Admin default `mudita` dibuat di Dashboard WP (bukan di repo), password awal sekali pakai wajib diganti saat login pertama (tidak dicatat di repo).
- Deploy baru: git hanya source of truth untuk theme + importer; core/uploads/DB ikut standar WP/Softaculous/cPanel backup. `AGENTS.md`, `.cpanel.yml`, `gh-deploy.php` disesuaikan eksplisit.

## Capabilities

### New Capabilities
- `wp-theme-id`: theme ID-only yang mem-port layout/konten/CSS sekarang tanpa i18n, dengan CPT layanan, taksonomi portfolio, dan template halaman.
- `wp-migrate`: migrasi seed JSON ke WP + redirect URL lama + sitemap + provisioning admin + alur deploy/backup baru.

### Modified Capabilities
- Tidak ada (inventaris `openspec list --specs` kosong; tidak ada spec durable yang diubah).

## Impact

- Terpengaruh: `public_html` (timpa total setelah backup), `wp-content/themes/sirius-custom/` (baru), `.htaccess` (aturan WP + redirect `.html`), `sitemap.xml`, `robots.txt`, `AGENTS.md` (izin WP theme), `.cpanel.yml` + `gh-deploy.php` (scope theme-only), `data/seed/*.json` (sumber importer, read-only).
- Tidak berubah: desain visual, copy ID, 9 slug layanan awal sebagai data awal (bukan daftar kunci), struktur nav utama, foto picsum seed.
- Prasyarat user: backup `public_html` lama + `$HOME/data`, install WP via Softaculous di root (PHP 8.2 per-domain sudah aktif), jangan tambah builder/plugin lain sebelum theme dipasang.
- Wajib lolos sebelum ship (AGENTS.md): review `better-interface`, taste check `design-taste-frontend` (redesign-preserve), cek mobile-friendly, serve check 200.
