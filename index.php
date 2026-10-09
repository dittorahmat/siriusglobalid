<?php
// index.php - Beranda (render dari JSON, visual identik dengan index.html).
require_once __DIR__ . '/includes/store.php';
$lang = cms_lang();
$settings = cms_settings();
$home = cms_page('home');
$title = cms_t('meta.home_title', $lang);
$desc = cms_b($home['meta_desc'] ?? '', $lang);
$active = 'home';
$contentFile = __DIR__ . '/templates/content-home.php';
include __DIR__ . '/templates/layout.php';
