<?php
// tools/sgi-admin-harness.php - Render halaman Konten Situs tanpa WordPress.
// Pakai: php tools/sgi-admin-harness.php [tab] [nojs] > sgi-admin.html
// Aset CSS/JS di-inline agar file HTML bisa langsung dibuka di browser.
declare(strict_types=1);

require __DIR__ . '/wp-stubs.php';
require dirname(__DIR__) . '/wp-content/themes/sirius-custom/functions.php';

$tab = (string)($argv[1] ?? 'header');
$nojs = ($argv[2] ?? '') === 'nojs';
$_GET['tab'] = $tab;

ob_start();
sgi_options_render();
$inner = (string)ob_get_clean();

$theme = dirname(__DIR__) . '/wp-content/themes/sirius-custom';
$css = (string)file_get_contents($theme . '/assets/css/admin.css');
$js = $nojs ? '' : (string)file_get_contents($theme . '/assets/js/admin.js');

// Approximation komponen wp-admin yang dipakai halaman (bukan pengganti core).
$wp = <<<'CSS'
body { margin: 0; padding: 20px; font: 13px/1.4 -apple-system, "Segoe UI", sans-serif; color: #1d2327; background: #f0f0f1; }
.wrap { max-width: 1100px; }
h1 { font-size: 23px; font-weight: 400; margin: 0 0 12px; }
h2 { font-size: 16px; font-weight: 600; margin: 22px 0 8px; }
.description { color: #646970; font-size: 12px; }
input[type="text"], textarea { box-sizing: border-box; border: 1px solid #8c8f94; border-radius: 4px; padding: 4px 8px; font-size: 14px; }
.nav-tab { display: inline-block; padding: 8px 12px; border: 1px solid #c3c4c7; border-bottom: 0; background: #dcdcde; color: #50575e; }
.nav-tab-active { background: #f0f0f1; color: #000; }
.form-table { border-collapse: collapse; width: 100%; }
.form-table th, .form-table td { text-align: left; padding: 12px 10px; vertical-align: top; }
.form-table th { width: 180px; font-weight: 600; }
.button { display: inline-block; padding: 5px 12px; border: 1px solid #2271b1; border-radius: 3px; background: #f6f7f7; color: #2271b1; }
.button-primary { background: #2271b1; color: #fff; }
@media (max-width: 782px) { .form-table th, .form-table td { display: block; width: auto; } }
CSS;

echo '<!DOCTYPE html><html lang="id"><head><meta charset="utf-8">'
  . '<meta name="viewport" content="width=device-width, initial-scale=1">'
  . '<title>Konten Situs - ' . htmlspecialchars($tab, ENT_QUOTES, 'UTF-8') . '</title>'
  . '<style>' . $wp . "\n" . $css . '</style></head><body>' . $inner;
if ($js !== '') echo '<script>' . $js . '</script>';
echo '</body></html>';
