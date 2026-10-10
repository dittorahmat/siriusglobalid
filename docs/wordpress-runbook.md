# Runbook WordPress siriusglobal.id

## Instalasi (user, sekali)
1. Backup: unduh `public_html` + `$HOME/data` via File Manager/Backup cPanel.
2. PHP Selector: `siriusglobal.id` di 8.2 (sudah aktif).
3. Softaculous > WordPress > Install di root (`public_html`), catat nama DB.
4. Biarkan theme default; JANGAN tambah builder/plugin bahasa.

## Deploy theme (tiap rilis)
- Push `main` > cPanel Git > Update from Remote > Deploy (hanya
  `wp-content/themes/sirius-custom/` + `tools/sgi-importer.php`).
- Atau webhook `gh-deploy.php` (theme-only, tidak menyentuh DB/uploads).
- Verifikasi: wp-admin > Appearance > theme aktif, 5 URL utama 200.

## Importer konten
- `wp eval-file wp-content/sgi-importer/sgi-importer.php --seed=<path-seed>`
- Idempoten: run kedua melaporkan nol perubahan.
- Cek kontrak tanpa WP: `python tools/verify-seed.py`.

## Admin mudita (Dashboard only, bukan repo)
1. Users > Add New > username `mudita`, role Administrator.
2. Beri password awal sekali pakai, centang kirim email reset agar
   login pertama dipaksa ganti password. Jangan simpan password di repo/chat.
3. Hapus/amankan user install default bila tak dipakai.

## Backup/restore
- Backup: cPanel Backup (home dir + MySQL) terjadwal + sebelum tiap cutover.
- Restore staging: pulihkan file + DB, ganti URL via Softaculous staging
  bila domain beda, verifikasi beranda + 1 layanan + kontak 200.
- Rollback cutover: restore arsip `public_html` lama + `$HOME/data`.

## Tambah konten
- Layanan baru: Layanan > Add New (slug bebas) — otomatis masuk
  arsip `/layanan/`, sitemap, dan redirect dinamis `.html`.
- Portofolio: Portofolio > Add New + pilih 1 dari 8 kategori.
- Halaman: Pages > Add New + pilih Template (Tentang/Portofolio/Kontak/generik).
