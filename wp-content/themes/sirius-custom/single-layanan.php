<?php
// single-layanan.php - port templates/content-service.php, ID-only dinamis.
// Semua layanan (9 seed + tambahan editor) memakai template yang sama.
get_header();
$id = get_the_ID();
$title = get_the_title();
$lead = get_post_meta($id, '_sgi_lead', true);
$h1 = get_post_meta($id, '_sgi_h1', true) ?: $title;
$intro_h = get_post_meta($id, '_sgi_intro_heading', true);
$intro_b = get_post_meta($id, '_sgi_intro_body', true);
$checklist = array_values(array_filter(array_map('trim', explode("\n", (string)get_post_meta($id, '_sgi_checklist', true)))));
$metrics_raw = array_values(array_filter(array_map('trim', explode("\n", (string)get_post_meta($id, '_sgi_metrics', true)))));
$metrics = [];
foreach ($metrics_raw as $ln) {
  $parts = array_map('trim', explode('|', $ln, 2));
  $metrics[] = ['label' => $parts[0], 'value' => $parts[1] ?? ''];
}
$pr_h = get_post_meta($id, '_sgi_pricing_heading', true);
$pr_b = get_post_meta($id, '_sgi_pricing_body', true);
$pr_q = get_post_meta($id, '_sgi_quote_btn', true) ?: 'Minta Quotation';
$tiers = json_decode((string)get_post_meta($id, '_sgi_tiers', true), true) ?: [];
$steps_h = get_post_meta($id, '_sgi_steps_heading', true);
$steps_raw = array_values(array_filter(array_map('trim', explode("\n", (string)get_post_meta($id, '_sgi_steps', true)))));
$faqs_raw = array_values(array_filter(array_map('trim', explode("\n", (string)get_post_meta($id, '_sgi_faqs', true)))));
$cta_t = get_post_meta($id, '_sgi_cta_title', true);
$cta_d = get_post_meta($id, '_sgi_cta_desc', true);
$cta_b = get_post_meta($id, '_sgi_cta_btn', true) ?: 'Konsultasi Gratis';
$cta_href = get_post_meta($id, '_sgi_cta_href', true) ?: home_url('/kontak/?layanan=' . get_post_field('post_name'));
$need = home_url('/kontak/?layanan=' . get_post_field('post_name'));
$nTiers = count($tiers);
?>
<section class="page-hero"><div class="container">
<p class="breadcrumb"><a href="<?php echo esc_url(home_url('/')); ?>">Beranda</a> / <a href="<?php echo esc_url(home_url('/layanan/')); ?>">Layanan</a> / <?php echo esc_html($title); ?></p>
<span class="eyebrow">Layanan</span><h1><?php echo esc_html($h1); ?></h1>
<p class="lead" style="margin-top:12px"><?php echo esc_html($lead); ?></p>
<div style="margin-top:20px;display:flex;gap:12px;flex-wrap:wrap">
  <a class="btn btn-primary" href="<?php echo esc_url($need); ?>">Konsultasi Gratis</a>
  <a class="btn btn-ghost" href="<?php echo esc_url(home_url('/portofolio/')); ?>">Lihat Studi Kasus</a>
</div>
</div></section>
<?php if ($checklist || $metrics): ?>
<section class="section"><div class="container split"><div>
<h2><?php echo esc_html($intro_h); ?></h2>
<?php if ($intro_b !== ''): ?><p class="muted" style="margin-top:10px"><?php echo esc_html($intro_b); ?></p><?php endif; ?>
<?php if ($checklist): ?><ul class="check-list" style="margin-top:18px"><?php foreach ($checklist as $li): ?><li>✓ <?php echo esc_html($li); ?></li><?php endforeach; ?></ul><?php endif; ?>
</div><?php if ($metrics): ?><div class="panel-img"><?php foreach ($metrics as $mm): ?><div class="mini-metric"><span class="small muted"><?php echo esc_html($mm['label']); ?></span><b><?php echo esc_html($mm['value']); ?></b></div><?php endforeach; ?></div><?php endif; ?></div></section>
<?php endif; ?>
<?php if ($tiers): ?>
<section class="section" style="padding-top:0"><div class="container">
<h2><?php echo esc_html($pr_h); ?></h2>
<p class="muted" style="margin-top:10px;max-width:62ch"><?php echo esc_html($pr_b); ?></p>
<div class="grid grid-3 pricing-grid" style="margin-top:24px">
<?php $ti = 0; foreach ($tiers as $t): $feat = array_key_exists('featured', $t) ? !empty($t['featured']) : ($ti === 1 && $nTiers === 3); $ti++; ?>
<article class="card price-card<?php echo $feat ? ' featured' : ''; ?> reveal"><p class="price-tier"><?php echo esc_html($t['tier'] ?? ''); ?></p><h3><?php echo esc_html($t['name'] ?? ''); ?></h3><p class="price-value"><?php echo esc_html($t['value'] ?? ''); ?></p><ul><?php foreach ($t['items'] ?? [] as $it): ?><li>✓ <?php echo esc_html(is_array($it) ? ($it['id'] ?? '') : $it); ?></li><?php endforeach; ?></ul><p style="margin-top:6px"><a class="btn <?php echo $feat ? 'btn-teal' : 'btn-ghost'; ?> btn-sm" href="<?php echo esc_url($need); ?>"><?php echo esc_html($pr_q); ?></a></p></article>
<?php endforeach; ?>
</div>
</div></section>
<?php endif; ?>
<?php if ($steps_raw): ?>
<section class="section" style="padding-top:0"><div class="container">
<h2><?php echo esc_html($steps_h); ?></h2>
<div class="grid grid-3" style="margin-top:24px">
<?php foreach ($steps_raw as $ln): $p = array_map('trim', explode('|', $ln, 2)); ?>
<div class="card reveal"><h3><?php echo esc_html($p[0]); ?></h3><p class="small muted"><?php echo esc_html($p[1] ?? ''); ?></p></div>
<?php endforeach; ?>
</div>
</div></section>
<?php endif; ?>
<section class="section section-soft"><div class="container"><h2>FAQ singkat</h2><div style="margin-top:18px;max-width:760px">
<?php foreach ($faqs_raw as $ln): $p = array_map('trim', explode('|', $ln, 2)); ?>
<div class="faq"><button aria-expanded="false"><?php echo esc_html($p[0]); ?> <span>+</span></button><div class="panel"><div><?php echo esc_html($p[1] ?? ''); ?></div></div></div>
<?php endforeach; ?>
</div><?php if ($cta_t !== ''): ?><div class="cta-band" style="margin-top:36px"><div><h2><?php echo esc_html($cta_t); ?></h2><p><?php echo esc_html($cta_d); ?></p></div><a class="btn btn-primary" href="<?php echo esc_url($cta_href); ?>"><?php echo esc_html($cta_b); ?></a></div><?php endif; ?></div></section>
<?php get_footer(); ?>
