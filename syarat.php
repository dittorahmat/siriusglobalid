<?php
// syarat.php - render dari JSON.
require_once __DIR__ . '/includes/store.php';
$lang = cms_lang();
$settings = cms_settings();
$page = cms_page('syarat');
$title = ($lang === 'en' ? 'Terms of Service - ' : 'Syarat Layanan - ') . ($settings['name'] ?? '');
$desc = cms_b($page['meta_desc'] ?? '', $lang);
$active = '';
$contentFile = __DIR__ . '/templates/content-doc.php';
include __DIR__ . '/templates/layout.php';
