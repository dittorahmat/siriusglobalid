# Code Review: 2026-11-03 Adapt Breadcrumbs and Template Labels to WordPress Site Language

**Commit:** [`a3eea21`](https://github.com/dittorahmat/siriusglobalid/commit/a3eea21)  
**Fixed Point:** `HEAD~1...HEAD`  
**Scope:** `wp-content/themes/sirius-custom/` (`404.php`, `archive-layanan.php`, `header.php`, `inc/helpers.php`, `inc/i18n.php`, `page-portofolio.php`, `page-tentang.php`, `page.php`, `single-layanan.php`)

---

## Standards

### (a) Documented Standards Violations
1. **Fallback Teks & Opsi Situs (Hard Violation)**
   * **Rule:** `AGENTS.md` (Poin 10 - Fallback teks & opsi situs): *"setiap teks/copy dinamis yang dibaca dari opsi situs (`sgi_home`, `sgi_settings`, `sgi_kontak`) WAJIB memiliki fallback nilai bawaan yang valid saat di-render ke markup. Jangan biarkan elemen tag judul atau tombol menghasilkan tag kosong jika admin mengosongkan input di panel."*
   * **Location:** `archive-layanan.php:16-21`
   ```php
   $eye = !empty($h['archive_svc_eye']) ? $h['archive_svc_eye'] : sgi_t('common.services', 'Layanan');
   if (sgi_is_en() && strtolower(trim((string)$h['archive_svc_eye'])) === 'layanan') {
     $eye = sgi_t('common.services', 'Services');
   }
   ```
   * **Issue:** Mengakses `$h['archive_svc_eye']` tanpa fallback/coalescing aman `(string)($h['archive_svc_eye'] ?? '')` sebelum evaluasi `trim()`. Jika key tidak ada atau bernilai null, memicu notice/warning pada strict PHP.

2. **YAGNI / Ad-hoc String Transformation (Hard / Design Standard Violation)**
   * **Rule:** `AGENTS.md` (Stack & YAGNI): *"Antarmuka tema mendukung bilingual otomatis (ID/EN) mengikuti Site Language WordPress via inc/i18n.php dan get_locale()"* & *"Solusi terkecil yang memenuhi acceptance... Jangan tambah abstraksi/check tanpa pemakai nyata."*
   * **Location:** `archive-layanan.php:17-19`
   * **Issue:** Melakukan ad-hoc hardcoded string interception (`strtolower(trim((string)...)) === 'layanan'`) langsung di template arsip untuk override opsi database ke bahasa Inggris daripada diselesaikan secara deklaratif melalui helper `sgi_t()` atau fallback model data.

### (b) Baseline Smells (Judgement Calls)
1. **Duplicated Code / Repeated Switches (Inconsistent i18n Strategy)**
   * **Hunk:** `404.php:8-9` vs `inc/i18n.php` dictionary:
   ```php
   <h1><?php echo esc_html(sgi_is_en() ? 'Page Not Found' : 'Halaman tidak ditemukan'); ?></h1>
   <p class="lead" style="margin-top:12px"><?php echo esc_html(sgi_is_en() ? 'The page you are looking for has been moved or removed. Please return home or contact us.' : 'Tautan yang Anda buka...'); ?></p>
   ```
   * **Smell:** Di sebagian besar template, alur translasi memakai dictionary-driven lookup `sgi_t('common.xxx', '...')`. Namun di `404.php`, muncul percabangan ternary langsung `sgi_is_en() ? ... : ...` dengan hardcoded string di markup. Hal ini menduplikasi logika i18n dan melemahkan sentralisasi kamus `inc/i18n.php`.

2. **Feature Envy**
   * **Hunk:** `archive-layanan.php:16-21`
   * **Smell:** Template melakukan inspeksi mendalam terhadap state locale dan isi spesifik field admin (`sgi_is_en() && strtolower(...) === 'layanan'`). Logika normalisasi fallback teks multibahasa seharusnya diurus oleh helper/i18n layer, bukan dieksekusi langsung di file view/template.

---

## Spec

### (a) Missing or Partial Requirements
* **Spec line**: `"ini ngubah yang ada di screenshot ini di mana? gimana caranya ?"` & `"terus untuk screenshot ini , kenapa tulisannya Beranda / Layanan ? padahal site language nya udah English. ini settingan i18n nya ga jalan?"`
  * **Partial**: Breadcrumb pada halaman `archive-layanan.php`, `single-layanan.php`, `page-portofolio.php`, `page-tentang.php`, dan `page.php` telah disesuaikan dengan `sgi_t('common.home')` / `sgi_t('common.services')`. Namun breadcrumb pada template lain seperti `page-kontak.php` (`contact.home` / `contact.breadcrumb`) masih menggunakan helper terpisah yang mungkin tidak seragam.

### (b) Scope Creep (Unasked Behaviour)
* **Spec line**: `"ya update agar sesuai dengan Site Language"` (dalam konteks pertanyaan breadcrumb `"kenapa tulisannya Beranda / Layanan ? padahal site language nya udah English"`).
  * **Scope creep**:
    1. Perubahan string 404 (`wp-content/themes/sirius-custom/404.php`): judul `"Page Not Found"`, lead text, dan tombol CTA `"Back to Home"` / `"Contact Us"`.
    2. Perubahan tombol CTA kartu layanan (`wp-content/themes/sirius-custom/archive-layanan.php`): `"Pelajari detail"` -> `common.learn_more` (`"Learn more"`).
    3. Perubahan tombol CTA single layanan (`wp-content/themes/sirius-custom/single-layanan.php`): `"Konsultasi Gratis"` -> `common.consultation_free` (`"Free Consultation"`).
    4. Perubahan skip link accessibility di header (`wp-content/themes/sirius-custom/header.php`): `"Lewati ke konten"` -> `common.skip_to_content` (`"Skip to content"`).

### (c) Implemented but Looks Wrong / Flawed
* **Spec line**: `"ya update agar sesuai dengan Site Language"`
  * **Archive Service Eyebrow override logic (`archive-layanan.php:16-21`)**:
    Implementasi melakukan hardcoded string matching `strtolower(trim((string)$h['archive_svc_eye'])) === 'layanan'` untuk meng-override opsi kustom admin. Jika admin menulis dengan spasi tambahan atau teks kustom lain, override ini rapuh dan mencampur konten dynamic theme options dengan static translation logic.
  * **Inline translation ternary di 404.php**: `404.php` menggunakan ternary ad-hoc `sgi_is_en() ? ... : ...` langsung di template daripada mendaftarkannya ke dictionary `sgi_t()` di `inc/i18n.php`.

---

## Status Perbaikan Temuan
1. **[SOLVED] Fallback Teks & Opsi Situs + Ad-hoc String Transformation (`archive-layanan.php`)**:
   - Logika string matching ad-hoc telah diganti dengan pengecekan aman bertipe `trim((string)($h['archive_svc_eye'] ?? ''))`.
   - Menggunakan `strcasecmp` aman terhadap nilai bawaan, dan mengembalikan `sgi_t('common.services', 'Layanan')` sebagai nilai standar bilingual terpusat jika opsi admin bernilai default/kosong.
2. **[SOLVED] Inline Ternary di `404.php`**:
   - String `error.404_title` dan `error.404_desc` telah dipindahkan dan didaftarkan ke dictionary terpusat di `inc/i18n.php`.
   - `404.php` kini bersih dari ternary `sgi_is_en()` dan sepenuhnya menggunakan `sgi_t()`.
3. **[SOLVED] Konsistensi Breadcrumb Kontak (`page-kontak.php`)**:
   - Breadcrumb dan eyebrow kontak kini seragam memakai `sgi_t('common.home')` dan `sgi_t('common.contact')`.
4. **[SOLVED] Guardrail `AGENTS.md`**:
   - Menambahkan aturan Poin 14 pada `AGENTS.md` yang mewajibkan i18n terpusat via `sgi_t()`, melarang ternary inline di markup, dan mewajibkan grouping `common.*` untuk elemen global.

## Verifikasi
- `php tools/sgi-static-check.php`: **PASS** (13 aturan mekanis lolos).
- `git ls-files "*.php" | ForEach-Object { php -l $_ }`: **PASS** (semua file valid sintaks PHP).
