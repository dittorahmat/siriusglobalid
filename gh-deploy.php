<?php
// Auto-deploy webhook: GitHub push ke main -> pull + salin file situs.
// PENTING: JANGAN taruh secret asli di file ini. Repo ini publik dan
// setiap deploy MENIMPA public_html/gh-deploy.php dengan versi repo.
// Cara pakai (cukup sekali):
// 1. Via cPanel File Manager, buat file berisi secret di HOME
//    (sejajar public_html, BUKAN di dalamnya agar tak bisa
//    diakses via browser). Nama file bebas salah satu:
//      .gh-webhook-secret   (perlu "Show Hidden Files" agar terlihat)
//      gh-webhook-secret.txt  (terlihat biasa, paling mudah)
//    Isinya 1 baris: secret acak panjang, sama persis dengan Secret
//    di setting webhook GitHub.
// 2. Di GitHub repo Settings -> Webhooks -> Add webhook:
//    Payload URL: https://siriusglobal.id/gh-deploy.php
//    Content type: application/json, Secret: <sama>, event: Just the push event.

// Path diturunkan dari lokasi file ini (public_html/gh-deploy.php),
// bukan dari env HOME yang sering kosong di PHP-FPM shared hosting.
$pub = __DIR__;
$home = dirname($pub);

$secretFile = $home . '/.gh-webhook-secret';
if (!is_file($secretFile) && is_file($home . '/gh-webhook-secret.txt')) {
    $secretFile = $home . '/gh-webhook-secret.txt';
}
$secret = is_file($secretFile) ? trim((string) @file_get_contents($secretFile)) : '';
if ($secret === '' || $secret === 'GANTI-DENGAN-SECRET-ACAK-PANJANG') {
    http_response_code(500);
    exit('secret belum dikonfigurasi di ~/.gh-webhook-secret');
}

$payload = file_get_contents('php://input');
$sig = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
$expected = 'sha256=' . hash_hmac('sha256', $payload, $secret);
if (!hash_equals($expected, $sig)) {
    http_response_code(403);
    exit('forbidden');
}

$data = json_decode($payload, true);
if (($_SERVER['HTTP_X_GITHUB_EVENT'] ?? '') !== 'push') {
    exit('ignored: bukan push');
}
if (($data['ref'] ?? '') !== 'refs/heads/main') {
    exit('ignored: bukan branch main');
}

ignore_user_abort(true);
set_time_limit(120);
echo 'ok: deploy dimulai';
if (function_exists('fastcgi_finish_request')) {
    fastcgi_finish_request();
}

// $pub dan $home sudah dihitung di atas (sebelum cek signature).
$repo = $home . '/siriusglobalid';
$logfile = $home . '/deploy.log';

$log = ['at' => date('c'), 'repo' => $repo];
if (!function_exists('shell_exec')) {
    $log['error'] = 'shell_exec dimatikan hosting ini';
} elseif (!is_dir($repo . '/.git')) {
    $log['error'] = 'clone tidak ditemukan di ' . $repo;
} else {
    $log['git_which'] = trim((string) shell_exec('which git 2>&1'));
    $log['pull'] = shell_exec('git -C ' . escapeshellarg($repo) . ' pull --ff-only 2>&1');
    $files = ['index.html', 'tentang.html', 'layanan.html', 'portofolio.html', 'kontak.html', '404.html', 'privasi.html', 'syarat.html', 'robots.txt', 'sitemap.xml', 'gh-deploy.php'];
    $copied = 0;
    foreach ($files as $f) {
        if (@copy($repo . '/' . $f, $pub . '/' . $f)) {
            $copied++;
        }
    }
    $log['copied_files'] = $copied . '/' . count($files);
    $log['copydirs'] = shell_exec('/bin/cp -R ' . escapeshellarg($repo . '/css') . ' ' . escapeshellarg($repo . '/js') . ' ' . escapeshellarg($repo . '/assets') . ' ' . escapeshellarg($repo . '/layanan') . ' ' . escapeshellarg($pub) . ' 2>&1');
}
@file_put_contents($logfile, json_encode($log) . PHP_EOL, FILE_APPEND);
