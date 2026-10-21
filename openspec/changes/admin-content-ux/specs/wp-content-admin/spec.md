# Spec Delta

## Purpose

Menyediakan satu tempat di wp-admin untuk mengubah navigasi header, tombol CTA, dan konten beranda/kontak dengan pengelompokan yang jelas, sehingga admin non-teknis tidak perlu menyentuh kode maupun memasang page builder.

## ADDED Requirements

### Requirement: Pengaturan konten berada di menu Tampilan

Sistem SHALL menyediakan halaman "Konten Situs" sebagai submenu Tampilan, hanya dapat diakses pengguna dengan kapabilitas `edit_theme_options`, dan menyimpan seluruh perubahan pada option yang sudah ada.

#### Scenario: Admin membuka pengaturan dari Tampilan

- **WHEN** admin dengan kapabilitas `edit_theme_options` membuka Tampilan > Konten Situs
- **THEN** halaman pengaturan terbuka dengan empat tab dan data tersimpan terisi

#### Scenario: Pengguna tanpa izin tidak dapat mengakses

- **WHEN** pengguna tanpa kapabilitas `edit_theme_options` membuka URL halaman pengaturan
- **THEN** WordPress menolak akses dan tidak ada data yang berubah

### Requirement: Entry point terlihat dari area Tampilan

Sistem SHALL menampilkan jalan masuk ke halaman pengaturan dari kartu theme aktif di Tampilan > Tema, dari notice di halaman Tampilan > Tema, dan dari admin bar saat admin melihat situs publik.

#### Scenario: Admin berada di halaman Tema

- **WHEN** admin membuka Tampilan > Tema
- **THEN** tampil link "Pengaturan konten" pada kartu theme aktif dan satu kartu info yang menuju halaman pengaturan

#### Scenario: Admin melihat situs dari depan

- **WHEN** admin dengan kapabilitas `edit_theme_options` membuka halaman publik dalam keadaan login
- **THEN** admin bar menampilkan node "Edit konten situs" yang menuju halaman pengaturan

### Requirement: URL pengaturan lama tetap berfungsi

Sistem SHALL mengalihkan permintaan ke `options-general.php?page=sgi` menuju lokasi pengaturan yang baru tanpa menampilkan halaman kosong atau error.

#### Scenario: Bookmark lama dibuka

- **WHEN** admin membuka bookmark `wp-admin/options-general.php?page=sgi`
- **THEN** admin diarahkan ke halaman Konten Situs dengan tab default aktif

### Requirement: Label navigasi dapat diubah tanpa meninggalkan halaman pengaturan

Sistem SHALL menyediakan input judul untuk setiap item menu pada lokasi `primary` dan menyimpan perubahan judul ke menu WordPress yang sama, tanpa mengubah URL, urutan, induk, kelas, atau target item.

#### Scenario: Rename label navigasi

- **WHEN** admin mengubah "Beranda" menjadi "Home" pada tab Header & navigasi lalu menyimpan
- **THEN** header dan menu seluler menampilkan "Home" dengan URL yang sama seperti sebelumnya

#### Scenario: Atribut item lain dipertahankan

- **WHEN** admin hanya mengubah judul satu item menu yang memiliki kelas CSS dan target `_blank`
- **THEN** item tersebut tetap mempertahankan kelas, target, urutan, dan induknya

#### Scenario: Gagal menyimpan label

- **WHEN** pembaruan satu item menu mengembalikan error
- **THEN** halaman menampilkan pesan bahwa sebagian label gagal diperbarui dan tidak mengklaim semua perubahan tersimpan

#### Scenario: Menu belum tersedia

- **WHEN** lokasi menu `primary` belum memiliki menu
- **THEN** tab Header & navigasi menampilkan keterangan bahwa situs memakai tiga label bawaan dan menyediakan aksi untuk membuat menu bawaan

#### Scenario: Menu terpasang tetapi kosong

- **WHEN** lokasi menu `primary` menunjuk menu tanpa item dan admin menekan "Buat menu bawaan"
- **THEN** tiga item bawaan ditambahkan ke menu tersebut dan input rename muncul

#### Scenario: Menu bawaan gagal dibuat

- **WHEN** pembuatan item menu bawaan gagal
- **THEN** halaman menampilkan pesan kegagalan dan tidak mengklaim menu berhasil dibuat

### Requirement: Tombol CTA header dapat diubah

Sistem SHALL menyimpan label dan tautan tombol CTA header pada option pengaturan situs dengan nilai bawaan "Konsultasi Gratis" dan `/kontak/`, lalu memakainya pada tombol desktop maupun seluler.

#### Scenario: Ubah label CTA

- **WHEN** admin mengubah label CTA header menjadi "Diskusi Gratis" lalu menyimpan
- **THEN** tombol di header desktop dan menu seluler menampilkan "Diskusi Gratis"

#### Scenario: Label CTA dikosongkan

- **WHEN** admin mengosongkan label atau tautan CTA lalu menyimpan
- **THEN** situs menampilkan nilai bawaan "Konsultasi Gratis" dan tautan `/kontak/`

#### Scenario: Tautan CTA absolut

- **WHEN** admin mengisi tautan CTA dengan URL `https://` lengkap
- **THEN** tombol memakai URL tersebut apa adanya tanpa ditambahi domain

### Requirement: Satu penyimpanan menyimpan seluruh field

Sistem SHALL menyimpan semua field dari keempat tab dalam satu aksi simpan, termasuk field pada tab yang sedang tidak aktif, dan tidak mengosongkan nilai tab lain.

#### Scenario: Simpan dari tab pertama

- **WHEN** admin mengubah satu field di tab Header & navigasi lalu menyimpan tanpa membuka tab lain
- **THEN** seluruh field tab Hero, Section beranda, dan Kontak tetap bernilai seperti sebelum simpan

#### Scenario: Perpindahan tab tidak menghilangkan isian

- **WHEN** admin mengisi field di satu tab, berpindah tab, lalu menyimpan
- **THEN** isian pada tab sebelumnya ikut tersimpan

### Requirement: Navigasi tab dapat di-deep-link dan tetap berfungsi tanpa JavaScript

Sistem SHALL menyimpan tab aktif pada URL, mengembalikan admin ke tab yang sama setelah simpan, dan menampilkan seluruh panel sehingga tetap dapat diedit ketika JavaScript tidak berjalan.

#### Scenario: Simpan dari tab tertentu

- **WHEN** admin menyimpan dari tab Section beranda
- **THEN** setelah redirect tab Section beranda aktif kembali dan tampil notice "Tersimpan"

#### Scenario: JavaScript dimatikan

- **WHEN** halaman pengaturan dibuka tanpa JavaScript
- **THEN** semua panel tab tampil berurutan dan seluruh field dapat diisi serta disimpan

### Requirement: Umpan balik simpan dan validasi

Sistem SHALL menampilkan notice sukses setelah simpan, menolak email tidak valid dengan pesan kesalahan tanpa menyimpan perubahan, dan menyediakan tombol untuk membuka beranda di tab baru.

#### Scenario: Email tidak valid

- **WHEN** admin mengganti email menjadi `bukan-email` lalu menyimpan
- **THEN** tampil pesan "Email tidak valid. Data tidak disimpan." dan option tidak berubah

#### Scenario: Preview beranda

- **WHEN** admin menekan tombol "Lihat beranda"
- **THEN** beranda situs terbuka di tab baru tanpa meninggalkan halaman pengaturan

### Requirement: Format penyimpanan tidak berubah

Sistem SHALL tetap memakai option `sgi_home`, `sgi_kontak`, dan `sgi_settings` dengan struktur dan format input yang sama seperti sebelum perubahan.

#### Scenario: Data lama tetap terbaca

- **WHEN** theme diperbarui pada situs yang sudah memiliki isi option
- **THEN** seluruh teks, urutan section, dan daftar item tampil sama tanpa langkah migrasi

#### Scenario: Import ulang tidak menimpa editan

- **WHEN** admin menjalankan Tools > Sirius Import setelah mengubah konten
- **THEN** nilai yang sudah disimpan admin tidak berubah

### Requirement: Halaman pengaturan dapat dipakai di layar kecil

Sistem SHALL menjaga halaman pengaturan tetap terbaca dan dapat dioperasikan pada lebar 360px dan pada zoom 200%, tanpa scroll horizontal pada tab, field, atau tombol simpan.

#### Scenario: Akses dari layar ponsel

- **WHEN** admin membuka halaman pengaturan pada lebar 360px
- **THEN** tab dapat dipilih, field dapat diisi, dan tombol simpan terjangkau tanpa scroll horizontal
