<?php
// templates/content-portfolio.php - Variabel: $pf (portfolio.json), $lang, $base.
$L = $lang;
$filters = [
  ['all', 'work.filter_all'], ['erp', 'work.filter_erp'], ['ai', 'work.filter_ai'],
  ['fleet', 'work.filter_fleet'], ['app', 'work.filter_app'], ['iot', 'work.filter_iot'],
  ['infra', 'work.filter_infra'], ['sec', 'work.filter_sec'], ['bi', 'work.filter_bi'],
];
?>
<section class="page-hero"><div class="container">
<p class="breadcrumb"><a href="<?php echo $base; ?>index.php" data-i18n="common.home"><?php echo cms_t('common.home', $L); ?></a> / <span data-i18n="nav.work"><?php echo cms_t('nav.work', $L); ?></span></p>
<span class="eyebrow" data-i18n="work.hero_eye"><?php echo cms_t('work.hero_eye', $L); ?></span>
<h1 data-i18n-html="work.hero_title_html"><?php echo cms_html(cms_t('work.hero_title_html', $L)); ?></h1>
<div class="pills" style="margin-top:20px">
<?php $fi = 0; foreach ($filters as [$f, $k]): $fi++; ?>
<button class="pill<?php echo $fi === 1 ? ' active' : ''; ?>" data-filter="<?php echo $f; ?>" data-i18n="<?php echo $k; ?>"><?php echo cms_t($k, $L); ?></button>
<?php endforeach; ?>
</div></div></section>
<section class="section"><div class="container grid cases">
<?php foreach (($pf['items'] ?? []) as $it): if (trim(cms_b($it['title'] ?? '', $L)) === '' && trim((string)($it['img'] ?? '')) === '') continue; ?>
<article class="card case-card" data-cat="<?php echo htmlspecialchars($it['cat'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"><div class="case-top"><img src="<?php echo htmlspecialchars($it['img'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo cms_a($it['alt'] ?? '', $L); ?>" width="640" height="400" loading="lazy"></div><div class="case-body"><p class="case-cat"><?php echo cms_e($it['cat_t'] ?? '', $L); ?></p><h3><?php echo cms_e($it['title'] ?? '', $L); ?></h3><p class="small muted"><?php echo cms_e($it['desc'] ?? '', $L); ?></p><?php if (!empty($it['results'])): ?><div class="case-result"><?php foreach ($it['results'] as $r): ?><div><b><?php echo cms_e($r['b'] ?? '', $L); ?></b><span class="small muted"><?php echo cms_e($r['s'] ?? '', $L); ?></span></div><?php endforeach; ?></div><?php endif; ?></div></article>
<?php endforeach; ?>
</div><div class="container"><div class="cta-band" style="margin-top:36px"><div><h2 data-i18n="cta.t"><?php echo cms_t('cta.t', $L); ?></h2></div><a class="btn btn-primary" href="<?php echo $base; ?>kontak.php" data-i18n="cta.b1"><?php echo cms_t('cta.b1', $L); ?></a></div></div></section>
