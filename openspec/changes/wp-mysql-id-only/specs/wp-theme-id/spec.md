# Spec Delta

## Purpose

Theme WordPress ID-only yang mem-port layout, konten, dan CSS situs saat ini apa adanya, sehingga pengunjung melihat tampilan sama persis namun seluruh teks dikelola lewat wp-admin.

## ADDED Requirements

### Requirement: Visual sama persis ID-only
The system SHALL render header, hero, section beranda, kartu layanan, halaman layanan, portofolio, tentang, kontak, privasi/syarat dengan struktur dan gaya yang sama seperti template PHP/JSON sekarang, seluruh string Bahasa Indonesia, tanpa toggle bahasa dan tanpa atribut i18n.

#### Scenario: Beranda sama struktur
- **WHEN** pengunjung membuka beranda
- **THEN** tampil hero + foto + kartu KPI, stat band, grid layanan 5 kartu + 1 kartu kombinasi, split why, steps 4 kolom, cases 3 kartu, testimoni 2 kutipan, dan CTA band dengan gaya CSS yang di-port.

#### Scenario: Tanpa sisa i18n
- **WHEN** halaman mana pun dirender
- **THEN** tidak ada elemen `data-i18n`, tidak ada `js/i18n.js`, tidak ada toggle ID/EN di header.

### Requirement: CPT layanan terbuka tambah-kurang
The system SHALL menyediakan CPT `layanan` dengan slug bebas: editor dapat menambah, mengubah, dan menghapus layanan lewat wp-admin tanpa perubahan kode. Sembilan slug seed awal (app-development, ai-solution, iot, odoo-erp, fleet-management, infrastruktur, dashboard-bi, cybersecurity, training) adalah data awal, bukan daftar kunci.

#### Scenario: Tambah layanan baru
- **WHEN** editor membuat layanan baru berslug `cloud-backup` dan mempublikasikannya
- **THEN** URL `/layanan/cloud-backup/` dapat dibuka dengan status 200 memakai template detail layanan yang sama.

#### Scenario: Hapus layanan
- **WHEN** editor menghapus sebuah layanan ke sampah
- **THEN** URL-nya menjawab 404 theme dan daftar layanan di arsip tidak lagi menampilkannya.

### Requirement: Struktur konten layanan lengkap
The system SHALL menyimpan per layanan: judul, lead, h1, meta description, intro + checklist (maks 20), metrik (maks 8), pricing tiers (maks 6, satu featured), steps (maks 8), FAQ (maks 20), CTA dengan teks dan href.

#### Scenario: Detail layanan utuh
- **WHEN** pengunjung membuka satu halaman layanan
- **THEN** tampil breadcrumb, hero, intro + checklist, metrik, grid pricing dengan tier featured disorot, steps, FAQ singkat, dan CTA band.

#### Scenario: Batas list ditegakkan
- **WHEN** data melebihi batas (mis. tiers > 6)
- **THEN** penyimpanan ditolak dengan pesan batas yang jelas dan data lama tidak rusak.

### Requirement: Halaman dan taksonomi inti
The system SHALL menyediakan pages tentang (values <= 12, timeline <= 12, team <= 12), layanan (cards dari seed), portofolio (items <= 24 dengan kategori erp/ai/fleet/app/iot/infra/sec/bi), kontak (email valid, phone, WA href, alamat, jam, mapEmbed), privasi, syarat, dan 404.

#### Scenario: Portofolio terfilter kategori valid
- **WHEN** pengunjung membuka portofolio
- **THEN** setiap item menampilkan kategori yang berasal dari 8 nilai valid dan item berkategori asing tidak tampil.

#### Scenario: Kontak dapat dihubungi
- **WHEN** pengunjung membuka kontak
- **THEN** terlihat email valid, nomor telepon, link WA, alamat, jam operasional, dan embed peta bila diisi.

### Requirement: Aksesibilitas dan responsif setara
The system SHALL memenuhi: meta viewport di semua template, tidak ada horizontal scroll di 360/768/1280px, grid runtuh via class grid (bukan inline fixed), hamburger + mobile menu dengan aria-expanded terupdate, target sentuh min 24px, input min 16px, layout utuh di zoom 200%.

#### Scenario: Mobile lolos
- **WHEN** halaman dibuka di viewport 360px dan zoom 200%
- **THEN** tidak ada scroll horizontal, teks tidak terpotong/overlap, dan menu seluler dapat dibuka dan semua link terjangkau keyboard.
