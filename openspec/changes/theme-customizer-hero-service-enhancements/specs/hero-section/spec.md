# Spec Delta: hero-section

## Purpose

Mengatur perilaku visual dan interaksi section hero pada beranda, termasuk hyperlink pada kartu status hero dan kondisi render kartu KPI agar tidak meninggalkan container kosong.

## ADDED Requirements

### Requirement: Clickable hero status card
Sistem SHALL menyediakan input link opsional `sgi_hero_card1_href` di panel pengaturan Hero sehingga kartu status hero dapat diklik menuju target URL saat diisi.

#### Scenario: Kartu status hero diberi tautan URL
- **WHEN** admin mengisi URL valid pada pengaturan `sgi_hero_card1_href`
- **THEN** kartu `.hero-photo-card` di-render sebagai elemen tautan interaktif (`<a>`) yang mengarah ke URL tersebut

#### Scenario: Kartu status hero tanpa tautan URL
- **WHEN** pengaturan `sgi_hero_card1_href` dibiarkan kosong
- **THEN** kartu `.hero-photo-card` di-render sebagai container non-link (`<div>`) biasa tanpa tautan

### Requirement: Conditional KPI hero card rendering
Sistem SHALL hanya me-render elemen container `.dash-card` jika terdapat data KPI yang valid dan tidak kosong.

#### Scenario: Data KPI dikosongkan oleh admin
- **WHEN** seluruh baris KPI pada panel Hero dikosongkan atau bernilai 0 tanpa label teks
- **THEN** elemen `.dash-card` tidak di-render ke dalam markup HTML beranda dan tidak memunculkan kotak putih kosong

#### Scenario: Data KPI terisi
- **WHEN** terdapat data KPI dengan angka dan label yang terisi
- **THEN** elemen `.dash-card` beserta deretan `.kpi` di dalamnya di-render secara normal
