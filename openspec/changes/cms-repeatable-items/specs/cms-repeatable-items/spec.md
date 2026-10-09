# Spec Delta

## Purpose

Memungkinkan editor tunggal menambah dan menghapus item daftar konten (kartu portofolio, FAQ, tier harga, checklist, metrik, tahapan, nilai/tim) langsung dari panel admin tanpa menyentuh file JSON.

## ADDED Requirements

### Requirement: Admin dapat menambah item daftar baru

Sistem SHALL menyediakan tombol tambah item di setiap form daftar, yang menambahkan satu entri kosong di akhir dan menyimpannya bersama seluruh form.

#### Scenario: Tambah kartu portofolio

- **WHEN** admin menekan tambah kartu lalu mengisi kategori, gambar, dan judul dan menyimpan
- **THEN** kartu baru tampil di halaman portofolio publik pada urutan terakhir

#### Scenario: Tambah FAQ layanan

- **WHEN** admin menambah satu pasangan pertanyaan-jawaban kosong lalu menyimpan
- **THEN** FAQ baru tampil di halaman layanan terkait

### Requirement: Admin dapat menghapus item daftar

Sistem SHALL menyediakan tombol hapus per item dengan konfirmasi, dan item yang dihapus hilang dari situs setelah save.

#### Scenario: Hapus kartu portofolio

- **WHEN** admin menekan hapus pada kartu ke-3, mengonfirmasi, lalu menyimpan
- **THEN** kartu tersebut tidak lagi tampil di halaman publik dan tidak ada di JSON

#### Scenario: Batal hapus

- **WHEN** admin menekan hapus lalu membatalkan konfirmasi
- **THEN** tidak ada perubahan dan form tetap seperti semula

### Requirement: Validasi per item dan batas jumlah

Sistem SHALL memvalidasi setiap item (kategori sesuai allowlist, field wajib terisi bila item tidak kosong, item kosong diabaikan) dan menolak save yang melewati batas jumlah dengan pesan yang menyebut daftarnya.

#### Scenario: Item melewati batas

- **WHEN** admin menyimpan 25 kartu portofolio (batas 24)
- **THEN** penyimpanan ditolak dengan pesan batas portofolio dan file di disk tidak berubah

#### Scenario: Item baru berkategori salah

- **WHEN** kartu baru memakai kategori di luar allowlist lalu disimpan
- **THEN** penyimpanan ditolak dengan pesan nomor item yang salah

### Requirement: Renderer publik generik tanpa asumsi jumlah

Sistem SHALL merender seluruh item array apa pun jumlahnya, melewati item kosong, dan tidak menggantungkan gaya pada posisi (mis. tier tengah selalu featured diganti aturan eksplisit per tier).

#### Scenario: Empat tier harga

- **WHEN** layanan memiliki 4 tier dan tier ke-2 ditandai featured
- **THEN** keempat tier tampil dan hanya tier bertanda yang memakai gaya featured
