<?php
// tools/sgi-static-check.php - Guardrail aturan mekanis AGENTS.
// Pakai: php tools/sgi-static-check.php (exit 0 = lolos)
declare(strict_types=1);

$root = dirname(__DIR__);
$fail = [];
$check = function (string $name, bool $ok, string $detail = '') use (&$fail): void {
  echo ($ok ? 'PASS' : 'FAIL') . ' ' . $name . ($ok || $detail === '' ? '' : ' (' . $detail . ')') . PHP_EOL;
  if (!$ok) $fail[] = $name;
};

$theme = $root . '/wp-content/themes/sirius-custom';
$files = [];
foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($theme, FilesystemIterator::SKIP_DOTS)) as $f) {
  if (in_array(strtolower($f->getExtension()), ['php', 'css', 'js'], true)) $files[] = $f->getPathname();
}
$source = '';
foreach ($files as $f) $source .= "\n" . file_get_contents($f);

foreach (['Settings > Sirius', 'data-i18n', 'lang-toggle'] as $needle) {
  $check('theme bebas "' . $needle . '"', !str_contains($source, $needle));
}
$check('theme bebas em-dash/en-dash', !preg_match('/[\x{2013}\x{2014}]/u', $source));
$check('theme bebas inline grid-template-columns', !preg_match('/style\s*=\s*["\'][^"\']*grid-template-columns/i', $source));
$check('header.php punya meta viewport', (bool)preg_match('/name=["\']viewport["\']/', file_get_contents($theme . '/header.php')));

$agents = file_get_contents($root . '/AGENTS.md');
$check('AGENTS menunjuk assets/css theme aktif', str_contains($agents, 'wp-content/themes/sirius-custom/assets/css'));
$check('AGENTS bebas lang-toggle', !str_contains($agents, 'lang-toggle'));
$check('AGENTS bebas js/i18n.js', !str_contains($agents, 'js/i18n.js'));
$check('AGENTS bebas scope css/*.css', !preg_match('/(?<!assets\/)css\/\*\.css/', $agents));

// Guardrail: meta.php tidak boleh memanggil wp_die tanpa sanitasi fallback array
$metaContent = file_get_contents($theme . "/inc/meta.php");
$check("meta.php punya parser tiers fleksibel", str_contains($metaContent, "sgi_parse_tiers_input"));

// Guardrail: seluruh file PHP di theme harus valid secara sintaks (php -l)
$phpLintOk = true;
$phpLintErr = "";
foreach ($files as $f) {
  if (strtolower(pathinfo($f, PATHINFO_EXTENSION)) === "php") {
    $out = [];
    $code = 0;
    exec("php -l " . escapeshellarg($f) . " 2>&1", $out, $code);
    if ($code !== 0) {
      $phpLintOk = false;
      $phpLintErr = implode(" ", $out);
      break;
    }
  }
}
$check("seluruh file PHP theme lolos sintaks lint php -l", $phpLintOk, $phpLintErr);

if ($fail) {
  echo 'GAGAL: ' . implode(', ', $fail) . PHP_EOL;
  exit(1);
}
echo 'OK: aturan mekanis lolos.' . PHP_EOL;
