<?php
// tools/setup-admin.php - JALANKAN SEKALI via CLI di server (tanpa SSH bisa
// lewat cPanel > Cron Jobs satu-kali, atau cPanel > Terminal bila tersedia):
//   php tools/setup-admin.php <username> <password-min-16-karakter>
// Membuat data/admin.json (di luar public_html saat deploy).
// DITOLAK bila diakses via web.
if (php_sapi_name() !== 'cli') {
  http_response_code(403);
  exit('CLI only.');
}
if ($argc !== 3 || strlen($argv[2]) < 16) {
  fwrite(STDERR, "Pakai: php tools/setup-admin.php <username> <password-min-16-karakter>\n");
  exit(1);
}
$dataDir = getenv('CMS_DATA_DIR');
if (!$dataDir) {
  // cPanel (cron/Terminal): HOME=/home/username -> pakai /home/username/data
  // agar sama dengan yang dibaca web (docroot sibling), bukan data di repo.
  $home = getenv('HOME');
  if (is_string($home) && preg_match('#^/home/[^/]+$#', $home)) {
    $dataDir = $home . '/data';
  } else {
    // Lokal: tools/ -> root/data.
    $dataDir = dirname(__DIR__) . '/data';
  }
}
if (!is_dir($dataDir) && !mkdir($dataDir, 0755, true)) {
  fwrite(STDERR, "Gagal membuat $dataDir\n");
  exit(1);
}
$file = $dataDir . '/admin.json';
if (is_file($file)) {
  fwrite(STDERR, "admin.json sudah ada. Hapus manual bila ingin reset.\n");
  exit(1);
}
$payload = json_encode([
  'user' => $argv[1],
  'hash' => password_hash($argv[2], PASSWORD_DEFAULT),
  'created_at' => gmdate('c'),
], JSON_PRETTY_PRINT);
file_put_contents($file, $payload, LOCK_EX);
chmod($file, 0600);
echo "OK: $file\n";
