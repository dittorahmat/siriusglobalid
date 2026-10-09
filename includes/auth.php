<?php
// auth.php - login admin tunggal: password_hash, regenerate session,
// rate-limit file-based (5 gagal / 10 menit / IP), CSRF token, idle timeout.
declare(strict_types=1);

const CMS_ADMIN_IDLE = 3600;
const CMS_ADMIN_MAX_FAIL = 5;
const CMS_ADMIN_WINDOW = 600;

function cms_admin_file(): string {
  return cms_data_dir() . '/admin.json';
}

function cms_session_start(): void {
  if (session_status() === PHP_SESSION_ACTIVE) return;
  $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
  session_set_cookie_params([
    'lifetime' => 0, 'path' => '/', 'httponly' => true,
    'samesite' => 'Lax', 'secure' => $secure,
  ]);
  session_start();
}

function cms_admin_user(): ?array {
  $f = cms_admin_file();
  if (!is_file($f)) return null;
  $d = json_decode((string)@file_get_contents($f), true);
  return is_array($d) && !empty($d['user']) ? $d : null;
}

function cms_logged_in(): bool {
  cms_session_start();
  if (empty($_SESSION['cms_admin']) || empty($_SESSION['cms_login_at'])) return false;
  if (time() - (int)$_SESSION['cms_login_at'] > CMS_ADMIN_IDLE) {
    cms_logout();
    return false;
  }
  $_SESSION['cms_login_at'] = time();
  return true;
}

function cms_require_admin(): void {
  if (!cms_logged_in()) {
    header('Location: login.php');
    exit;
  }
}

function cms_ratelimit_dir(): string {
  $d = cms_data_dir() . '/ratelimit';
  if (!is_dir($d)) @mkdir($d, 0755, true);
  return $d;
}

function cms_ratelimit_key(): string {
  $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
  return cms_ratelimit_dir() . '/' . preg_replace('/[^a-zA-Z0-9\.\-_]/', '_', $ip) . '.json';
}

function cms_ratelimit_check(): array {
  $f = cms_ratelimit_key();
  $now = time();
  $d = is_file($f) ? (json_decode((string)@file_get_contents($f), true) ?: []) : [];
  $fails = array_values(array_filter((array)($d['fails'] ?? []), fn($t) => $now - (int)$t < CMS_ADMIN_WINDOW));
  if (count($fails) >= CMS_ADMIN_MAX_FAIL) {
    $wait = CMS_ADMIN_WINDOW - ($now - (int)min($fails));
    return [false, $wait];
  }
  return [true, 0];
}

function cms_ratelimit_hit(): void {
  $f = cms_ratelimit_key();
  $d = is_file($f) ? (json_decode((string)@file_get_contents($f), true) ?: []) : [];
  $fails = (array)($d['fails'] ?? []);
  $fails[] = time();
  @file_put_contents($f, json_encode(['fails' => $fails]), LOCK_EX);
}

function cms_ratelimit_reset(): void {
  @unlink(cms_ratelimit_key());
}

function cms_try_login(string $user, string $pass): array {
  [$allow, $wait] = cms_ratelimit_check();
  if (!$allow) return [false, 'Terlalu banyak percobaan. Coba lagi dalam ' . (int)ceil($wait / 60) . ' menit.'];
  $a = cms_admin_user();
  if ($a === null) return [false, 'Akun admin belum dibuat. Jalankan tools/setup-admin.php sekali via CLI.'];
  if (!hash_equals((string)$a['user'], $user) || !password_verify($pass, (string)$a['hash'])) {
    cms_ratelimit_hit();
    return [false, 'Kredensial salah.'];
  }
  cms_ratelimit_reset();
  cms_session_start();
  session_regenerate_id(true);
  $_SESSION['cms_admin'] = $a['user'];
  $_SESSION['cms_login_at'] = time();
  return [true, ''];
}

function cms_logout(): void {
  cms_session_start();
  $_SESSION = [];
  if (session_status() === PHP_SESSION_ACTIVE) session_destroy();
}

function cms_csrf(): string {
  cms_session_start();
  if (empty($_SESSION['cms_csrf'])) $_SESSION['cms_csrf'] = bin2hex(random_bytes(32));
  return $_SESSION['cms_csrf'];
}

function cms_csrf_check(?string $token): bool {
  cms_session_start();
  $s = (string)($_SESSION['cms_csrf'] ?? '');
  return $s !== '' && $token !== null && hash_equals($s, $token);
}
