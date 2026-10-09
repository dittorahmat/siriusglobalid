<?php
// templates/content-layanan.php - Variabel: $page (layanan.json), $lang, $base.
$L = $lang;
?>
<section class="page-hero"><div class="container">
  <p class="breadcrumb"><a href="<?php echo $base; ?>index.php" data-i18n="common.home"><?php echo cms_t('common.home', $L); ?></a> / <span data-i18n="nav.services"><?php echo cms_t('nav.services', $L); ?></span></p>
  <span class="eyebrow" data-i18n="svc.hero_eye"><?php echo cms_t('svc.hero_eye', $L); ?></span>
  <h1 data-i18n-html="svc.hero_title_html"><?php echo cms_html(cms_t('svc.hero_title_html', $L)); ?></h1>
</div></section>
<section class="section"><div class="container grid services-grid">
  <?php foreach (($page['cards'] ?? []) as $c): ?>
  <article class="card reveal"><div class="card-icon<?php echo htmlspecialchars($c['cls'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($c['icon'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div><h3><?php echo cms_e($c['title'] ?? '', $L); ?></h3><p class="small muted"><?php echo cms_e($c['desc'] ?? '', $L); ?></p><ul class="check-list small" style="margin-top:12px"><?php foreach (($c['points'] ?? []) as $p): ?><li>✓ <?php echo cms_e($p, $L); ?></li><?php endforeach; ?></ul><p style="margin-top:14px"><a class="btn btn-ghost btn-sm" href="<?php echo $base . htmlspecialchars($c['href'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"><?php echo cms_e($c['btn'] ?? '', $L); ?> →</a></p></article>
  <?php endforeach; ?>
  <?php $cta = $page['cta'] ?? []; ?>
  <article class="card reveal" style="background:var(--navy-900);color:#fff;border-color:var(--navy-900)"><h3 style="color:#fff"><?php echo cms_e($cta['t'] ?? '', $L); ?></h3><p class="small" style="color:#c6d4ee"><?php echo cms_e($cta['d'] ?? '', $L); ?></p><p style="margin-top:14px"><a class="btn btn-teal btn-sm" href="<?php echo $base . htmlspecialchars($cta['href'] ?? 'kontak.php', ENT_QUOTES, 'UTF-8'); ?>" data-i18n="nav.cta"><?php echo cms_t('nav.cta', $L); ?></a></p></article>
</div></section>
<section class="section section-soft"><div class="container">
  <h2 class="reveal"><?php echo cms_e($page['models_heading'] ?? '', $L); ?></h2>
  <p class="muted reveal" style="margin-top:10px;max-width:62ch"><?php echo cms_e($page['models_body'] ?? '', $L); ?></p>
  <div class="grid grid-3" style="margin-top:24px">
    <?php foreach (($page['models'] ?? []) as $m): ?>
    <div class="card reveal"><h3><?php echo cms_e($m['t'] ?? '', $L); ?></h3><p class="small muted"><?php echo cms_e($m['d'] ?? '', $L); ?></p></div>
    <?php endforeach; ?>
  </div>
</div></section>
