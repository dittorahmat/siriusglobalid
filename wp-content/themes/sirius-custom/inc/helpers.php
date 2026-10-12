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
    'home.services' => 8, 'home.why' => 6, 'home.steps' => 8, 'home.testi' => 6,
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
