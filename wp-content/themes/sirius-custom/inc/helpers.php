<?php
// sgi_id() - ambil sisi ID dari field bilingual seed {id,en} atau string biasa.
// Importer dan theme ID-only: sisi 'en' selalu diabaikan.
declare(strict_types=1);

function sgi_id($v): string {
  if (is_array($v)) return (string)($v['id'] ?? '');
  if (is_string($v)) return $v;
  return '';
}

function sgi_e($v): void {
  echo esc_html(sgi_id($v));
}

// Batas list (port dari cms_limits(), proteksi layout).
function sgi_limits(): array {
  return [
    'portfolio.items' => 24, 'service.faqs' => 20, 'service.tiers' => 6,
    'service.checklist' => 20, 'service.metrics' => 8, 'service.steps' => 8,
    'tentang.values' => 12, 'tentang.timeline' => 12, 'tentang.team' => 12,
    'home.clients' => 12, 'home.cases' => 6,
    'home.services' => 24, 'home.why' => 6, 'home.steps' => 8, 'home.testi' => 6,
  ];
}

function sgi_over_limit(string $key, array $items): ?string {
  $limits = sgi_limits();
  if (isset($limits[$key]) && count($items) > $limits[$key]) {
    return 'Daftar ' . $key . ' maksimal ' . $limits[$key] . ' item (kini ' . count($items) . ').';
  }
  return null;
}

// Kunci section homepage yang valid (urutan default = urutan array).
function sgi_home_section_keys(): array {
  return ['hero', 'stats', 'layanan', 'why', 'alur', 'cases', 'testi', 'cta'];
}

// Normalisasi urutan section dari input admin (array atau string koma/spasi).
// Nama tak dikenal dibuang, duplikat dibuang, kosong = urutan default.
function sgi_home_order($raw): array {
  $def = sgi_home_section_keys();
  $list = is_array($raw) ? $raw : preg_split('/[\s,]+/', (string)$raw);
  $out = [];
  foreach ((array)$list as $s) {
    $s = strtolower(trim((string)$s));
    if (in_array($s, $def, true) && !in_array($s, $out, true)) $out[] = $s;
  }
  return $out ?: $def;
}

// Parse textarea "kolom1 | kolom2 | ..." per baris jadi list array.
// Baris kosong dibuang. Selalu kembalikan tepat $cols kolom per baris.
function sgi_pair_lines($raw, int $cols = 2): array {
  $out = [];
  foreach (preg_split('/\r?\n/', (string)$raw) as $ln) {
    $ln = trim($ln);
    if ($ln === '') continue;
    $parts = array_map('trim', explode('|', $ln));
    $parts = array_slice(array_merge($parts, array_fill(0, $cols, '')), 0, $cols);
    if (implode('', $parts) === '') continue;
    $out[] = $parts;
  }
  return $out;
}

// Menu utama: isi bawaan Beranda, Layanan, Kontak. Dipakai sebagai fallback
// saat lokasi Menu 'primary' belum diisi + sebagai seed awal.
function sgi_default_nav(): array {
  if (!function_exists('home_url')) return [];
  return [
    ['href' => home_url('/'), 'label' => 'Beranda', 'key' => 'home'],
    ['href' => home_url('/layanan/'), 'label' => 'Layanan', 'key' => 'layanan'],
    ['href' => home_url('/kontak/'), 'label' => 'Kontak', 'key' => 'kontak'],
  ];
}

// Menu utama dari Tampilan > Menu (lokasi 'primary'), dirender flat sebagai
// <a> langsung agar cocok dengan CSS .main-nav a / .mobile-menu a.
// Hanya item level atas yang dipakai (dropdown di luar scope ronde ini).
// Fallback ke sgi_default_nav() bila Menu kosong agar header tak bolong.
function sgi_primary_nav(): array {
  $fallback = sgi_default_nav();
  if (!function_exists('get_nav_menu_locations') || !function_exists('wp_get_nav_menu_items')) {
    return $fallback;
  }
  $locs = get_nav_menu_locations();
  if (empty($locs['primary'])) return $fallback;
  $items = wp_get_nav_menu_items($locs['primary']);
  if (!is_array($items) || !$items) return $fallback;
  $out = [];
  foreach ($items as $it) {
    if (!isset($it->menu_item_parent) || (int)$it->menu_item_parent !== 0) continue;
    $href = isset($it->url) ? (string)$it->url : '';
    $label = isset($it->title) ? trim((string)$it->title) : '';
    if ($href === '' || $label === '') continue;
    $current = false;
    if (function_exists('get_queried_object_id') && isset($it->object_id)) {
      $current = (int)$it->object_id === (int)get_queried_object_id();
    }
    $out[] = ['href' => $href, 'label' => $label, 'key' => sanitize_title($label), 'current' => $current];
  }
  return $out ?: $fallback;
}

// Isi hanya kunci yang belum ada. Dipakai importer agar re-import tidak
// menimpa editan admin (termasuk ctaLabel/ctaHref).
function sgi_fill_missing_keys(array $current, array $seed): array {
  foreach ($seed as $key => $value) {
    if (!array_key_exists($key, $current)) $current[$key] = $value;
  }
  return $current;
}

// Buat Menu "Navigasi utama" berisi 3 item bawaan bila lokasi 'primary'
// belum punya Menu. Idempoten: tidak menimpa Menu editor yang sudah ada.
// Mengembalikan true bila menu berisi item (baru dibuat atau sudah ada).
function sgi_seed_primary_menu(): bool {
  if (!function_exists('get_nav_menu_locations') || !function_exists('wp_create_nav_menu')) return false;
  $locs = get_nav_menu_locations();
  $menuId = 0;
  if (!empty($locs['primary'])) {
    $m = wp_get_nav_menu_object($locs['primary']);
    if ($m && !is_wp_error($m)) {
      $have = function_exists('wp_get_nav_menu_items') ? (array)wp_get_nav_menu_items($m->term_id) : [];
      if ($have) return true; // Menu editor sudah punya isi: jangan sentuh.
      $menuId = (int)$m->term_id; // Menu ada tapi kosong: isi dengan bawaan.
    }
  }
  if (!$menuId) {
    $existing = wp_get_nav_menu_object('Navigasi utama');
    if (!$existing || is_wp_error($existing)) {
      $created = wp_create_nav_menu('Navigasi utama');
      if (is_wp_error($created) || !$created) return false;
      $menuId = (int)$created;
    } else {
      $menuId = (int)$existing->term_id;
    }
  }
  $have = function_exists('wp_get_nav_menu_items') ? (array)wp_get_nav_menu_items($menuId) : [];
  if ($have) {
    $locs['primary'] = $menuId;
    set_theme_mod('nav_menu_locations', $locs);
    return true;
  }
  if (!function_exists('home_url')) return false;
  $items = [
    ['Beranda', home_url('/')],
    ['Layanan', home_url('/layanan/')],
    ['Kontak', home_url('/kontak/')],
  ];
  $ok = true;
  foreach ($items as $i => $it) {
    $res = wp_update_nav_menu_item($menuId, 0, [
      'menu-item-title' => $it[0],
      'menu-item-url' => $it[1],
      'menu-item-status' => 'publish',
      'menu-item-position' => $i + 1,
    ]);
    if (is_wp_error($res) || (int)$res <= 0) $ok = false;
  }
  $locs = get_nav_menu_locations();
  $locs['primary'] = $menuId;
  set_theme_mod('nav_menu_locations', $locs);
  return $ok;
}

// Slug layanan yang dikenal (port CMS_SERVICE_SLUGS).
function sgi_service_slugs(): array {
  return [
    'app-development', 'ai-solution', 'iot', 'odoo-erp', 'fleet-management',
    'infrastruktur', 'dashboard-bi', 'cybersecurity', 'training',
  ];
}

function sgi_portfolio_cats(): array {
  return ['erp', 'ai', 'fleet', 'app', 'iot', 'infra', 'sec', 'bi'];
}

// Halaman Kontak: nilai bawaan = nilai hardcode template saat ini.
// Option `sgi_kontak` menimpa bila diisi via Tampilan > Konten Situs; kosong = fallback.
function sgi_kontak_defaults(): array {
  return [
    'hero_h1' => 'Ceritakan kebutuhan Anda, kami balas < 1 hari kerja',
    'form_t' => 'Formulir konsultasi',
    'form_d' => 'Isi detail kebutuhan. Pesan Anda terkirim sebagai chat WhatsApp terstruktur ke tim kami.',
    'needs' => [
      'app-development' => 'Application Development',
      'ai-solution' => 'AI Implementation',
      'iot' => 'IoT Solutions',
      'odoo-erp' => 'ERP Odoo',
      'fleet-management' => 'Fleet Management',
      'infrastruktur' => 'Network dan Infrastruktur',
      'dashboard-bi' => 'Dashboard dan BI',
      'cybersecurity' => 'Cybersecurity',
      'training' => 'Corporate Training',
      'other' => 'Lainnya / kombinasi',
    ],
    'budgets' => [
      'lt50' => 'Di bawah Rp 50 jt',
      '50-150' => 'Rp 50-150 jt',
      '150-500' => 'Rp 150-500 jt',
      'gt500' => 'Di atas Rp 500 jt',
      'unknown' => 'Belum tahu, minta arahan',
    ],
    'times' => [
      'urgent' => 'Mendesak, di bawah 1 bulan',
      '1-3mo' => '1-3 bulan',
      'explore' => 'Masih eksplorasi',
    ],
  ];
}

function sgi_kontak(): array {
  $def = sgi_kontak_defaults();
  $saved = function_exists('get_option') ? (array)get_option('sgi_kontak', []) : [];
  foreach (['hero_h1', 'form_t', 'form_d'] as $k) {
    if (isset($saved[$k]) && trim((string)$saved[$k]) !== '') {
      $def[$k] = (string)$saved[$k];
    }
  }
  foreach (['needs', 'budgets', 'times'] as $k) {
    if (isset($saved[$k]) && is_array($saved[$k]) && $saved[$k]) {
      $clean = [];
      foreach ($saved[$k] as $v => $label) {
        $v = trim((string)$v);
        $label = trim((string)$label);
        if ($v === '') continue;
        $clean[$v] = $label !== '' ? $label : $v;
      }
      if ($clean) $def[$k] = $clean;
    }
  }
  return $def;
}

// Parse textarea "nilai | label" per baris jadi assoc. Baris tanpa nilai dibuang,
// label kosong = nilai. Maks 20 item.
function sgi_kontak_pairs($raw): array {
  $out = [];
  foreach (preg_split('/\r?\n/', (string)$raw) as $ln) {
    $ln = trim($ln);
    if ($ln === '') continue;
    $parts = array_map('trim', explode('|', $ln, 2));
    $v = $parts[0] ?? '';
    if ($v === '') continue;
    $out[$v] = ($parts[1] ?? '') !== '' ? $parts[1] : $v;
    if (count($out) >= 20) break;
  }
  return $out;
}

function sgi_kontak_pairs_text(array $pairs): string {
  $out = [];
  foreach ($pairs as $v => $label) {
    $out[] = $v . (($label !== '' && $label !== $v) ? ' | ' . $label : '');
  }
  return implode("\n", $out);
}
