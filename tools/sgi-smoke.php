<?php
// tools/sgi-smoke.php - Uji perilaku theme tanpa WordPress (stub WP minimal).
// Pakai: php tools/sgi-smoke.php  (exit 0 = semua lolos)
// Dijalankan CI (php-lint.yml, job php-smoke). TIDAK ikut deploy cPanel
// (.cpanel.yml hanya menyalin sgi-importer.php + verify-seed.py).
declare(strict_types=1);

// --- Stub WordPress: dipakai bersama harness UI (tools/wp-stubs.php). ---
require __DIR__ . '/wp-stubs.php';
$GLOBALS['sgi_smoke_options'] = [
  // Simulasi editan admin: hero dikosongkan (harus fallback), needs kustom.
  'sgi_kontak' => ['hero_h1' => '', 'needs' => ['kustom' => 'Layanan Kustom']],
];

require dirname(__DIR__) . '/wp-content/themes/sirius-custom/functions.php';

$fail = [];
$check = function (string $name, bool $cond) use (&$fail): void {
  echo ($cond ? 'PASS' : 'FAIL') . ' ' . $name . PHP_EOL;
  if (!$cond) $fail[] = $name;
};

// 1. Menu: fallback 3 item saat lokasi primary kosong.
$nav = sgi_primary_nav();
$check('nav fallback 3 item', count($nav) === 3);
$check('nav label Beranda/Layanan/Kontak',
  ($nav[0]['label'] ?? '') === 'Beranda' && ($nav[1]['label'] ?? '') === 'Layanan' && ($nav[2]['label'] ?? '') === 'Kontak');
$check('nav href benar',
  ($nav[0]['href'] ?? '') === 'https://siriusglobal.id/'
  && ($nav[1]['href'] ?? '') === 'https://siriusglobal.id/layanan/'
  && ($nav[2]['href'] ?? '') === 'https://siriusglobal.id/kontak/');

// 2. Settings: default CTA header + semantics "kosong = bawaan".
$set = sgi_settings();
$check('settings default ctaLabel/ctaHref',
  ($set['ctaLabel'] ?? '') === 'Konsultasi Gratis' && ($set['ctaHref'] ?? '') === '/kontak/');
$GLOBALS['sgi_smoke_options']['sgi_settings'] = ['ctaLabel' => '', 'ctaHref' => ''];
$setEmpty = sgi_settings();
$check('cta kosong -> bawaan',
  ($setEmpty['ctaLabel'] ?? '') === 'Konsultasi Gratis' && ($setEmpty['ctaHref'] ?? '') === '/kontak/');
unset($GLOBALS['sgi_smoke_options']['sgi_settings']);

// 2b. Home & Archive: hero fallback saat opsi dikosongkan.
$hInit = sgi_home();
$check('sgi_home bawaan punya archive_svc_title_html',
  !empty($hInit['archive_svc_title_html']) && str_contains($hInit['archive_svc_title_html'], 'Sembilan kapabilitas'));
$GLOBALS['sgi_smoke_options']['sgi_home'] = ['archive_svc_title_html' => ''];
$hEmpty = sgi_home();
$check('archive hero kosong tetap tersimpan sebagai string kosong (view punya fallback)',
  array_key_exists('archive_svc_title_html', $hEmpty));
unset($GLOBALS['sgi_smoke_options']['sgi_home']);

// 3. Rename nav: hanya judul yang berubah, atribut lain disalin apa adanya.
$fakeItem = (object)[
  'ID' => 42, 'object_id' => 7, 'object' => 'page', 'menu_item_parent' => 0,
  'menu_order' => 2, 'type' => 'post_type', 'title' => 'Beranda',
  'url' => 'https://siriusglobal.id/', 'description' => '', 'attr_title' => '',
  'target' => '_blank', 'classes' => ['menu-item', 'custom-class'], 'xfn' => '',
  'post_status' => 'publish',
];
$args = sgi_nav_item_update_args($fakeItem, 'Home');
$check('nav rename hanya ganti judul',
  $args['menu-item-title'] === 'Home'
  && $args['menu-item-url'] === 'https://siriusglobal.id/'
  && $args['menu-item-target'] === '_blank'
  && $args['menu-item-classes'] === 'menu-item custom-class'
  && $args['menu-item-position'] === 2);

// 4. Kontak: defaults + semantik merge (admin menang, kosong = bawaan).
$k = sgi_kontak();
$check('kontak hero kosong -> fallback bawaan', ($k['hero_h1'] ?? '') === sgi_kontak_defaults()['hero_h1']);
$check('kontak needs kustom admin menang', ($k['needs'] ?? []) === ['kustom' => 'Layanan Kustom']);
$check('kontak budgets/bawaan utuh', count($k['budgets'] ?? []) === 5 && count($k['times'] ?? []) === 3);

// 5. Parser pasangan "nilai | label".
$p = sgi_kontak_pairs("a | Label A\nb\n| tanpa nilai\nc | ");
$check('pairs buang baris tanpa nilai', $p === ['a' => 'Label A', 'b' => 'b', 'c' => 'c']);
$check('pairs roundtrip', sgi_kontak_pairs(sgi_kontak_pairs_text(['a' => 'Label A', 'b' => 'b'])) === ['a' => 'Label A', 'b' => 'b']);

// 6. Seed idempoten tanpa WP penuh.
sgi_seed_primary_menu();
$check('seed tanpa WP penuh tidak crash', true);

// 7. Render header: struktur nav desktop/mobile.
ob_start();
require dirname(__DIR__) . '/wp-content/themes/sirius-custom/header.php';
$html = ob_get_clean();
$html = substr($html, 0, (int)strpos($html, '</header>') + 10);
preg_match('#<nav class="main-nav".*?</nav>#s', $html, $m);
$desktop = $m[0] ?? '';
$check('desktop 3 link tanpa Tentang/Portofolio',
  substr_count($desktop, '<a href=') === 3 && !str_contains($desktop, 'Tentang') && !str_contains($desktop, 'Portofolio'));
preg_match('#<nav class="mobile-menu".*?</nav>#s', $html, $m2);
$mobile = $m2[0] ?? '';
$check('mobile 3 link + CTA Konsultasi Gratis',
  substr_count($mobile, '<a href=') === 4 && str_contains($mobile, 'Konsultasi Gratis'));
$check('meta viewport + skip-link + menu-btn', str_contains($html, 'name="viewport"')
  && str_contains($html, 'skip-link') && str_contains($html, 'aria-expanded="false"'));

// 8. CTA header mengikuti sgi_settings (label + tautan absolut apa adanya).
$GLOBALS['sgi_smoke_options']['sgi_settings'] = [
  'ctaLabel' => 'Diskusi Gratis',
  'ctaHref' => 'https://wa.me/628111',
];
ob_start();
require dirname(__DIR__) . '/wp-content/themes/sirius-custom/header.php';
$htmlCta = ob_get_clean();
$htmlCta = substr($htmlCta, 0, (int)strpos($htmlCta, '</header>') + 10);
$check('CTA header ikut setting',
  substr_count($htmlCta, 'Diskusi Gratis') === 2
  && substr_count($htmlCta, 'https://wa.me/628111') === 2
  && !str_contains($htmlCta, '>Konsultasi Gratis<'));

// 9. Settings: nilai mentah vs efektif, dan helper CTA header.
$GLOBALS['sgi_smoke_options']['sgi_settings'] = ['ctaLabel' => '', 'ctaHref' => ''];
$raw = sgi_settings_raw();
$check('settings raw kosong tetap kosong', ($raw['ctaLabel'] ?? 'x') === '' && ($raw['ctaHref'] ?? 'x') === '');
$check('settings baca kosong -> bawaan',
  sgi_settings()['ctaLabel'] === 'Konsultasi Gratis' && sgi_settings()['ctaHref'] === '/kontak/');
$ctaDefault = sgi_header_cta();
$check('helper CTA header menormalkan href',
  $ctaDefault['label'] === 'Konsultasi Gratis' && $ctaDefault['href'] === 'https://siriusglobal.id/kontak/');
$mergedSeed = sgi_fill_missing_keys(['name' => 'Edit', 'legacy' => 'keep'], ['name' => 'Seed', 'email' => 'a@b.co']);
$check('importer hanya mengisi key settings yang belum ada',
  ($mergedSeed['name'] ?? '') === 'Edit' && ($mergedSeed['email'] ?? '') === 'a@b.co' && ($mergedSeed['legacy'] ?? '') === 'keep');

// 10. Render halaman admin: semua panel + field tab lain ada di DOM.
$_GET['tab'] = 'header';
ob_start();
sgi_options_render();
$admin = ob_get_clean();
$check('admin 4 panel', substr_count($admin, 'class="sgi-panel"') === 4);
$required = ['sgi_ctaLabel', 'sgi_ctaHref', 'sgi_hero_title', 'sgi_why', 'sgi_k_needs', 'sgi_home_order', 'sgi_tab'];
$missing = [];
foreach ($required as $name) {
  if (!str_contains($admin, 'name="' . $name . '"')) $missing[] = $name;
}
$check('admin semua field lintas tab ada' . ($missing ? ' (' . implode(',', $missing) . ')' : ''), !$missing);
$check('admin tab berupa button tanpa role tablist dari server',
  substr_count($admin, 'data-sgi-tab=') === 4 && !str_contains($admin, 'role="tablist"'));
$check('admin placeholder CTA',
  str_contains($admin, 'placeholder="Konsultasi Gratis"') && str_contains($admin, 'placeholder="/kontak/"'));
$check('admin CTA menampilkan nilai mentah (kosong)',
  str_contains($admin, 'name="sgi_ctaLabel" value=""') && str_contains($admin, 'name="sgi_ctaHref" value=""'));
$adminIds = preg_match_all('/\sid="([^"]+)"/', $admin, $idm) ? $idm[1] : [];
$adminDupes = array_keys(array_filter(array_count_values($adminIds), function ($c) { return $c > 1; }));
$check('admin tanpa duplicate id', !$adminDupes);

// 11. Handler simpan: email tidak valid tidak mengubah option.
$GLOBALS['sgi_smoke_options']['sgi_settings'] = ['name' => 'Lama'];
$_POST = ['sgi_save' => '1', 'sgi_tab' => 'header', 'sgi_name' => 'Baru', 'sgi_email' => 'bukan-email'];
$redirect = '';
try { sgi_options_handle_save(); } catch (SgiRedirect $e) { $redirect = $e->url; }
$check('save email invalid ditolak tanpa ubah option',
  str_contains($redirect, 'sgi_err=email')
  && ($GLOBALS['sgi_smoke_options']['sgi_settings']['name'] ?? '') === 'Lama');

// 12. Handler simpan: satu submit menulis settings + home + kontak dan
// mempertahankan key yang tidak ikut di-post (mis. case_imgs, key settings lain).
$GLOBALS['sgi_smoke_options']['sgi_settings'] = ['name' => 'Lama', 'legacy' => 'keep', 'ctaLabel' => 'Lama CTA'];
$GLOBALS['sgi_smoke_options']['sgi_home'] = ['case_imgs' => [['img' => 'x']], 'hero' => ['eyebrow' => 'Lama']];
$GLOBALS['sgi_smoke_options']['sgi_kontak'] = ['hero_h1' => 'Lama'];
$_POST = [
  'sgi_save' => '1', 'sgi_tab' => 'sections', 'sgi_email' => 'a@b.co', 'sgi_name' => 'Baru',
  'sgi_ctaLabel' => 'Diskusi Gratis', 'sgi_ctaHref' => 'https://wa.me/1',
  'sgi_hero_eyebrow' => 'Eyebrow Baru', 'sgi_k_hero_h1' => 'Kontak Baru',
  'sgi_why' => 'Keunggulan | Isi', 'sgi_home_order' => 'hero,cta',
];
$redirect = '';
try { sgi_options_handle_save(); } catch (SgiRedirect $e) { $redirect = $e->url; }
$opt = $GLOBALS['sgi_smoke_options'];
$check('save menulis settings + mempertahankan key lain',
  ($opt['sgi_settings']['name'] ?? '') === 'Baru'
  && ($opt['sgi_settings']['ctaLabel'] ?? '') === 'Diskusi Gratis'
  && ($opt['sgi_settings']['legacy'] ?? '') === 'keep');
$check('save menulis home + mempertahankan case_imgs',
  ($opt['sgi_home']['hero']['eyebrow'] ?? '') === 'Eyebrow Baru' && isset($opt['sgi_home']['case_imgs']));
$check('save menulis kontak', ($opt['sgi_kontak']['hero_h1'] ?? '') === 'Kontak Baru');
$check('save redirect ke tab + updated', str_contains($redirect, 'tab=sections') && str_contains($redirect, 'updated=1'));

if ($fail) {
  echo 'GAGAL: ' . count($fail) . ' cek.' . PHP_EOL;
  exit(1);
}
echo 'OK: semua cek lolos.' . PHP_EOL;
