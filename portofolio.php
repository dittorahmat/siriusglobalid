<?php
// portofolio.php - render dari JSON.
require_once __DIR__ . '/includes/store.php';
$lang = cms_lang();
$settings = cms_settings();
$pf = cms_page('portfolio');
$title = ($lang === 'en' ? 'Portfolio - ' : 'Portofolio - ') . ($settings['name'] ?? '');
$desc = cms_b($pf['meta_desc'] ?? '', $lang);
$active = 'portofolio';
$contentFile = __DIR__ . '/templates/content-portfolio.php';
include __DIR__ . '/templates/layout.php';
