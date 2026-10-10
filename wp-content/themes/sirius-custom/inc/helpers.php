<?php
// sgi_id() - ambil sisi ID dari field bilingual seed {id,en} atau string biasa.
// Importer dan theme ID-only: sisi 'en' selalu diabaikan.
declare(strict_types=1);

function sgi_id($v): string {
  if (is_array($v)) return (string)($v['id'] ?? '');
  if (is_string($v)) return $v;
  return '';
}

function sgi_e($v): void {
  echo esc_html(sgi_id($v));
}

// Batas list (port dari cms_limits(), proteksi layout).
function sgi_limits(): array {
  return [
    'portfolio.items' => 24, 'service.faqs' => 20, 'service.tiers' => 6,
    'service.checklist' => 20, 'service.metrics' => 8, 'service.steps' => 8,
    'tentang.values' => 12, 'tentang.timeline' => 12, 'tentang.team' => 12,
    'home.clients' => 12, 'home.cases' => 6,
  ];
}

function sgi_over_limit(string $key, array $items): ?string {
  $limits = sgi_limits();
  if (isset($limits[$key]) && count($items) > $limits[$key]) {
    return 'Daftar ' . $key . ' maksimal ' . $limits[$key] . ' item (kini ' . count($items) . ').';
  }
  return null;
}

// Slug layanan yang dikenal (port CMS_SERVICE_SLUGS).
function sgi_service_slugs(): array {
  return [
    'app-development', 'ai-solution', 'iot', 'odoo-erp', 'fleet-management',
    'infrastruktur', 'dashboard-bi', 'cybersecurity', 'training',
  ];
}

function sgi_portfolio_cats(): array {
  return ['erp', 'ai', 'fleet', 'app', 'iot', 'infra', 'sec', 'bi'];
}
