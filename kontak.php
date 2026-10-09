<?php
// kontak.php - formulir tetap WA deep link (tanpa backend).
require_once __DIR__ . '/includes/store.php';
$lang = cms_lang();
$settings = cms_settings();
$page = cms_page('kontak');
$title = ($lang === 'en' ? 'Contact - ' : 'Kontak - ') . ($settings['name'] ?? '');
$desc = cms_b($page['meta_desc'] ?? '', $lang);
$active = 'kontak';
$contentFile = __DIR__ . '/templates/content-kontak.php';
include __DIR__ . '/templates/layout.php';
