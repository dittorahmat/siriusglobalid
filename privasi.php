<?php
// privasi.php - render dari JSON.
require_once __DIR__ . '/includes/store.php';
$lang = cms_lang();
$settings = cms_settings();
$page = cms_page('privasi');
$title = ($lang === 'en' ? 'Privacy Policy - ' : 'Kebijakan Privasi - ') . ($settings['name'] ?? '');
$desc = cms_b($page['meta_desc'] ?? '', $lang);
$active = '';
$contentFile = __DIR__ . '/templates/content-doc.php';
include __DIR__ . '/templates/layout.php';
