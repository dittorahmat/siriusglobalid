# Review dua sumbu — admin-content-ux

- **Fixed point:** `HEAD` `aada72f751e0a7ac8448d3127f4737d8dcfe4f99` (branch `main`)
- **Scope:** perubahan uncommitted (6 file termodifikasi + file baru) untuk halaman Tampilan > Konten Situs
- **Metode:** skill `code-review`, dua sub-agent paralel (Standards + Spec), lalu remediasi
- **Commit list:** kosong (`git log HEAD..HEAD`), seluruh diff ada di working tree

Sumber Standards: `AGENTS.md`, `GLOSSARY.md`, `docs/wordpress-runbook.md`,
`docs/agents/domain.md`, `docs/agents/shell-notes.md`, theme `README.md`, plus
smell baseline Fowler ch.3. Tidak ada `CODING_STANDARDS.md`/`CONTRIBUTING.md`.
Sumber Spec: `proposal.md`, `specs/wp-content-admin/spec.md`, `design.md`, `tasks.md`.

## Standards

| # | Temuan | Status | Perbaikan / alasan |
|---|---|---|---|
| S1 | CTA menampilkan nilai hasil merge, bukan nilai mentah; field kosong tidak terlihat kosong (melanggar konvensi README) | Fixed | `sgi_settings_raw()` untuk form, `sgi_header_cta()` untuk render. Smoke: `admin CTA menampilkan nilai mentah (kosong)`. |
| S2 | `GLOSSARY.md` masih menyebut Settings > Sirius; istilah baru belum ada | Fixed | Entri Menu/Konten Situs/Label navigasi/CTA header/Info kontak diperbarui. |
| S3 | Laporan review tidak tersimpan sebagai artefak | Fixed | File ini + mandat baru di `AGENTS.md`. |
| S4 | AGENTS mengunci "label nav utama", change membuatnya editable | Fixed | AGENTS dibedakan: teks label boleh diedit, struktur/urutan/URL tetap di Tampilan > Menu. |
| S5 | URL halaman dibangun ulang di 4 tempat | Fixed | Semua memakai `sgi_admin_page_url()`. |
| S6 | Default CTA tersebar di 3 tempat, fallback `??` mati | Fixed | Default hanya di `sgi_settings_defaults()`; header memakai `sgi_header_cta()`. |
| S7 | `ctaLabel`+`ctaHref` selalu berpasangan (data clump) | Fixed | Satu akses `sgi_header_cta()`. |
| S8 | `options.php` ~350 baris, beberapa alasan berubah | Fixed | Dipecah: `options.php` (registrasi/render skeleton), `options-fields.php` (field+panel), `options-save.php` (handler). |
| S9 | Daftar key settings ditulis dua kali | Fixed | Satu map `sgi_settings_fields()` untuk simpan + render. |
| S10 | `sgi_nav_menu_for_edit()` mengembalikan `menu_name` tak terpakai dan menyembunyikan objek di `raw` | Fixed | Kembalian `menu_id` + item `{id,title,url,parent,args}`. |
| S11 | Panel Kontak mengambil `sgi_settings()` sendiri, tidak konsisten dengan panel lain | Fixed | Panel menerima `array $s` seperti panel lain. |
| S12 | Warna hardcoded di `admin.css` | Partial (accepted) | Accent memakai `var(--wp-admin-theme-color, #2271b1)`. `#fff`/`#dcdcde` dipertahankan karena wp-admin core tidak menyediakan token surface/border; nilai itu sama dengan core. |

Smell baseline yang tersisa: tidak ada yang material. `sgi_opt_input/area`
masih berupa helper render kecil, bukan duplikasi logika.

## Spec

| # | Temuan | Status | Perbaikan |
|---|---|---|---|
| P1 | Handler simpan/tab tanpa cakupan tes | Fixed | Smoke menambah render 4 panel + semua field lintas tab, placeholder, duplicate id, email invalid, dan simpan sukses (settings/home/kontak, key lain dipertahankan). |
| P2 | Placeholder CTA yang diminta task tidak ada | Fixed | `sgi_opt_input()` mendukung placeholder; CTA memakai `Konsultasi Gratis` dan `/kontak/`. |
| P3 | Komentar masih "Settings > Sirius" | Fixed | `front-page.php`, `page-kontak.php`, `inc/helpers.php`, `tools/sgi-importer.php`. |
| P4 | Perubahan seed (mengisi menu kosong) di luar spec | Fixed (documented) | Spec menambah scenario "Menu terpasang tetapi kosong" dan "Menu bawaan gagal dibuat"; design mencatat alasannya. |
| P5 | Import ulang menghapus `ctaLabel`/`ctaHref` | Fixed | `tools/sgi-importer.php` memakai `sgi_fill_missing_keys()`; smoke `importer hanya mengisi key settings yang belum ada`. |
| P6 | Seed selalu melaporkan sukses | Fixed | `sgi_seed_primary_menu(): bool`; handler memilih `seeded=1` atau `sgi_err=seed`. |
| P7 | Kegagalan rename nav tetap "Tersimpan" | Fixed | `sgi_save_nav_labels()` mengembalikan `saved`/`failed`; handler menampilkan `sgi_err=nav`. |
| P8 | Link tab tanpa JS membuang isian | Fixed | Tab menjadi `<button>`; JS menambahkan role/aria; `<noscript>` menyembunyikan bar tab dan semua panel tampil. |
| P9 | CTA menampilkan nilai hasil merge | Fixed | Sama dengan S1. |

## Verifikasi

| Perintah / cek | Hasil |
|---|---|
| `php -l` seluruh PHP theme + importer | 0 error |
| `php tools/sgi-smoke.php` | 31 PASS, 0 FAIL |
| Render harness (stub WP) | 4 panel, 4 tab, 57 field per tab; `?tab=bogus` jatuh ke header; tanpa duplicate id |
| Headless 320/360/640/768/1280 | `overflow=no` di semua lebar |
| No-JS (`<noscript>`) | Bar tab tersembunyi, 4 panel tampil berurutan (screenshot) |
| JS aktif | `roleTabs=4`, 1 panel aktif, tab bar terlihat |
| Kontras | `#2271b1` vs `#f0f0f1` 4.54:1; vs `#dcdcde` 3.77:1; `#646970` vs `#f0f0f1` 4.86:1 |
| `git diff --check` | bersih |
| Keyboard tab (harness) | Dari tab Hero, `ArrowRight` → fokus + `aria-selected` pindah ke `sgi-tab-sections`, 1 panel terlihat |
| Tinggi tab | 34px (native `.nav-tab` wp-admin; di atas minimum 24px, di bawah target 36px karena mengikuti core) |

## Delta review UI admin (setelah remediasi)

Scope: halaman Tampilan > Konten Situs yang berubah (tab tombol + `<noscript>`,
placeholder/raw CTA, tombol seed, notice error) dan CTA header (helper, output
sama). Review penuh `better-interface` sebelumnya: Approve, 0 HIGH. Delta ini
di-cek ulang terhadap accessibility/layout/color/copy; tidak ada temuan baru.

- **Keyboard:** tab dapat dicapai, panah/Home/End memindahkan fokus + seleksi,
  Space mengaktifkan; fokus memakai outline 2px `var(--wp-admin-theme-color)`.
- **Tanpa JS:** tidak ada kontrol tab yang bisa membuang isian; bar tab
  disembunyikan `<noscript>` dan semua panel tetap dapat diedit.
- **Copy:** error menyebut jalan keluar (Tampilan > Menu), tanpa em-dash/en-dash,
  satu accent (warna WP).
- **Mobile/zoom:** 320-1280 tanpa overflow; form satu kolom; tab 34px masih di
  atas minimum 24px (native wp-admin).
- Pre-flight `design-taste-frontend`: front-end publik tidak berubah komposisi;
  label CTA hanya menjadi dinamis. Tidak ada foto/hero/section baru, tidak ada
  em-dash, accent tetap tunggal. Tidak ada pelanggaran yang perlu dicatat.

## Verdict

- **Standards:** 11 fixed, 1 partial/accepted (surface color tanpa token WP).
- **Spec:** 9/9 fixed.
- **Temuan HIGH tersisa:** 0. **Status: Approve.**
- Temuan terberat sebelum remediasi: CTA menampilkan nilai merge (Standards) dan
  importer menghapus `ctaLabel`/`ctaHref` saat re-import (Spec); keduanya sudah
  diperbaiki dan diverifikasi.

## Catatan / penyimpangan yang disengaja

- Label navigasi kini dapat diedit dari Konten Situs; struktur, urutan, dan URL tetap milik Tampilan > Menu. Sudah dicatat di AGENTS dan GLOSSARY.
- Tanpa dark mode: wp-admin core berjalan light; komponen memakai warna WP native.
- UI admin memakai komponen wp-admin, bukan design system situs; front-end tidak berubah visual (label CTA hanya menjadi dinamis).
- `#fff`/`#dcdcde` pada save bar/border dipertahankan karena tidak ada custom property WP untuk surface/border.

## Carry-over pasca-deploy

1. Import ulang via Tools > Sirius Import: pastikan `ctaLabel`/`ctaHref` dan editan lain tidak berubah.
2. Curl 200: `/`, `/layanan/`, `/layanan/app-development/`, `/kontak/`, `/tentang/`, `/portofolio/`.
3. Rollback: revert commit theme, halaman kembali ke Pengaturan > Sirius, data editan tetap ada.
