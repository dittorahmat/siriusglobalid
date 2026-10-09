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
// Beberapa hosting memakai symlink docroot, jadi pilih kandidat pertama
// yang benar-benar ada.
$pub = __DIR__;
$homeCandidates = [dirname($pub)];
if (($rp = realpath($pub)) !== false) { $homeCandidates[] = dirname($rp); }
$envHome = getenv('HOME');
if (is_string($envHome) && $envHome !== '') { $homeCandidates[] = $envHome; }
$home = $pub;
foreach ($homeCandidates as $c) { if (is_dir($c)) { $home = $c; break; } }

$candidates = [$home . '/.gh-webhook-secret', $home . '/gh-webhook-secret.txt', $home . '/gh-webhook-secret'];
$secretFile = '';
foreach ($candidates as $c) { if (is_file($c)) { $secretFile = $c; break; } }
$raw = ($secretFile !== '') ? (string) @file_get_contents($secretFile) : '';
$raw = preg_replace("/^\xEF\xBB\xBF/", '', $raw); // buang BOM editor
$secret = preg_replace('/\s+/', '', $raw); // buang spasi/newline tempelan
if ($secret === '' || $secret === 'GANTI-DENGAN-SECRET-ACAK-PANJANG') {
    http_response_code(500);
    @file_put_contents($home . '/deploy.log', json_encode(['at' => date('c'), 'auth' => 'secret file tidak terbaca', 'checked' => $candidates]) . PHP_EOL, FILE_APPEND);
    exit('secret belum dikonfigurasi di ~/.gh-webhook-secret');
}

$payload = file_get_contents('php://input');
$sig = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
$expected = 'sha256=' . hash_hmac('sha256', $payload, $secret);
if (!hash_equals($expected, $sig)) {
    http_response_code(403);
    @file_put_contents($home . '/deploy.log', json_encode(['at' => date('c'), 'auth' => 'forbidden: secret tidak cocok']) . PHP_EOL, FILE_APPEND);
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
// SENGAJA sinkron: GitHub dan penguji melihat hasil JSON sebagai respons.
// Jangan echo dulu + finish-request, karena di hosting ini proses
// background sering mati sebelum menulis apa pun.

// $pub dan $home sudah dihitung di atas (sebelum cek signature).
$repo = $home . '/siriusglobalid';

$log = ['at' => date('c'), 'repo' => $repo, 'home_used' => $home];
$files = ['index.php', 'tentang.php', 'layanan.php', 'portofolio.php', 'kontak.php', '404.php', 'privasi.php', 'syarat.php', 'robots.txt', 'sitemap.xml', '.htaccess', 'gh-deploy.php'];
$siteDirs = ['css', 'js', 'assets', 'templates', 'includes', 'admin', 'layanan', 'tools'];
// DATA PRODUKSI ($HOME/data/*.json + uploads) TIDAK PERNAH ditimpa:
// seed hanya disalin bila file tujuan belum ada (lihat bawah).
// Salin rekursif murni-PHP (pengganti cp -R saat shell mati).
$rcopy = function ($s, $d) use (&$rcopy, &$copied) {
    if (is_dir($s)) {
        @mkdir($d, 0755, true);
        foreach ((array) @scandir($s) as $e) {
            if ($e === '.' || $e === '..') continue;
            $rcopy($s . '/' . $e, $d . '/' . $e);
        }
    } elseif (@copy($s, $d)) { $copied++; }
};
$rrmdir = function ($d) use (&$rrmdir) {
    if (!is_dir($d)) { @unlink($d); return; }
    foreach ((array) @scandir($d) as $e) {
        if ($e === '.' || $e === '..') continue;
        $rrmdir($d . '/' . $e);
    }
    @rmdir($d);
};
$copied = 0;
if (!function_exists('shell_exec')) {
    // Jalur tanpa shell: unduh ZIP branch main + ekstrak via ZipArchive.
    // Tidak butuh git, tidak butuh shell_exec.
    $log['mode'] = 'zip';
    if (!class_exists('ZipArchive')) {
        $log['error'] = 'shell_exec mati dan ZipArchive tidak tersedia di hosting ini';
    } elseif (!ini_get('allow_url_fopen')) {
        $log['error'] = 'shell_exec mati dan allow_url_fopen mati di hosting ini';
    } else {
        $tmpBase = sys_get_temp_dir() . '/sgi-' . bin2hex(random_bytes(6));
        $zipFile = $tmpBase . '.zip';
        $dl = @copy('https://github.com/dittorahmat/siriusglobalid/archive/refs/heads/main.zip', $zipFile);
        if (!$dl || !is_file($zipFile)) {
            $log['error'] = 'gagal unduh ZIP main dari GitHub';
        } else {
            $zip = new ZipArchive();
            if ($zip->open($zipFile) !== true) {
                $log['error'] = 'gagal buka ZIP main';
            } else {
                $zip->extractTo($tmpBase);
                $zip->close();
                $roots = glob($tmpBase . '/*', GLOB_ONLYDIR);
                $src = $roots ? $roots[0] : null;
                if (!$src) {
                    $log['error'] = 'isi ZIP tak terduga';
                } else {
                    foreach ($files as $f) { $rcopy($src . '/' . $f, $pub . '/' . $f); }
                    foreach ($siteDirs as $dd) { $rcopy($src . '/' . $dd, $pub . '/' . $dd); }
                    // Seed CMS: salin hanya bila belum ada (jangan timpa data produksi).
                    foreach (['', '/services'] as $sub) {
                        @mkdir($home . '/data/seed' . $sub, 0755, true);
                        foreach ((array) @glob($src . '/data/seed' . $sub . '/*.json') as $sf) {
                            $dest = $home . '/data/seed' . $sub . '/' . basename($sf);
                            if (!is_file($dest)) { @copy($sf, $dest); }
                        }
                    }
                    @mkdir($home . '/data/services', 0755, true);
                    @mkdir($home . '/data/backups', 0755, true);
                    @mkdir($home . '/data/ratelimit', 0755, true);
                    @mkdir($pub . '/assets/uploads', 0755, true);
                    $log['copied_files'] = $copied;
                }
            }
            @unlink($zipFile);
            $rrmdir($tmpBase);
        }
    }
} elseif (!is_dir($repo . '/.git')) {
    $log['error'] = 'clone tidak ditemukan di ' . $repo;
} else {
    $log['git_which'] = trim((string) shell_exec('which git 2>&1'));
    // Self-healing: mirror deploy, paksa bersih agar tak pernah
    // terblokir "uncommitted changes" seperti tombol Deploy cPanel.
    $log['fetch'] = shell_exec('git -C ' . escapeshellarg($repo) . ' fetch origin 2>&1');
    $log['reset'] = shell_exec('git -C ' . escapeshellarg($repo) . ' reset --hard origin/main 2>&1');
    $copied = 0;
    foreach ($files as $f) {
        if (@copy($repo . '/' . $f, $pub . '/' . $f)) {
            $copied++;
        }
    }
    $log['copied_files'] = $copied . '/' . count($files);
    $log['copydirs'] = shell_exec('/bin/cp -R ' . escapeshellarg($repo . '/css') . ' ' . escapeshellarg($repo . '/js') . ' ' . escapeshellarg($repo . '/assets') . ' ' . escapeshellarg($repo . '/templates') . ' ' . escapeshellarg($repo . '/includes') . ' ' . escapeshellarg($repo . '/admin') . ' ' . escapeshellarg($repo . '/layanan') . ' ' . escapeshellarg($repo . '/tools') . ' ' . escapeshellarg($pub) . ' 2>&1');
    // Seed CMS: hanya bila belum ada (cp -n), data produksi tidak tersentuh.
    $log['seeddirs'] = shell_exec('mkdir -p ' . escapeshellarg($home . '/data/seed/services') . ' ' . escapeshellarg($home . '/data/services') . ' ' . escapeshellarg($home . '/data/backups') . ' ' . escapeshellarg($home . '/data/ratelimit') . ' ' . escapeshellarg($pub . '/assets/uploads') . ' 2>&1');
    $log['seedcopy'] = shell_exec('/bin/cp -Rn ' . escapeshellarg($repo . '/data/seed/.') . ' ' . escapeshellarg($home . '/data/seed/') . ' 2>&1');
}
// Tulis ke dua tempat: HOME (utama) dan public_html (cadangan yang mudah
// ditemukan). Isi log hanya path + output git, tanpa secret.
// Respons juga membawa JSON yang sama agar terlihat di GitHub deliveries.
$line = json_encode($log) . PHP_EOL;
$wroteHome = (bool) @file_put_contents($home . '/deploy.log', $line, FILE_APPEND);
if (!$wroteHome) {
    @file_put_contents($pub . '/deploy-webhook.log', $line, FILE_APPEND);
}
header('Content-Type: application/json');
echo $line;
