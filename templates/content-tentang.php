<?php
// templates/content-tentang.php - Variabel: $page (tentang.json), $lang, $base.
$L = $lang;
?>
<section class="page-hero"><div class="container">
  <p class="breadcrumb"><a href="<?php echo $base; ?>index.php" data-i18n="common.home"><?php echo cms_t('common.home', $L); ?></a> / <span data-i18n="nav.about"><?php echo cms_t('nav.about', $L); ?></span></p>
  <span class="eyebrow" data-i18n="about.hero_eye"><?php echo cms_t('about.hero_eye', $L); ?></span>
  <h1 data-i18n-html="about.hero_title_html"><?php echo cms_html(cms_t('about.hero_title_html', $L)); ?></h1>
  <p class="lead" style="margin-top:14px" data-i18n="about.hero_lead"><?php echo cms_t('about.hero_lead', $L); ?></p>
</div></section>
<section class="section"><div class="container split">
  <div class="card reveal"><h3 data-i18n="about.m1t"><?php echo cms_t('about.m1t', $L); ?></h3><p class="muted" data-i18n="about.m1d"><?php echo cms_t('about.m1d', $L); ?></p></div>
  <div class="card reveal"><h3 data-i18n="about.m2t"><?php echo cms_t('about.m2t', $L); ?></h3><p class="muted" data-i18n="about.m2d"><?php echo cms_t('about.m2d', $L); ?></p></div>
</div></section>
<section class="section section-soft"><div class="container">
  <h2 class="reveal" data-i18n="about.val_t"><?php echo cms_t('about.val_t', $L); ?></h2>
  <div class="grid grid-4" style="margin-top:24px" id="vals">
    <?php foreach (($page['values'] ?? []) as $v): ?>
    <div class="card reveal"><h3><?php echo cms_e($v['t'] ?? '', $L); ?></h3><p class="small muted"><?php echo cms_e($v['d'] ?? '', $L); ?></p></div>
    <?php endforeach; ?>
  </div>
  <h2 class="reveal" style="margin-top:56px" data-i18n="about.timeline_t"><?php echo cms_t('about.timeline_t', $L); ?></h2>
  <div class="grid grid-3" style="margin-top:20px">
    <?php foreach (($page['timeline'] ?? []) as $t): ?>
    <div class="card reveal"><b><?php echo cms_e($t['t'] ?? '', $L); ?></b><p class="small muted"><?php echo cms_e($t['d'] ?? '', $L); ?></p></div>
    <?php endforeach; ?>
  </div>
  <h2 class="reveal" style="margin-top:56px" data-i18n="about.team_t"><?php echo cms_t('about.team_t', $L); ?></h2>
  <p class="muted reveal" data-i18n="about.team_d"><?php echo cms_t('about.team_d', $L); ?></p>
  <div class="grid grid-4" style="margin-top:20px">
    <?php foreach (($page['team'] ?? []) as $m): ?>
    <div class="card reveal"><h3><?php echo cms_e($m['t'] ?? '', $L); ?></h3><p class="small muted"><?php echo cms_e($m['d'] ?? '', $L); ?></p></div>
    <?php endforeach; ?>
  </div>
  <div class="cta-band reveal" style="margin-top:44px"><div><h2 data-i18n="cta.t"><?php echo cms_t('cta.t', $L); ?></h2></div><a class="btn btn-primary" href="<?php echo $base; ?>kontak.php" data-i18n="cta.b1"><?php echo cms_t('cta.b1', $L); ?></a></div>
</div></section>
