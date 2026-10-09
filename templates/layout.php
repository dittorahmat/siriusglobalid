<?php
// templates/layout.php - kerangka halaman publik.
// Variabel wajib: $title, $desc, $active, $contentFile (path partial konten).
// Variabel opsional: $lang, $settings, $full (footer penuh), $base ('./' atau '../').
declare(strict_types=1);
$lang = $lang ?? cms_lang();
$settings = $settings ?? cms_settings();
$base = $base ?? './';
$full = $full ?? true;
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($title ?? $settings['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></title>
  <?php if (!empty($desc)): ?><meta name="description" content="<?php echo htmlspecialchars($desc, ENT_QUOTES, 'UTF-8'); ?>"><?php endif; ?>
  <meta property="og:title" content="<?php echo htmlspecialchars($title ?? '', ENT_QUOTES, 'UTF-8'); ?>">
  <meta property="og:type" content="website">
  <link rel="icon" type="image/svg+xml" href="<?php echo $base; ?>assets/favicon.svg">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo $base; ?>css/tokens.css">
  <link rel="stylesheet" href="<?php echo $base; ?>css/base.css">
  <link rel="stylesheet" href="<?php echo $base; ?>css/components.css">
  <link rel="stylesheet" href="<?php echo $base; ?>css/pages.css">
  <noscript><style>.reveal{opacity:1!important;transform:none!important}</style></noscript>
</head>
<body>
  <?php include __DIR__ . '/header.php'; ?>
  <main id="main">
    <?php include $contentFile; ?>
  </main>
  <?php include __DIR__ . '/footer.php'; ?>
  <script>
    window.SGI_SETTINGS = <?php echo json_encode([
      'phoneHref' => $settings['phoneHref'] ?? '',
    ], JSON_UNESCAPED_SLASHES); ?>;
    window.SGI_I18N = <?php
      $dId = cms_load('i18n.id'); $dEn = cms_load('i18n.en');
      echo json_encode(['id' => ($dId['keys'] ?? []), 'en' => ($dEn['keys'] ?? [])],
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    ?>;
  </script>
  <script src="<?php echo $base; ?>js/site-config.js"></script>
  <script src="<?php echo $base; ?>js/i18n.js"></script>
  <script src="<?php echo $base; ?>js/main.js"></script>
</body>
</html>
