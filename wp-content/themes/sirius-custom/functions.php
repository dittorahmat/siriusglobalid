<?php
// functions.php - bootstrap theme Sirius Custom (ID-only).
declare(strict_types=1);

require_once __DIR__ . '/inc/helpers.php';
require_once __DIR__ . '/inc/cpt.php';
require_once __DIR__ . '/inc/meta.php';
require_once __DIR__ . '/inc/options.php';
require_once __DIR__ . '/inc/redirects.php';
require_once __DIR__ . '/inc/import-page.php';

function sgi_setup(): void {
  add_theme_support('title-tag');
  add_theme_support('post-thumbnails');
  register_nav_menus(['primary' => 'Navigasi utama']);
}
add_action('after_setup_theme', 'sgi_setup');

function sgi_assets(): void {
  $uri = get_stylesheet_directory_uri();
  $dir = get_stylesheet_directory();
  $ver = wp_get_theme()->get('Version');
  foreach (['tokens', 'base', 'components', 'pages'] as $css) {
    $rel = 'assets/css/' . $css . '.css';
    wp_enqueue_style('sgi-' . $css, $uri . '/' . $rel, [], file_exists($dir . '/' . $rel) ? (string)filemtime($dir . '/' . $rel) : $ver);
  }
  wp_enqueue_script('sgi-main', $uri . '/assets/js/main.js', [], file_exists($dir . '/assets/js/main.js') ? (string)filemtime($dir . '/assets/js/main.js') : $ver, true);
  $settings = get_option('sgi_settings', []);
  wp_localize_script('sgi-main', 'SGI_SETTINGS', [
    'phoneHref' => (string)($settings['phoneHref'] ?? 'https://wa.me/6281510481010'),
  ]);
}
add_action('wp_enqueue_scripts', 'sgi_assets');

// Opsi site: settings + home (diisi importer, bisa diubah via Settings > Sirius).
function sgi_settings(): array {
  $defaults = [
    'name' => 'PT Sirius Global Indonesia', 'email' => 'sales@siriusglobal.id',
    'phone' => '+62 815-1048-1010', 'phoneHref' => 'https://wa.me/6281510481010',
    'address' => 'Gedung Wirausaha, Jl. HR Rasuna Said Kav. C5, Kuningan, Jakarta Selatan 12920',
    'hours' => 'Senin-Jumat, 09.00-18.00 WIB',
    'mapEmbed' => 'https://www.google.com/maps?q=Gedung+Wirausaha+HR+Rasuna+Said+Kav+C5+Jakarta+Selatan&output=embed',
  ];
  return array_merge($defaults, (array)get_option('sgi_settings', []));
}

function sgi_home(): array {
  $defaults = [
    'hero_photo' => ['src' => 'https://picsum.photos/seed/sirius-engineering-team/880/1000', 'alt' => 'Tim engineer Sirius Global Indonesia mengerjakan proyek di kantor', 'w' => 880, 'h' => 1000],
    'kpi_counts' => ['18', '9', '40'],
    'stat_counts' => ['120', '40', '9', '60'],
    'clients' => ['Manufaktur', 'Logistik dan Distribusi', 'Pertambangan', 'Ritel', 'Pemerintahan', 'Agribisnis'],
    'case_imgs' => [
      ['img' => 'https://picsum.photos/seed/odoo-erp-factory/640/400', 'alt' => 'Pabrik yang memakai ERP Odoo untuk inventaris'],
      ['img' => 'https://picsum.photos/seed/ai-vision-warehouse/640/400', 'alt' => 'Inspeksi kemasan otomatis dengan computer vision di gudang'],
      ['img' => 'https://picsum.photos/seed/fleet-trucks-highway/640/400', 'alt' => 'Armada truk distribusi yang terpantau GPS'],
    ],
  ];
  $saved = (array)get_option('sgi_home', []);
  return array_merge($defaults, $saved);
}
