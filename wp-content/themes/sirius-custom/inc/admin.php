<?php
// inc/admin.php - entry point & helper wp-admin untuk halaman Konten Situs.
// Halaman itu sendiri didaftarkan di inc/options.php (Tampilan > Konten Situs).
declare(strict_types=1);

// URL kanonis halaman pengaturan konten (slug 'sgi', parent themes.php).
function sgi_admin_page_url(array $args = []): string {
  $base = ['page' => 'sgi'];
  return add_query_arg(array_merge($base, $args), admin_url('themes.php'));
}

// Bookmark lama (Pengaturan > Sirius) diarahkan ke lokasi baru. Guard $pagenow
// + query agar tidak pernah menjadi loop.
function sgi_admin_legacy_redirect(): void {
  global $pagenow;
  if ($pagenow !== 'options-general.php') return;
  if (!isset($_GET['page']) || $_GET['page'] !== 'sgi') return;
  if (!current_user_can('edit_theme_options')) return;
  wp_safe_redirect(sgi_admin_page_url());
  exit;
}

// Filter theme_action_links hanya terpasang di admin, untuk theme aktif.
function sgi_admin_register_theme_link(): void {
  if (function_exists('get_stylesheet')) {
    add_filter('theme_action_links_' . get_stylesheet(), 'sgi_admin_theme_action_links');
  }
  add_filter('admin_body_class', 'sgi_admin_body_class');
}

function sgi_admin_theme_action_links($actions) {
  if (!is_array($actions)) return $actions;
  $actions['sgi_content'] = '<a href="' . esc_url(sgi_admin_page_url()) . '">Pengaturan konten</a>';
  return $actions;
}

// Kartu info di Tampilan > Tema.
function sgi_admin_themes_notice(): void {
  if (!current_user_can('edit_theme_options')) return;
  if (!function_exists('get_current_screen')) return;
  $screen = get_current_screen();
  if (!$screen || $screen->id !== 'themes') return;
  echo '<div class="notice notice-info"><p><strong>Sirius Custom</strong>. Ubah label navigasi, tombol header, dan isi beranda tanpa menyentuh kode.</p>'
    . '<p><a class="button button-primary" href="' . esc_url(sgi_admin_page_url()) . '">Buka pengaturan konten</a></p></div>';
}

// Node admin bar di situs publik.
function sgi_admin_bar_link($bar): void {
  if (!function_exists('is_admin_bar_showing') || !is_admin_bar_showing()) return;
  if (!current_user_can('edit_theme_options')) return;
  if (!is_object($bar) || !method_exists($bar, 'add_node')) return;
  $bar->add_node([
    'id' => 'sgi-content',
    'title' => 'Edit konten situs',
    'href' => sgi_admin_page_url(),
    'meta' => ['title' => 'Buka pengaturan konten situs'],
  ]);
}

// Aset hanya untuk halaman Konten Situs (hook suffix appearance_page_sgi).
function sgi_admin_assets(string $hook): void {
  $target = get_plugin_page_hookname('sgi', 'themes.php');
  if ($hook !== $target) return;
  $uri = get_stylesheet_directory_uri();
  $dir = get_stylesheet_directory();
  $ver = wp_get_theme()->get('Version');
  $css = '/assets/css/admin.css';
  $js = '/assets/js/admin.js';
  wp_enqueue_style('sgi-admin', $uri . $css, [], file_exists($dir . $css) ? (string)filemtime($dir . $css) : $ver);
  wp_enqueue_script('sgi-admin', $uri . $js, [], file_exists($dir . $js) ? (string)filemtime($dir . $js) : $ver, true);
}

// Item menu lokasi 'primary' untuk diedit judulnya. Mengembalikan struktur
// sederhana + argumen update lengkap per item (dipakai saat menyimpan).
// items kosong = lokasi belum diisi ATAU menu tanpa item (keduanya memakai
// fallback di front-end).
function sgi_nav_menu_for_edit(): array {
  $out = ['menu_id' => 0, 'items' => []];
  if (!function_exists('get_nav_menu_locations') || !function_exists('wp_get_nav_menu_items')) return $out;
  $locs = get_nav_menu_locations();
  $menuId = (int)($locs['primary'] ?? 0);
  if ($menuId <= 0) return $out;
  $items = wp_get_nav_menu_items($menuId);
  if (!is_array($items)) return $out;
  $out['menu_id'] = $menuId;
  foreach ($items as $it) {
    if (!is_object($it)) continue;
    $id = (int)($it->ID ?? 0);
    if ($id <= 0) continue;
    $out['items'][] = [
      'id' => $id,
      'title' => (string)($it->title ?? ''),
      'url' => (string)($it->url ?? ''),
      'parent' => (int)($it->menu_item_parent ?? 0),
      'args' => sgi_nav_item_update_args($it, (string)($it->title ?? '')),
    ];
  }
  return $out;
}

// Argumen lengkap untuk wp_update_nav_menu_item(). Judul adalah satu-satunya
// field yang diubah; url, target, classes, parent, dsb disalin dari item lama
// agar tidak terhapus oleh update parsial.
function sgi_nav_item_update_args($item, string $title): array {
  $get = function (string $key, $default = '') use ($item) {
    return is_object($item) && isset($item->{$key}) ? $item->{$key} : $default;
  };
  $classes = (array)($get('classes', []) ?: []);
  return [
    'menu-item-db-id' => (int)$get('ID', 0),
    'menu-item-object-id' => (int)$get('object_id', 0),
    'menu-item-object' => (string)$get('object', ''),
    'menu-item-parent-id' => (int)$get('menu_item_parent', 0),
    'menu-item-position' => max(1, (int)$get('menu_order', 1)),
    'menu-item-type' => (string)$get('type', 'custom'),
    'menu-item-title' => $title,
    'menu-item-url' => (string)$get('url', ''),
    'menu-item-description' => (string)$get('description', ''),
    'menu-item-attr-title' => (string)$get('attr_title', ''),
    'menu-item-target' => (string)$get('target', ''),
    'menu-item-classes' => implode(' ', array_map('strval', $classes)),
    'menu-item-xfn' => (string)$get('xfn', ''),
    'menu-item-status' => ((string)$get('post_status', 'publish') === 'publish') ? 'publish' : 'draft',
  ];
}

// Simpan judul item menu. Mengembalikan jumlah item yang berhasil dan gagal,
// agar handler bisa melaporkan kegagalan alih-alih selalu "Tersimpan".
function sgi_save_nav_labels(array $labels): array {
  $out = ['saved' => 0, 'failed' => 0];
  if (!function_exists('wp_update_nav_menu_item')) return $out;
  $menu = sgi_nav_menu_for_edit();
  if ((int)$menu['menu_id'] <= 0 || !$menu['items']) return $out;
  foreach ($menu['items'] as $it) {
    $id = (int)$it['id'];
    if (!array_key_exists($id, $labels)) continue;
    $title = sanitize_text_field((string)$labels[$id]);
    if ($title === '' || $title === (string)$it['title']) continue;
    $args = (array)$it['args'];
    $args['menu-item-title'] = $title;
    $res = wp_update_nav_menu_item((int)$menu['menu_id'], $id, $args);
    if (is_wp_error($res) || (int)$res <= 0) {
      $out['failed']++;
    } else {
      $out['saved']++;
    }
  }
  return $out;
}

// Class body khusus halaman Konten Situs (dipakai CSS untuk scroll-padding
// agar field terakhir tidak tertutup save bar sticky saat difokuskan).
function sgi_admin_body_class($classes) {
  if (!function_exists('get_current_screen')) return $classes;
  $screen = get_current_screen();
  if ($screen && $screen->id === 'appearance_page_sgi') $classes .= ' sgi-admin-page';
  return $classes;
}

add_action('admin_init', 'sgi_admin_legacy_redirect');
add_action('admin_init', 'sgi_admin_register_theme_link');
add_action('admin_notices', 'sgi_admin_themes_notice');
add_action('admin_bar_menu', 'sgi_admin_bar_link', 80);
add_action('admin_enqueue_scripts', 'sgi_admin_assets');
