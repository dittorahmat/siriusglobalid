# Tasks

## 1. Aturan featured eksplisit + batas

- [x] 1.1 Tambah flag `featured` per tier via skrip patch seed (tier indeks-1 = true) dan verifikasi 9 file seed lolos `cms_validate`
- [x] 1.2 Tambah batas jumlah per daftar di `cms_validate` (portofolio 24, FAQ 20, tier 6, checklist 20, metrik 8, tahapan 8, values/team/timeline 12, clients 12, cases 6) dan verifikasi save berlebih ditolak tanpa ubah file
- [x] 1.3 Ubah `content-service.php` memakai flag `featured` (fallback indeks-1 bila flag absen) dan verifikasi render 9 layanan identik dengan sebelumnya

## 2. Tambah/hapus di form admin

- [x] 2.1 Tambah helper tombol tambah (`?add=1` render blok kosong) + checkbox hapus + `confirm()` di `_form.php` dan verifikasi di satu form (portfolio) tombol min 44px dan blok kosong tervalidasi
- [x] 2.2 Terapkan pola tambah/hapus ke `service_edit.php` (checklist, metrik, tier items, tahapan, FAQ) dan verifikasi tambah+simpan tampil di publik, hapus+konfirmasi hilang dari JSON
- [x] 2.3 Terapkan pola tambah/hapus ke `tentang.php` (values, timeline, team) dan `home.php` (clients, case) dan verifikasi batas + item kosong dilewati
- [x] 2.4 Uji tolak: kategori invalid pada item baru, batal hapus via confirm, dan refresh `?add=1` dan verifikasi tidak ada tulis tanpa POST

## 3. Verifikasi akhir

- [x] 3.1 Jalankan lint PHP + render check (9 layanan, portofolio tambah/hapus) + serve 200 dan verifikasi tidak ada href `.html` baru
- [x] 3.2 Jalankan review `better-interface` scope file yang diubah + cek mobile (tombol 44px, input 16px, tabel scroll) dan verifikasi tidak ada temuan HIGH

## Workflow follow-up

- Arsipkan change setelah review proyek terpenuhi.
- Verifikasi hasil arsip dan sinkronisasi spec bila diperlukan.
