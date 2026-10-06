<?php
// Auto-deploy webhook: GitHub push ke main -> pull + salin file situs.
// Cara pakai:
// 1. File ini ter-copy otomatis ke public_html via .cpanel.yml.
// 2. Via cPanel File Manager, ganti WEBHOOK_SECRET di bawah dengan string
//    acak panjang (sama persis dengan Secret di setting webhook GitHub).
// 3. Di GitHub repo Settings -> Webhooks -> Add webhook:
//    Payload URL: https://siriusglobal.id/gh-deploy.php
//    Content type: application/json, Secret: <sama>, event: Just the push event.

define('GH_WEBHOOK_SECRET', 'GANTI-DENGAN-SECRET-ACAK-PANJANG');

$payload = file_get_contents('php://input');
$sig = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
$expected = 'sha256=' . hash_hmac('sha256', $payload, GH_WEBHOOK_SECRET);
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

$home = rtrim(getenv('HOME') ?: '', '/');
$log = [];
if ($home === '' || !function_exists('shell_exec')) {
    $log[] = 'exec tidak tersedia di hosting ini';
} else {
    $repo = $home . '/siriusglobalid';
    $pub = $home . '/public_html';
    $log[] = shell_exec('git -C ' . escapeshellarg($repo) . ' pull --ff-only 2>&1');
    foreach (['index.html', 'tentang.html', 'layanan.html', 'portofolio.html', 'kontak.html', '404.html', 'robots.txt', 'sitemap.xml', 'gh-deploy.php'] as $f) {
        @copy($repo . '/' . $f, $pub . '/' . $f);
    }
    $log[] = shell_exec('/bin/cp -R ' . escapeshellarg($repo . '/css') . ' ' . escapeshellarg($repo . '/js') . ' ' . escapeshellarg($repo . '/assets') . ' ' . escapeshellarg($repo . '/layanan') . ' ' . escapeshellarg($pub) . ' 2>&1');
}
@file_put_contents($home . '/deploy.log', date('c') . ' ' . json_encode($log) . PHP_EOL, FILE_APPEND);
