<?php
// layanan/iot.php - render dari JSON (service:iot).
require_once dirname(__DIR__) . '/includes/store.php';
$lang = cms_lang();
$settings = cms_settings();
$svc = cms_service('iot');
$title = (cms_b($svc['title'] ?? '', $lang) ?: 'IoT Solutions') . ' - ' . ($settings['name'] ?? '');
$desc = cms_b($svc['meta_desc'] ?? '', $lang);
$active = 'layanan';
$base = '../';
$contentFile = dirname(__DIR__) . '/templates/content-service.php';
include dirname(__DIR__) . '/templates/layout.php';
