# Design

## Context

Lihat `proposal.md` Why. Kondisi kini (hasil `cms-json-v1`): form admin me-render item array yang ada (`admin/portfolio.php`, `service_edit.php`, `tentang.php`, `home.php`) dan template publik sudah loop generik, kecuali `content-service.php` yang mengasumsikan tier tengah (`$ti === 1`) sebagai featured dan 3 tier pricing.

## Goals / Non-Goals

**Goals:**
- Tambah/hapus item murni server-side (tanpa JS framework): tambah via query `?add=1` yang me-render satu blok kosong; hapus via checkbox/tombol + `confirm()` + save.
- Aturan featured eksplisit per tier (`featured: true`), bukan posisi.

**Non-Goals:**
- Tanpa drag-and-drop susun ulang (urutan = urutan form; pindah posisi via hapus + tambah ulang bila perlu).
- Tanpa halaman/slug baru, tanpa section dinamis, tanpa ubah skema JSON.
- Tanpa AJAX; save tetap satu POST penuh dengan backup + optimistic-lock existing.

## Decisions

1. **Tambah = GET `?add=1`, bukan JS-kloning.** Rasional: blok item berisi name array + select kategori + textarea berpasangan yang rawan salah kloning via JS; render server-side selalu konsisten dengan validasi. Alternatif JS template ditolak (duplikasi markup di dua tempat).
2. **Hapus = checkbox `del[]` per item + `confirm()` saat submit bila ada yang dicentang.** Rasional: satu mekanisme untuk semua form, tanpa endpoint hapus terpisah (permukaan CSRF lebih kecil). Item kosong (semua field kosong) tetap dilewati seperti kini.
3. **Featured tier eksplisit.** Tambah field `featured` boolean per tier di seed (tier tengah existing = true) + radio/checkbox di form; renderer memakai flag, bukan indeks. Migrasi seed sekali jalan via skrip.
4. **Batas jumlah di server (bukan cuma hint):** portofolio 24, FAQ 20, tier 6, checklist 20, metrik 8, tahapan 8, values/team/timeline 12, clients 12, cases 6. Angka dari layout (grid 3–4 kolom, halaman tetap rapi) dan performa (satu JSON < 200KB).
5. **Tombol tambah/hapus min-height 44px**, form tetap 16px, konfirmasi hapus kalimat Indonesia jelas — menutup lensa mobile + destructive-action review v1.

## Risks / Trade-offs

- [Risk] Admin menambah 24 kartu ber-gambar besar → halaman berat → Mitigasi: batas jumlah + himbauan ukuran di hint upload (sudah ada 2MB).
- [Risk] Hapus tidak sengaja → Mitigasi: `confirm()` + snapshot otomatis tiap save (restore via menu Backup).
- [Risk] GET `?add=1` di-bookmark lalu dibuka belakangan → Mitigasi: tidak menulis apa pun, hanya me-render blok kosong; aman di-refresh.
- [Trade-off] Tanpa reorder drag-and-drop. Diterima: kebutuhan susun ulang jarang; workaround hapus + tambah ulang didokumentasikan di dashboard.

## Migration Plan

1. Patch seed: tambah `featured` pada tier (skrip sekali jalan, tier indeks-1 = true).
2. Ubah 4 form + `_form.php` helper + renderer tier; tambah batas di `cms_validate` (perluasan kecil, kompatibel ke belakang).
3. Verifikasi di PHP lokal: tambah/hapus tiap daftar, batas ditolak, render publik, serve 200, review ulang `better-interface` (scope: file yang diubah).
4. Deploy via alur existing (data produksi tak tersentuh; seed `featured` hanya dipakai bila file runtime belum ada — runtime existing tanpa flag dianggap tier-1-featured seperti kini? Tidak: fallback renderer = indeks-1 bila flag absen, jadi produksi lama tetap identik).

## Open Questions

- Tidak ada yang menunda; batas jumlah di atas final untuk v1 perubahan ini.
