<?php
// layanan/ai-solution.php - render dari JSON (service:ai-solution).
require_once dirname(__DIR__) . '/includes/store.php';
$lang = cms_lang();
$settings = cms_settings();
$svc = cms_service('ai-solution');
$title = (cms_b($svc['title'] ?? '', $lang) ?: 'AI Implementation') . ' - ' . ($settings['name'] ?? '');
$desc = cms_b($svc['meta_desc'] ?? '', $lang);
$active = 'layanan';
$base = '../';
$contentFile = dirname(__DIR__) . '/templates/content-service.php';
include dirname(__DIR__) . '/templates/layout.php';
