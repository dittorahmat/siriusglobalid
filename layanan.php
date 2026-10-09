<?php
// layanan.php - indeks layanan, render dari JSON.
require_once __DIR__ . '/includes/store.php';
$lang = cms_lang();
$settings = cms_settings();
$page = cms_page('layanan');
$title = ($lang === 'en' ? 'Services - ' : 'Layanan - ') . ($settings['name'] ?? '');
$desc = cms_b($page['meta_desc'] ?? '', $lang);
$active = 'layanan';
$contentFile = __DIR__ . '/templates/content-layanan.php';
include __DIR__ . '/templates/layout.php';
