<?php
// inc/redirects.php - Redirect 301 URL lama (.html/.php) ke permalink WP.
// Peta statis untuk halaman inti; detail layanan ditangani dinamis via
// template_redirect (mendukung layanan yang bertambah/berkurang).
declare(strict_types=1);

function sgi_legacy_map(): array {
  return [
    'index' => '/', 'tentang' => '/tentang/', 'layanan' => '/layanan/',
    'portofolio' => '/portofolio/', 'kontak' => '/kontak/',
    'privasi' => '/privasi/', 'syarat' => '/syarat/',
  ];
}

function sgi_legacy_redirects(): void {
  $uri = strtok($_SERVER['REQUEST_URI'] ?? '/', '?') ?: '/';
  // /x.html atau /x.php -> peta statis.
  if (preg_match('#^/([a-z-]+)\.(html|php)$#', $uri, $m) && isset(sgi_legacy_map()[$m[1]])) {
    wp_redirect(home_url(sgi_legacy_map()[$m[1]]), 301);
    exit;
  }
  // /layanan/<slug>.html|php -> CPT layanan dinamis (slug apa pun).
  if (preg_match('#^/layanan/([a-z0-9-]+)\.(html|php)$#', $uri, $m)) {
    $post = get_page_by_path($m[1], OBJECT, 'layanan');
    if ($post) {
      wp_redirect(get_permalink($post), 301);
      exit;
    }
  }
}
add_action('template_redirect', 'sgi_legacy_redirects', 1);
