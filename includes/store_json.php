<?php
// store_json.php - lapisan penyimpanan JSON v1 (Opsi C).
// Antarmuka: cms_data_dir(), cms_load($domain), cms_save($domain, $data, $expectedUpdatedAt),
// cms_validate($domain, $data), cms_last_error().
// Tulis atomik: LOCK_EX + file .tmp + rename. Backup otomatis dipicu pemanggil via backup.php.
declare(strict_types=1);

const CMS_PORTFOLIO_CATS = ['erp', 'ai', 'fleet', 'app', 'iot', 'infra', 'sec', 'bi'];
const CMS_SERVICE_SLUGS = [
  'app-development', 'ai-solution', 'iot', 'odoo-erp', 'fleet-management',
  'infrastruktur', 'dashboard-bi', 'cybersecurity', 'training',
];

$GLOBALS['__cms_last_error'] = '';

function cms_last_error(): string {
  return $GLOBALS['__cms_last_error'];
}

function cms_set_error(string $msg): void {
  $GLOBALS['__cms_last_error'] = $msg;
}

/** Direktori data runtime. Prod: $HOME/data (di luar public_html). Lokal: <repo>/data. */
function cms_data_dir(): string {
  $env = getenv('CMS_DATA_DIR');
  if (is_string($env) && $env !== '' && is_dir($env)) return rtrim($env, '/');
  $docroot = '';
  if (!empty($_SERVER['DOCUMENT_ROOT'])) {
    $rp = realpath($_SERVER['DOCUMENT_ROOT']);
    if ($rp !== false) $docroot = $rp;
  }
  if ($docroot !== '') {
    $homeData = dirname($docroot) . '/data';
    if (is_dir($homeData)) return $homeData;
  }
  $local = dirname(__DIR__) . '/data';
  return $local;
}

function cms_seed_dir(): string {
  $docroot = '';
  if (!empty($_SERVER['DOCUMENT_ROOT'])) {
    $rp = realpath($_SERVER['DOCUMENT_ROOT']);
    if ($rp !== false) $docroot = $rp;
  }
  if ($docroot !== '') {
    $homeSeed = dirname($docroot) . '/data/seed';
    if (is_dir($homeSeed)) return $homeSeed;
  }
  return dirname(__DIR__) . '/data/seed';
}

/** Domain yang dikenal -> file JSON. */
function cms_domain_file(string $domain): ?string {
  if (str_starts_with($domain, 'service:')) {
    $slug = substr($domain, 8);
    if (!in_array($slug, CMS_SERVICE_SLUGS, true)) return null;
    return cms_data_dir() . '/services/' . $slug . '.json';
  }
  if (!preg_match('/^[a-z0-9_\-\.]+$/', $domain)) return null;
  $allowed = [
    'settings', 'home', 'tentang', 'layanan', 'portfolio', 'kontak',
    'privasi', 'syarat', 'i18n.id', 'i18n.en',
  ];
  if (!in_array($domain, $allowed, true)) return null;
  return cms_data_dir() . '/' . $domain . '.json';
}

function cms_seed_file(string $domain): ?string {
  if (str_starts_with($domain, 'service:')) {
    $slug = substr($domain, 8);
    if (!in_array($slug, CMS_SERVICE_SLUGS, true)) return null;
    return cms_seed_dir() . '/services/' . $slug . '.json';
  }
  return cms_seed_dir() . '/' . $domain . '.json';
}

/** Muat domain. Fallback: seed bila runtime hilang; array kosong bila keduanya hilang. */
function cms_load(string $domain): array {
  cms_set_error('');
  $file = cms_domain_file($domain);
  if ($file === null) { cms_set_error('Domain tidak dikenal.'); return []; }
  if (is_file($file)) {
    $raw = @file_get_contents($file);
    if ($raw !== false) {
      $data = json_decode($raw, true);
      if (is_array($data)) return $data;
      cms_log('cms_load invalid json: ' . $file);
    }
  }
  $seed = cms_seed_file($domain);
  if ($seed !== null && is_file($seed)) {
    $raw = @file_get_contents($seed);
    if ($raw !== false) {
      $data = json_decode($raw, true);
      if (is_array($data)) return $data;
    }
  }
  cms_set_error('Data tidak ditemukan, memakai fallback kosong.');
  return [];
}

/** Batas jumlah item per daftar (anti-jebol layout, satu JSON < 200KB). */
function cms_limits(): array {
  return [
    'portfolio.items' => 24, 'service.faqs' => 20, 'service.tiers' => 6,
    'service.checklist' => 20, 'service.metrics' => 8, 'service.steps' => 8,
    'tentang.values' => 12, 'tentang.timeline' => 12, 'tentang.team' => 12,
    'home.clients' => 12, 'home.cases' => 6,
  ];
}

function cms_over_limit(string $key, array $items): ?string {
  $limits = cms_limits();
  if (isset($limits[$key]) && count($items) > $limits[$key]) {
    return 'Daftar ' . $key . ' maksimal ' . $limits[$key] . ' item (kini ' . count($items) . ').';
  }
  return null;
}

/** Validasi skema minimal per domain. Mengembalikan [ok, pesan]. */
function cms_validate(string $domain, array $data): array {
  if (str_starts_with($domain, 'service:')) {
    foreach (['slug', 'title', 'lead'] as $k) {
      if (!isset($data[$k]) || (!is_string($data[$k]) && !is_array($data[$k]))) {
        return [false, 'Field wajib hilang: ' . $k];
      }
    }
    if (($data['slug'] ?? '') !== substr($domain, 8)) return [false, 'Slug tidak cocok dengan file.'];
    foreach ([
      'service.faqs' => ($data['faqs'] ?? []),
      'service.tiers' => ($data['pricing']['tiers'] ?? []),
      'service.checklist' => ($data['intro']['checklist'] ?? []),
      'service.metrics' => ($data['metrics'] ?? []),
      'service.steps' => ($data['steps']['items'] ?? []),
    ] as $key => $items) {
      if (!is_array($items)) return [false, 'Daftar rusak: ' . $key];
      if ($m = cms_over_limit($key, $items)) return [false, $m];
    }
    return [true, ''];
  }
  switch ($domain) {
    case 'settings':
      foreach (['name', 'email', 'phone', 'phoneHref', 'address', 'hours'] as $k) {
        if (empty($data[$k]) || !is_string($data[$k])) return [false, 'Field wajib hilang: ' . $k];
      }
      if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) return [false, 'Email tidak valid.'];
      return [true, ''];
    case 'portfolio':
      if (!isset($data['items']) || !is_array($data['items'])) return [false, 'Field wajib hilang: items'];
      if ($m = cms_over_limit('portfolio.items', $data['items'])) return [false, $m];
      foreach ($data['items'] as $i => $it) {
        if (!is_array($it) || empty($it['title']) || empty($it['cat'])) {
          return [false, 'Item #' . ((int)$i + 1) . ' wajib punya title dan cat.'];
        }
        if (!in_array($it['cat'], CMS_PORTFOLIO_CATS, true)) {
          return [false, 'Item #' . ((int)$i + 1) . ': cat tidak dikenal (' . (string)$it['cat'] . ').'];
        }
      }
      return [true, ''];
    case 'home': case 'tentang': case 'layanan': case 'kontak': case 'privasi': case 'syarat':
      foreach ([
        'home.clients' => ($data['clients'] ?? null),
        'home.cases' => ($data['case_imgs'] ?? null),
        'tentang.values' => ($data['values'] ?? null),
        'tentang.timeline' => ($data['timeline'] ?? null),
        'tentang.team' => ($data['team'] ?? null),
      ] as $key => $items) {
        if ($items === null) continue;
        if (!is_array($items)) return [false, 'Daftar rusak: ' . $key];
        if ($m = cms_over_limit($key, $items)) return [false, $m];
      }
      return [true, ''];
    case 'i18n.id': case 'i18n.en':
      if (!isset($data['keys']) || !is_array($data['keys'])) return [false, 'Field wajib hilang: keys'];
      return [true, ''];
    default:
      return [false, 'Domain tidak dikenal.'];
  }
}

/**
 * Simpan domain secara atomik.
 * $expectedUpdatedAt: optimistic lock; bila tidak null dan beda dari file saat ini -> tolak.
 * Mengembalikan true sukses; false + cms_last_error().
 */
function cms_save(string $domain, array $data, ?string $expectedUpdatedAt = null): bool {
  cms_set_error('');
  $file = cms_domain_file($domain);
  if ($file === null) { cms_set_error('Domain tidak dikenal.'); return false; }
  $data['updated_at'] = gmdate('Y-m-d\TH:i:s\Z');
  [$ok, $msg] = cms_validate($domain, $data);
  if (!$ok) { cms_set_error($msg); return false; }
  $dir = dirname($file);
  if (!is_dir($dir) && !@mkdir($dir, 0755, true)) { cms_set_error('Gagal membuat direktori data.'); return false; }

  $fp = @fopen($file, 'c+');
  if ($fp === false) {
    // File belum ada: tulis langsung via tmp+rename.
    return cms_write_new($file, $domain, $data);
  }
  if (!flock($fp, LOCK_EX)) { fclose($fp); cms_set_error('Gagal mengunci file. Coba lagi.'); return false; }
  $raw = stream_get_contents($fp);
  $current = is_string($raw) && $raw !== '' ? json_decode($raw, true) : null;
  if ($expectedUpdatedAt !== null && is_array($current)
      && isset($current['updated_at']) && $current['updated_at'] !== $expectedUpdatedAt) {
    flock($fp, LOCK_UN); fclose($fp);
    cms_set_error('Data berubah di tab lain. Muat ulang sebelum menyimpan.');
    return false;
  }
  // Backup otomatis dari isi lama sebelum timpa.
  if (is_array($current)) cms_backup_snapshot($domain, $current);
  // Tutup handle SEBELUM rename: Windows menolak rename file yang
  // masih terbuka (di POSIX tidak masalah). Cek revisi sudah terjadi
  // di bawah lock di atas; jendela balapan tersisa minimal untuk
  // editor tunggal dan tetap tertutup optimistic-lock berikutnya.
  flock($fp, LOCK_UN); fclose($fp);
  $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
  if ($json === false) { cms_set_error('Gagal encode JSON.'); return false; }
  $tmp = $file . '.' . bin2hex(random_bytes(6)) . '.tmp';
  if (@file_put_contents($tmp, $json, LOCK_EX) === false) {
    cms_set_error('Gagal menulis file sementara.'); return false;
  }
  $renamed = @rename($tmp, $file);
  if (!$renamed) { @unlink($tmp); cms_set_error('Gagal menyimpan file.'); return false; }
  @chmod($file, 0644);
  return true;
}

function cms_write_new(string $file, string $domain, array $data): bool {
  $data['updated_at'] = gmdate('Y-m-d\TH:i:s\Z');
  [$ok, $msg] = cms_validate($domain, $data);
  if (!$ok) { cms_set_error($msg); return false; }
  $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
  if ($json === false) { cms_set_error('Gagal encode JSON.'); return false; }
  $tmp = $file . '.' . bin2hex(random_bytes(6)) . '.tmp';
  if (@file_put_contents($tmp, $json, LOCK_EX) === false) { cms_set_error('Gagal menulis file.'); return false; }
  if (!@rename($tmp, $file)) { @unlink($tmp); cms_set_error('Gagal menyimpan file.'); return false; }
  @chmod($file, 0644);
  return true;
}

function cms_log(string $msg): void {
  $dir = cms_data_dir();
  @file_put_contents($dir . '/cms-error.log', '[' . date('c') . '] ' . $msg . PHP_EOL, FILE_APPEND | LOCK_EX);
}
