<?php
// tentang.php - render dari JSON.
require_once __DIR__ . '/includes/store.php';
$lang = cms_lang();
$settings = cms_settings();
$page = cms_page('tentang');
$title = ($lang === 'en' ? 'About - ' : 'Tentang - ') . ($settings['name'] ?? '');
$desc = cms_b($page['meta_desc'] ?? '', $lang);
$active = 'tentang';
$contentFile = __DIR__ . '/templates/content-tentang.php';
include __DIR__ . '/templates/layout.php';
