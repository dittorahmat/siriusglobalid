# Proposal

## Why

CMS v1 (`cms-json-v1`) hanya bisa menyunting item yang sudah ada; menambah kartu portofolio, FAQ, tier harga, atau checklist baru masih butuh edit JSON manual. Editor tunggal butuh tombol tambah/hapus item langsung dari admin untuk daftar yang strukturnya sudah array.

## What Changes

- Tambah tombol tambah/hapus item (dengan konfirmasi hapus) di form admin untuk: kartu portofolio, FAQ layanan, tier harga (tambah item isi paket; tier ke-4+ dirender generik), checklist intro, metrik, tahapan, serta values/timeline/team di halaman Tentang.
- Renderer publik tetap generik (loop array), tanpa batas jumlah item yang di-hardcode, dengan item kosong tetap dilewati.
- Validasi per item tetap berlaku (kategori portofolio allowlist, field wajib); item yang gagal validasi menolak seluruh save dengan pesan item ke berapa.
- Batas wajar per daftar (mis. portofolio maks 24, FAQ maks 20) agar halaman tidak jebol; melewati batas ditolak dengan pesan.

## Capabilities

### New Capabilities
- `cms-repeatable-items`: tambah, hapus, dan susun ulang item daftar konten (portofolio, FAQ, tier, checklist, metrik, tahapan, values/tim) lewat admin dengan validasi per item dan batas jumlah.

### Modified Capabilities
- Tidak ada (belum ada spec utama di `openspec/specs/`; `cms-json-v1` belum diarsip).

## Impact

- Terpengaruh: `admin/portfolio.php`, `admin/service_edit.php`, `admin/tentang.php`, `admin/home.php` (clients/case alts), `admin/_form.php` (helper tombol), template publik yang loop (`content-portfolio.php` sudah generik; `content-service.php` tier generik — perlu lepas asumsi tier tengah featured).
- Tidak berubah: skema JSON (array tetap array), `includes/store.php`/`store_json.php` (validasi per item sudah ada), deploy, URL, desain visual.
- Wajib sama seperti v1: review `better-interface`, taste check `design-taste-frontend`, cek mobile (tombol tambah/hapus min 44px, form tetap 16px), serve check 200.
