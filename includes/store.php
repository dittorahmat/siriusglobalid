<?php
// store.php - fasad konten: bahasa, i18n, settings, halaman, escape.
// Template publik + admin HANYA memakai fungsi cms_* di sini, tidak
// menyentuh JSON langsung (sekat migrasi ke SQLite/MySQL kelak).
declare(strict_types=1);

require_once __DIR__ . '/store_json.php';
require_once __DIR__ . '/backup.php';

function cms_lang(): string {
  $l = $_GET['lang'] ?? $_COOKIE['sgi-lang'] ?? 'id';
  return $l === 'en' ? 'en' : 'id';
}

/** Ambil string bilingual: ['id'=>..,'en'=>..] atau string polos. Fallback EN->ID. */
function cms_b($v, string $lang = 'id'): string {
  if (is_array($v)) {
    $s = (string)($v[$lang] ?? $v['id'] ?? '');
    return $s;
  }
  return (string)$v;
}

/** i18n key/value: cari di i18n.{lang}.json -> fallback lang lain -> key itu sendiri. */
function cms_t(string $key, ?string $lang = null): string {
  static $cache = [];
  $lang = $lang ?? cms_lang();
  if (!isset($cache[$lang])) $cache[$lang] = cms_load('i18n.' . $lang);
  $keys = $cache[$lang]['keys'] ?? [];
  if (isset($keys[$key]) && $keys[$key] !== '') return (string)$keys[$key];
  $other = $lang === 'id' ? 'en' : 'id';
  if (!isset($cache[$other])) $cache[$other] = cms_load('i18n.' . $other);
  $keys2 = $cache[$other]['keys'] ?? [];
  if (isset($keys2[$key]) && $keys2[$key] !== '') return (string)$keys2[$key];
  return $key;
}

function cms_settings(): array {
  $s = cms_load('settings');
  if (empty($s)) {
    return [
      'name' => 'PT Sirius Global Indonesia', 'email' => 'sales@siriusglobal.id',
      'phone' => '+62 815-1048-1010', 'phoneHref' => 'https://wa.me/6281510481010',
      'address' => 'Jakarta, Indonesia', 'hours' => 'Senin-Jumat, 09.00-18.00 WIB',
      'mapEmbed' => 'https://www.google.com/maps?q=Jakarta,Indonesia&output=embed',
    ];
  }
  return $s;
}

function cms_page(string $domain): array {
  return cms_load($domain);
}

function cms_service(string $slug): array {
  return cms_load('service:' . $slug);
}

function cms_portfolio(): array {
  $p = cms_load('portfolio');
  return isset($p['items']) && is_array($p['items']) ? $p : ['items' => [], 'updated_at' => ''];
}

/** Escape teks polos. */
function cms_e($v, string $lang = 'id'): string {
  return htmlspecialchars(cms_b($v, $lang), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Escape atribut. */
function cms_a($v, string $lang = 'id'): string {
  return htmlspecialchars(cms_b($v, $lang), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Render field *_html: sanitasi allowlist ketat (a/span/br/strong/em/b/i/u/p).
 * Atribut yang diizinkan: href (http/https/mailto/tel/relatif), class, target, rel.
 */
function cms_html($v, string $lang = 'id'): string {
  $html = cms_b($v, $lang);
  if ($html === '') return '';
  $allowed = ['a' => ['href', 'class', 'target', 'rel'], 'span' => ['class'],
    'br' => [], 'strong' => [], 'em' => [], 'b' => [], 'i' => [], 'u' => [], 'p' => ['class']];
  $doc = new DOMDocument('1.0', 'UTF-8');
  libxml_use_internal_errors(true);
  $doc->loadHTML('<?xml encoding="utf-8"?><div>' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
  libxml_clear_errors();
  $out = '';
  $root = $doc->documentElement;
  if ($root) {
    foreach ($root->childNodes as $node) $out .= cms_sanitize_node($node, $allowed);
  }
  return $out;
}

function cms_sanitize_node(DOMNode $node, array $allowed): string {
  if ($node instanceof DOMText) {
    return htmlspecialchars($node->wholeText, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
  }
  if (!($node instanceof DOMElement)) return '';
  $tag = strtolower($node->tagName);
  if (!isset($allowed[$tag])) {
    $s = '';
    foreach ($node->childNodes as $c) $s .= cms_sanitize_node($c, $allowed);
    return $s;
  }
  $attrs = '';
  foreach ($allowed[$tag] as $a) {
    if (!$node->hasAttribute($a)) continue;
    $val = $node->getAttribute($a);
    if ($a === 'href') {
      if (!preg_match('~^(https?://|mailto:|tel:|/|#|[a-z0-9_\-\.]+\.(html|php)(\?[a-z0-9_\-=&%\.]*)?$)~i', trim($val))) continue;
    } else {
      $val = preg_replace('/[^a-zA-Z0-9\-_ ]/', '', $val);
    }
    $attrs .= ' ' . $a . '="' . htmlspecialchars($val, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '"';
  }
  $inner = '';
  foreach ($node->childNodes as $c) $inner .= cms_sanitize_node($c, $allowed);
  if ($tag === 'br') return '<br>';
  return '<' . $tag . $attrs . '>' . $inner . '</' . $tag . '>';
}
