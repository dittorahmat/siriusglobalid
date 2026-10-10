<?php
// tools/sgi-importer.php - Importer seed JSON (sisi ID) ke WordPress.
// Pakai: wp eval-file tools/sgi-importer.php --seed=data/seed
//   atau: wp eval "require 'tools/sgi-importer.php'; sgi_import_all('data/seed');"
// Menerima slug layanan APA PUN (terbuka). Idempoten: cocokkan by slug/nama,
// update bila hash konten berubah, lewati bila sama. Sisi 'en' diabaikan.
// TIDAK berisi kredensial apa pun.
//
// Kebutuhan: plugin aktif? Tidak. Cukup theme sirius-custom aktif (CPT terdaftar).
declare(strict_types=1);

function sgi_imp_id($v): string {
  if (is_array($v)) return trim((string)($v['id'] ?? ''));
  return trim((string)$v);
}

function sgi_imp_lines(array $items): string {
  $out = [];
  foreach ($items as $it) {
    $s = sgi_imp_id($it);
    if ($s !== '') $out[] = $s;
  }
  return implode("\n", $out);
}

function sgi_imp_pairs(array $items, string $lk, string $vk): string {
  $out = [];
  foreach ($items as $it) {
    if (!is_array($it)) continue;
    $l = sgi_imp_id($it[$lk] ?? '');
    $v = sgi_imp_id($it[$vk] ?? '');
    if ($l !== '' || $v !== '') $out[] = $l . ' | ' . $v;
  }
  return implode("\n", $out);
}

function sgi_imp_check(string $key, array $items, array $limits): ?string {
  if (isset($limits[$key]) && count($items) > $limits[$key]) {
    return "Daftar $key maksimal {$limits[$key]} item (kini " . count($items) . ').';
  }
  return null;
}

/** Upsert post by slug; kembalikan [id, changed]. Tidak pernah duplikat. */
function sgi_imp_upsert(string $type, string $slug, string $title, string $status = 'publish'): array {
  $existing = get_page_by_path($slug, OBJECT, $type);
  $hash = md5($title);
  if ($existing) {
    $oldHash = (string)get_post_meta($existing->ID, '_sgi_seed_hash', true);
    if ($oldHash === $hash && $existing->post_title === $title) {
      return [(int)$existing->ID, false];
    }
    wp_update_post(['ID' => $existing->ID, 'post_title' => $title, 'post_name' => $slug]);
    update_post_meta($existing->ID, '_sgi_seed_hash', $hash);
    return [(int)$existing->ID, true];
  }
  $id = wp_insert_post(['post_type' => $type, 'post_name' => $slug, 'post_title' => $title, 'post_status' => $status]);
  if (is_wp_error($id) || !$id) {
    return [0, false];
  }
  update_post_meta($id, '_sgi_seed_hash', $hash);
  return [(int)$id, true];
}

function sgi_import_all(string $seedDir): array {
  $log = ['created' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => []];
  $limits = function_exists('sgi_limits') ? sgi_limits() : ['portfolio.items' => 24, 'service.faqs' => 20, 'service.tiers' => 6, 'service.checklist' => 20, 'service.metrics' => 8, 'service.steps' => 8, 'tentang.values' => 12, 'tentang.timeline' => 12, 'tentang.team' => 12, 'home.clients' => 12, 'home.cases' => 6];
  $read = function (string $f) use (&$log) {
    if (!is_file($f)) { $log['errors'][] = "Hilang: $f"; return null; }
    $d = json_decode((string)file_get_contents($f), true);
    if (!is_array($d)) { $log['errors'][] = "JSON invalid: $f"; return null; }
    return $d;
  };

  // Settings.
  if ($d = $read($seedDir . '/settings.json')) {
    if (!is_email($d['email'] ?? '')) {
      $log['errors'][] = 'settings.json: email tidak valid, dilewati.';
    } else {
      update_option('sgi_settings', [
        'name' => $d['name'] ?? '', 'email' => $d['email'] ?? '', 'phone' => $d['phone'] ?? '',
        'phoneHref' => $d['phoneHref'] ?? '', 'address' => $d['address'] ?? '',
        'hours' => $d['hours'] ?? '', 'mapEmbed' => $d['mapEmbed'] ?? '',
      ]);
      $log['updated']++;
    }
  }

  // Home.
  if ($d = $read($seedDir . '/home.json')) {
    $hp = $d['hero_photo'] ?? [];
    update_option('sgi_home', [
      'hero_photo' => ['src' => $hp['src'] ?? '', 'alt' => sgi_imp_id($hp['alt'] ?? ''), 'w' => (int)($hp['w'] ?? 880), 'h' => (int)($hp['h'] ?? 1000)],
      'kpi_counts' => array_slice($d['kpi_counts'] ?? [], 0, 3),
      'stat_counts' => array_slice($d['stat_counts'] ?? [], 0, 4),
      'clients' => array_slice(array_map('sgi_imp_id', $d['clients'] ?? []), 0, 12),
      'case_imgs' => array_slice(array_map(function ($c) {
        return ['img' => $c['img'] ?? '', 'alt' => sgi_imp_id($c['alt'] ?? '')];
      }, $d['case_imgs'] ?? []), 0, 6),
    ]);
    $log['updated']++;
  }

  // Layanan: semua file services/*.json, slug apa pun diterima.
  foreach ((array)glob($seedDir . '/services/*.json') as $f) {
    $d = $read($f);
    if (!$d) continue;
    $slug = (string)($d['slug'] ?? basename($f, '.json'));
    if (($m = sgi_imp_check('service.checklist', $d['intro']['checklist'] ?? [], $limits))
      || ($m = sgi_imp_check('service.metrics', $d['metrics'] ?? [], $limits))
      || ($m = sgi_imp_check('service.tiers', $d['pricing']['tiers'] ?? [], $limits))
      || ($m = sgi_imp_check('service.steps', $d['steps']['items'] ?? [], $limits))
      || ($m = sgi_imp_check('service.faqs', $d['faqs'] ?? [], $limits))) {
      $log['errors'][] = "$slug: $m Dilewati, data lama utuh.";
      continue;
    }
    [$id, $changed] = sgi_imp_upsert('layanan', $slug, sgi_imp_id($d['title'] ?? $slug));
    if (!$id) { $log['errors'][] = "$slug: gagal tulis post."; continue; }
    $meta = [
      '_sgi_lead' => sgi_imp_id($d['lead'] ?? ''), '_sgi_h1' => sgi_imp_id($d['h1'] ?? ''),
      '_sgi_meta_desc' => sgi_imp_id($d['meta_desc'] ?? ''),
      '_sgi_intro_heading' => sgi_imp_id($d['intro']['heading'] ?? ''),
      '_sgi_intro_body' => sgi_imp_id($d['intro']['body'] ?? ''),
      '_sgi_checklist' => sgi_imp_lines($d['intro']['checklist'] ?? []),
      '_sgi_metrics' => sgi_imp_pairs($d['metrics'] ?? [], 'label', 'value'),
      '_sgi_pricing_heading' => sgi_imp_id($d['pricing']['heading'] ?? ''),
      '_sgi_pricing_body' => sgi_imp_id($d['pricing']['body'] ?? ''),
      '_sgi_quote_btn' => sgi_imp_id($d['pricing']['quote_btn'] ?? ''),
      '_sgi_tiers' => json_encode(array_map(function ($t) {
        return [
          'tier' => sgi_imp_id($t['tier'] ?? ''), 'name' => sgi_imp_id($t['name'] ?? ''),
          'value' => sgi_imp_id($t['value'] ?? ''),
          'items' => array_map('sgi_imp_id', $t['items'] ?? []),
          'featured' => !empty($t['featured']),
        ];
      }, $d['pricing']['tiers'] ?? []), JSON_UNESCAPED_UNICODE),
      '_sgi_steps_heading' => sgi_imp_id($d['steps']['heading'] ?? ''),
      '_sgi_steps' => sgi_imp_pairs($d['steps']['items'] ?? [], 't', 'd'),
      '_sgi_faqs' => sgi_imp_pairs($d['faqs'] ?? [], 'q', 'a'),
      '_sgi_cta_title' => sgi_imp_id($d['cta']['t'] ?? ''),
      '_sgi_cta_desc' => sgi_imp_id($d['cta']['d'] ?? ''),
      '_sgi_cta_btn' => sgi_imp_id($d['cta']['btn'] ?? ''),
      '_sgi_cta_href' => (string)($d['cta']['href'] ?? ''),
    ];
    foreach ($meta as $k => $v) update_post_meta($id, $k, $v);
    $log[$changed ? 'updated' : 'skipped']++;
    if (!$changed) $log['created'] += 0;
  }

  // Portfolio.
  if ($d = $read($seedDir . '/portfolio.json')) {
    if ($m = sgi_imp_check('portfolio.items', $d['items'] ?? [], $limits)) {
      $log['errors'][] = "portfolio: $m Dilewati.";
    } else {
      foreach ($d['items'] ?? [] as $it) {
        $slug = sanitize_title(sgi_imp_id($it['title'] ?? ''));
        if ($slug === '') continue;
        [$id, $changed] = sgi_imp_upsert('portfolio', $slug, sgi_imp_id($it['title'] ?? $slug));
        if ($id) {
          wp_set_object_terms($id, (string)($it['cat'] ?? ''), 'portfolio_cat');
          update_post_meta($id, '_sgi_cat_label', sgi_imp_id($it['cat_t'] ?? ''));
          $log[$changed ? 'updated' : 'skipped']++;
        }
      }
    }
  }

  // Pages inti: tentang/portofolio/kontak/privasi/syarat (sisi id -> konten/meta).
  // 'layanan' SENGAJA tidak dibuat sebagai Page: arsip CPT yang melayani /layanan/.
  $templates = ['tentang' => 'page-tentang.php', 'portofolio' => 'page-portofolio.php', 'kontak' => 'page-kontak.php'];
  foreach (['tentang', 'portofolio', 'kontak', 'privasi', 'syarat'] as $pg) {
    $seedFile = $pg === 'portofolio' ? $seedDir . '/portfolio.json' : $seedDir . '/' . $pg . '.json';
    if ($d = $read($seedFile)) {
      [$id, $changed] = sgi_imp_upsert('page', $pg, $pg === 'portofolio' ? 'Portofolio' : sgi_imp_id($d['h1'] ?? $d['meta_desc'] ?? $pg));
      if ($id) {
        update_post_meta($id, '_sgi_seed', json_encode($d, JSON_UNESCAPED_UNICODE));
        if ($pg === 'tentang') {
          $pair = function ($items, $tk, $dk) {
            return json_encode(array_map(function ($x) use ($tk, $dk) {
              return ['t' => sgi_imp_id($x[$tk] ?? ''), 'd' => sgi_imp_id($x[$dk] ?? '')];
            }, $items), JSON_UNESCAPED_UNICODE);
          };
          update_post_meta($id, '_sgi_values', $pair($d['values'] ?? [], 't', 'd'));
          update_post_meta($id, '_sgi_timeline', $pair($d['timeline'] ?? [], 't', 'd'));
          update_post_meta($id, '_sgi_team', $pair($d['team'] ?? [], 't', 'd'));
        }
        if (isset($templates[$pg])) update_post_meta($id, '_wp_page_template', $templates[$pg]);
        $log[$changed ? 'updated' : 'skipped']++;
      }
    }
  }

  if (php_sapi_name() === 'cli' && defined('WP_CLI') && WP_CLI) {
    WP_CLI::log(json_encode($log, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
  }
  return $log;
}

if (php_sapi_name() === 'cli' && isset($argv) && in_array('--seed', array_slice($argv, 0, 2), true)) {
  $seed = $argv[array_search('--seed', $argv, true) + 1] ?? 'data/seed';
  $log = sgi_import_all($seed);
  echo json_encode($log, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . PHP_EOL;
}
