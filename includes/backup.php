<?php
// backup.php - snapshot otomatis per save + download ZIP + restore tervalidasi.
declare(strict_types=1);

function cms_backup_dir(): string {
  $d = cms_data_dir() . '/backups';
  if (!is_dir($d)) @mkdir($d, 0755, true);
  return $d;
}

/** Simpan snapshot isi lama (maks 20 per domain). */
function cms_backup_snapshot(string $domain, array $old): void {
  $safe = preg_replace('/[^a-z0-9_\-:]/i', '_', $domain);
  $f = cms_backup_dir() . '/' . $safe . '-' . gmdate('Ymd-His') . '.json';
  @file_put_contents($f, json_encode($old, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT), LOCK_EX);
  $files = glob(cms_backup_dir() . '/' . $safe . '-*.json') ?: [];
  rsort($files);
  foreach (array_slice($files, 20) as $extra) @unlink($extra);
}

/** Kumpulkan semua file data runtime untuk ZIP. */
function cms_backup_files(): array {
  $dir = cms_data_dir();
  $out = [];
  foreach (['settings', 'home', 'tentang', 'layanan', 'portfolio', 'kontak', 'privasi', 'syarat', 'i18n.id', 'i18n.en'] as $d) {
    $f = $dir . '/' . $d . '.json';
    if (is_file($f)) $out[$d . '.json'] = $f;
  }
  foreach (glob($dir . '/services/*.json') ?: [] as $f) {
    $out['services/' . basename($f)] = $f;
  }
  return $out;
}

/** Validasi isi restore sebelum dipakai. Mengembalikan [ok, pesan]. */
function cms_backup_validate_array(array $all): array {
  if (empty($all)) return [false, 'Arsip kosong.'];
  foreach ($all as $name => $data) {
    if (!is_array($data)) return [false, 'Berkas rusak: ' . $name];
    $domain = preg_replace('/\.json$/', '', $name);
    $domain = str_starts_with($domain, 'services/')
      ? 'service:' . basename($domain)
      : $domain;
    [$ok, $msg] = cms_validate($domain, $data);
    if (!$ok) return [false, 'Berkas ' . $name . ': ' . $msg];
  }
  return [true, ''];
}
