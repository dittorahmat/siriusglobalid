<?php
// templates/content-service.php - renderer halaman detail layanan. Variabel: $svc (service json), $lang, $base.
$L = $lang;
$need = 'kontak.php?layanan=' . urlencode($svc['slug'] ?? '');
if (($base ?? './') !== './') $need = '../' . $need;
?>
<section class="page-hero"><div class="container">
<p class="breadcrumb"><a href="<?php echo $base; ?>index.php"><?php echo $L === 'en' ? 'Home' : 'Beranda'; ?></a> / <a href="<?php echo $base; ?>layanan.php"><?php echo $L === 'en' ? 'Services' : 'Layanan'; ?></a> / <?php echo cms_e($svc['title'] ?? '', $L); ?></p>
<span class="eyebrow"><?php echo $L === 'en' ? 'Services' : 'Layanan'; ?></span><h1><?php echo cms_e($svc['title'] ?? '', $L); ?></h1>
<p class="lead" style="margin-top:12px"><?php echo cms_e($svc['lead'] ?? '', $L); ?></p>
<div style="margin-top:20px;display:flex;gap:12px;flex-wrap:wrap">
  <a class="btn btn-primary" href="<?php echo htmlspecialchars($need, ENT_QUOTES, 'UTF-8'); ?>"><?php echo cms_e($svc['hero_cta'][0]['text'] ?? ['id' => 'Konsultasi Gratis', 'en' => 'Free Consultation'], $L); ?></a>
  <a class="btn btn-ghost" href="<?php echo $base; ?>portofolio.php"><?php echo $L === 'en' ? 'View Case Studies' : 'Lihat Studi Kasus'; ?></a>
</div>
</div></section>
<?php $intro = $svc['intro'] ?? []; $hasList = !empty($intro['checklist']); $hasMetrics = !empty($svc['metrics']); ?>
<?php if ($hasList || $hasMetrics): ?>
<section class="section"><div class="container split"><div>
<h2><?php echo cms_e($intro['heading'] ?? '', $L); ?></h2>
<?php if (!empty($intro['body']) && cms_b($intro['body'], $L) !== ''): ?><p class="muted" style="margin-top:10px"><?php echo cms_e($intro['body'], $L); ?></p><?php endif; ?>
<?php if ($hasList): ?><ul class="check-list" style="margin-top:18px"><?php foreach ($intro['checklist'] as $li): ?><li>✓ <?php echo cms_e($li, $L); ?></li><?php endforeach; ?></ul><?php endif; ?>
</div><?php if ($hasMetrics): ?><div class="panel-img"><?php foreach ($svc['metrics'] as $mm): ?><div class="mini-metric"><span class="small muted"><?php echo cms_e($mm['label'] ?? '', $L); ?></span><b><?php echo cms_e($mm['value'] ?? '', $L); ?></b></div><?php endforeach; ?></div><?php endif; ?></div></section>
<?php endif; ?>
<?php if (!empty($svc['table'])): $tb = $svc['table']; ?>
<section class="section"><div class="container split"><div>
<h2><?php echo cms_e($intro['heading'] ?? '', $L); ?></h2>
<?php if (cms_b($intro['body'] ?? '', $L) !== ''): ?><p class="muted small" style="margin-top:10px"><?php echo cms_e($intro['body'] ?? '', $L); ?></p><?php endif; ?>
</div></div>
<div class="container" style="margin-top:22px">
<div class="table-wrap reveal" tabindex="0" role="region" aria-label="Tabel risiko dan kontrol keamanan">
<table class="tier-table">
<thead><tr><?php foreach ($tb['head'] as $h): ?><th scope="col"><?php echo cms_e($h, $L); ?></th><?php endforeach; ?></tr></thead>
<tbody>
<?php foreach ($tb['rows'] as $row): ?><tr><?php foreach ($row as $c): ?><td><?php echo cms_e($c, $L); ?></td><?php endforeach; ?></tr><?php endforeach; ?>
</tbody>
</table>
</div>
</div></section>
<?php endif; ?>
<?php if (!empty($svc['pricing']['tiers'])): $pr = $svc['pricing']; $qb = $pr['quote_btn'] ?? ['id' => 'Minta Quotation', 'en' => '']; ?>
<section class="section" style="padding-top:0"><div class="container">
<h2><?php echo cms_e($pr['heading'] ?? '', $L); ?></h2>
<p class="muted" style="margin-top:10px;max-width:62ch"><?php echo cms_e($pr['body'] ?? '', $L); ?></p>
<div class="grid grid-3 pricing-grid" style="margin-top:24px">
<?php $ti = 0; $nTiers = count($pr['tiers']); foreach ($pr['tiers'] as $t): $feat = array_key_exists('featured', $t) ? !empty($t['featured']) : ($ti === 1 && $nTiers === 3); $ti++; ?>
<article class="card price-card<?php echo $feat ? ' featured' : ''; ?> reveal"><p class="price-tier"><?php echo cms_e($t['tier'] ?? '', $L); ?></p><h3><?php echo cms_e($t['name'] ?? '', $L); ?></h3><p class="price-value"><?php echo cms_e($t['value'] ?? '', $L); ?></p><ul><?php foreach ($t['items'] ?? [] as $it): ?><li>✓ <?php echo cms_e($it, $L); ?></li><?php endforeach; ?></ul><p style="margin-top:6px"><a class="btn <?php echo $feat ? 'btn-teal' : 'btn-ghost'; ?> btn-sm" href="<?php echo htmlspecialchars($need, ENT_QUOTES, 'UTF-8'); ?>"><?php echo cms_e($qb, $L); ?></a></p></article>
<?php endforeach; ?>
</div>
</div></section>
<?php endif; ?>
<?php if (!empty($svc['steps']['items'])): ?>
<section class="section" style="padding-top:0"><div class="container">
<h2><?php echo cms_e($svc['steps']['heading'] ?? '', $L); ?></h2>
<div class="grid grid-3" style="margin-top:24px">
<?php foreach ($svc['steps']['items'] as $st): ?><div class="card reveal"><h3><?php echo cms_e($st['t'] ?? '', $L); ?></h3><p class="small muted"><?php echo cms_e($st['d'] ?? '', $L); ?></p></div><?php endforeach; ?>
</div>
</div></section>
<?php endif; ?>
<section class="section section-soft"><div class="container"><h2><?php echo $L === 'en' ? 'Short FAQ' : 'FAQ singkat'; ?></h2><div style="margin-top:18px;max-width:760px">
<?php foreach ($svc['faqs'] ?? [] as $f): ?><div class="faq"><button aria-expanded="false"><?php echo cms_e($f['q'] ?? '', $L); ?> <span>+</span></button><div class="panel"><div><?php echo cms_e($f['a'] ?? '', $L); ?></div></div></div><?php endforeach; ?>
</div><?php $cta = $svc['cta'] ?? []; if (!empty($cta)): ?><div class="cta-band" style="margin-top:36px"><div><h2><?php echo cms_e($cta['t'] ?? '', $L); ?></h2><p><?php echo cms_e($cta['d'] ?? '', $L); ?></p></div><a class="btn btn-primary" href="<?php echo htmlspecialchars($cta['href'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"><?php echo cms_e($cta['btn'] ?? '', $L); ?></a></div><?php endif; ?></div></section>
