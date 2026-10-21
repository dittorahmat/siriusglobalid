<?php
// tools/wp-stubs.php - Stub WordPress minimal untuk smoke test + harness UI.
// Dipakai tools/sgi-smoke.php (CI) dan tools/sgi-admin-harness.php (lokal).
// Jangan dipakai di production; semua fungsi hanya meniru perilaku yang
// dibutuhkan theme tanpa memuat WordPress.
declare(strict_types=1);

$GLOBALS['sgi_smoke_options'] = $GLOBALS['sgi_smoke_options'] ?? [];

function add_action(...$a): void {}
function add_filter(...$a): void {}
function add_theme_support(...$a): void {}
function register_nav_menus(...$a): void {}
function register_post_type(...$a): void {}
function register_taxonomy(...$a): void {}

function get_option($k, $d = []) {
  return array_key_exists($k, $GLOBALS['sgi_smoke_options']) ? $GLOBALS['sgi_smoke_options'][$k] : $d;
}
function update_option($k, $v): bool {
  $GLOBALS['sgi_smoke_options'][$k] = $v;
  return true;
}
function get_theme_mod($k, $d = 0) { return $d; }
function set_theme_mod(...$a): void {}

function home_url($p = '/') { return 'https://siriusglobal.id' . $p; }
function admin_url($p = '') { return 'https://siriusglobal.id/wp-admin/' . $p; }
function get_stylesheet_directory_uri() { return 'https://siriusglobal.id/wp-content/themes/sirius-custom'; }
function get_stylesheet_directory() { return dirname(__DIR__) . '/wp-content/themes/sirius-custom'; }
function wp_get_theme() { return new class { public function get($k) { return '1.0'; } }; }
function get_plugin_page_hookname($a, $b) { return 'appearance_page_' . $a; }

function get_nav_menu_locations() { return []; }
function get_queried_object() { return null; }
function get_queried_object_id() { return 0; }
function bloginfo($s) { if ($s === 'charset') echo 'UTF-8'; }
function wp_head(): void {}
function wp_body_open(): void {}
function body_class() { echo 'class="home"'; }
function wp_get_attachment_image(...$a) { return ''; }
function wp_enqueue_style(...$a): void {}
function wp_enqueue_script(...$a): void {}
function wp_localize_script(...$a): void {}

function esc_url($u) { return htmlspecialchars((string)$u, ENT_QUOTES, 'UTF-8'); }
function esc_url_raw($u) { return (string)$u; }
function esc_html($t) { return htmlspecialchars((string)$t, ENT_QUOTES, 'UTF-8'); }
function esc_attr($t) { return htmlspecialchars((string)$t, ENT_QUOTES, 'UTF-8'); }
function esc_textarea($t) { return htmlspecialchars((string)$t, ENT_QUOTES, 'UTF-8'); }

function sanitize_title($t) { return strtolower(trim((string)preg_replace('/[^a-z0-9]+/i', '-', (string)$t), '-')); }
function sanitize_text_field($t) { return trim((string)preg_replace('/[\r\n\t]+/', ' ', wp_strip_all_tags_stub((string)$t))); }
function wp_strip_all_tags_stub($t) { return strip_tags($t); }
function sanitize_key($t) { return preg_replace('/[^a-z0-9_\-]/', '', strtolower((string)$t)); }
function wp_unslash($v) { return $v; }
function wp_kses($v, $a) { return (string)$v; }
function is_email($v) { return (bool)filter_var((string)$v, FILTER_VALIDATE_EMAIL); }
function current_user_can($c) { return true; }

function add_query_arg($args, $url = '') {
  $sep = str_contains($url, '?') ? '&' : '?';
  return $url . $sep . http_build_query((array)$args);
}
function wp_nonce_field($action = -1, $name = '_wpnonce', $referer = true, $echo = true) {
  $r = '<input type="hidden" name="' . $name . '" value="stub">';
  if ($referer) $r .= '<input type="hidden" name="_wp_http_referer" value="">';
  if ($echo) echo $r;
  return $r;
}
function checked($a, $b = true, $echo = true) {
  $r = ((string)$a === (string)$b) ? ' checked="checked"' : '';
  if ($echo) echo $r;
  return $r;
}
function check_admin_referer(...$a): void {}

class SgiRedirect extends Exception { public string $url = ''; }
class SgiWpDie extends Exception {}
function wp_safe_redirect($url): void { $e = new SgiRedirect('redirect'); $e->url = (string)$url; throw $e; }
function wp_die($msg = ''): void { throw new SgiWpDie(is_string($msg) ? $msg : 'die'); }
