<?php
// tools/sgi-smoke.php - Uji perilaku theme tanpa WordPress (stub WP minimal).
// Pakai: php tools/sgi-smoke.php  (exit 0 = semua lolos)
// Dijalankan CI (php-lint.yml, job php-smoke). TIDAK ikut deploy cPanel
// (.cpanel.yml hanya menyalin sgi-importer.php + verify-seed.py).
declare(strict_types=1);

// --- Stub WordPress: cukup untuk helpers + functions + render header. ---
function add_action(...$a): void {}
function add_theme_support(...$a): void {}
function register_nav_menus(...$a): void {}
$GLOBALS['sgi_smoke_options'] = [
  // Simulasi editan admin: hero dikosongkan (harus fallback), needs kustom.
  'sgi_kontak' => ['hero_h1' => '', 'needs' => ['kustom' => 'Layanan Kustom']],
];
function get_option($k, $d = []) {
  return array_key_exists($k, $GLOBALS['sgi_smoke_options']) ? $GLOBALS['sgi_smoke_options'][$k] : $d;
}
function get_theme_mod($k, $d = 0) { return $d; }
function home_url($p = '/') { return 'https://siriusglobal.id' . $p; }
function get_nav_menu_locations() { return []; }
function get_queried_object() { return null; }
function get_queried_object_id() { return 0; }
function bloginfo($s) { if ($s === 'charset') echo 'UTF-8'; }
function wp_head(): void {}
function wp_body_open(): void {}
function body_class() { echo 'class="home"'; }
function wp_get_attachment_image(...$a) { return ''; }
function esc_url($u) { return htmlspecialchars((string)$u, ENT_QUOTES, 'UTF-8'); }
function esc_html($t) { return htmlspecialchars((string)$t, ENT_QUOTES, 'UTF-8'); }
function esc_attr($t) { return htmlspecialchars((string)$t, ENT_QUOTES, 'UTF-8'); }
function sanitize_title($t) { return strtolower(trim((string)preg_replace('/[^a-z0-9]+/i', '-', (string)$t), '-')); }

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

// 2. Kontak: defaults + semantik merge (admin menang, kosong = bawaan).
$k = sgi_kontak();
$check('kontak hero kosong -> fallback bawaan', ($k['hero_h1'] ?? '') === sgi_kontak_defaults()['hero_h1']);
$check('kontak needs kustom admin menang', ($k['needs'] ?? []) === ['kustom' => 'Layanan Kustom']);
$check('kontak budgets/bawaan utuh', count($k['budgets'] ?? []) === 5 && count($k['times'] ?? []) === 3);

// 3. Parser pasangan "nilai | label".
$p = sgi_kontak_pairs("a | Label A\nb\n| tanpa nilai\nc | ");
$check('pairs buang baris tanpa nilai', $p === ['a' => 'Label A', 'b' => 'b', 'c' => 'c']);
$check('pairs roundtrip', sgi_kontak_pairs(sgi_kontak_pairs_text(['a' => 'Label A', 'b' => 'b'])) === ['a' => 'Label A', 'b' => 'b']);

// 4. Seed idempoten tanpa WP penuh.
sgi_seed_primary_menu();
$check('seed tanpa WP penuh tidak crash', true);

// 5. Render header: struktur nav desktop/mobile.
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

if ($fail) {
  echo 'GAGAL: ' . count($fail) . ' cek.' . PHP_EOL;
  exit(1);
}
echo 'OK: semua cek lolos.' . PHP_EOL;
