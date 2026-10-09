<?php
// templates/content-home.php - Variabel: $home, $lang, $base.
$L = $lang;
$hp = $home['hero_photo'] ?? [];
$kpis = $home['kpi_counts'] ?? ['18', '9', '40'];
$stats = $home['stat_counts'] ?? ['120', '40', '9', '60'];
$clients = $home['clients'] ?? [];
$caseImgs = array_slice($home['case_imgs'] ?? [], 0, 3);
$svcCards = [
  ['icon' => '◈', 'cls' => '', 't' => 'svc.app_t', 'd' => 'svc.app_d', 'href' => 'layanan/app-development.php'],
  ['icon' => '✦', 'cls' => ' teal', 't' => 'svc.ai_t', 'd' => 'svc.ai_d', 'href' => 'layanan/ai-solution.php'],
  ['icon' => '⬡', 'cls' => '', 't' => 'svc.iot_t', 'd' => 'svc.iot_d', 'href' => 'layanan/iot.php'],
  ['icon' => '▣', 'cls' => ' teal', 't' => 'svc.odoo_t', 'd' => 'svc.odoo_d', 'href' => 'layanan/odoo-erp.php'],
  ['icon' => '⬢', 'cls' => '', 't' => 'svc.fleet_t', 'd' => 'svc.fleet_d', 'href' => 'layanan/fleet-management.php'],
];
$cases = [
  ['c' => 'case.1c', 't' => 'case.1t', 'd' => 'case.1d'],
  ['c' => 'case.2c', 't' => 'case.2t', 'd' => 'case.2d'],
  ['c' => 'case.3c', 't' => 'case.3t', 'd' => 'case.3d'],
];
?>
<section class="hero">
  <div class="container hero-grid">
    <div>
      <span class="eyebrow" data-i18n="hero.eyebrow"><?php echo cms_t('hero.eyebrow', $L); ?></span>
      <h1 data-i18n-html="hero.title_html"><?php echo cms_html(cms_t('hero.title_html', $L), $L) ?: 'Aplikasi, AI dan IoT <span>siap produksi</span> untuk bisnis Anda'; ?></h1>
      <p class="lead" style="margin-top:16px" data-i18n="hero.lead"><?php echo cms_t('hero.lead', $L); ?></p>
      <div class="hero-cta">
        <a class="btn btn-primary" href="<?php echo $base; ?>kontak.php" data-i18n="hero.cta1"><?php echo cms_t('hero.cta1', $L); ?></a>
        <a class="btn btn-ghost" href="<?php echo $base; ?>layanan.php" data-i18n="hero.cta2"><?php echo cms_t('hero.cta2', $L); ?></a>
      </div>
    </div>
    <div class="hero-visual">
      <img class="photo" src="<?php echo htmlspecialchars($hp['src'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo cms_a($hp['alt'] ?? '', $L); ?>" width="<?php echo (int)($hp['w'] ?? 880); ?>" height="<?php echo (int)($hp['h'] ?? 1000); ?>" fetchpriority="high">
      <div class="hero-photo-card">
        <b data-i18n="hero.card1t"><?php echo cms_t('hero.card1t', $L); ?></b>
        <div class="small muted" data-i18n="hero.card1a"><?php echo cms_t('hero.card1a', $L); ?></div>
        <div class="small muted" data-i18n="hero.card1b"><?php echo cms_t('hero.card1b', $L); ?></div>
        <div class="small muted" data-i18n="hero.card1c"><?php echo cms_t('hero.card1c', $L); ?></div>
      </div>
      <div class="dash-card">
        <div class="kpi-row">
          <div class="kpi"><b><span data-count="<?php echo (int)($kpis[0] ?? 18); ?>">0</span>+</b><span data-i18n="hero.card2a"><?php echo cms_t('hero.card2a', $L); ?></span></div>
          <div class="kpi"><b><span data-count="<?php echo (int)($kpis[1] ?? 9); ?>">0</span></b><span data-i18n="hero.card2b"><?php echo cms_t('hero.card2b', $L); ?></span></div>
          <div class="kpi"><b><span data-count="<?php echo (int)($kpis[2] ?? 40); ?>">0</span>+</b><span data-i18n="hero.card2c"><?php echo cms_t('hero.card2c', $L); ?></span></div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="section section-soft">
  <div class="container">
    <div class="stat-band reveal">
      <div class="stat"><b><span data-count="<?php echo (int)($stats[0] ?? 120); ?>">0</span>+</b><span data-i18n="stats.1"><?php echo cms_t('stats.1', $L); ?></span></div>
      <div class="stat"><b><span data-count="<?php echo (int)($stats[1] ?? 40); ?>">0</span>+</b><span data-i18n="stats.2"><?php echo cms_t('stats.2', $L); ?></span></div>
      <div class="stat"><b><span data-count="<?php echo (int)($stats[2] ?? 9); ?>">0</span></b><span data-i18n="stats.3"><?php echo cms_t('stats.3', $L); ?></span></div>
      <div class="stat"><b><span data-count="<?php echo (int)($stats[3] ?? 60); ?>">0</span>+</b><span data-i18n="stats.4"><?php echo cms_t('stats.4', $L); ?></span></div>
    </div>
    <p class="small muted reveal" style="margin-top:26px;font-weight:700;letter-spacing:.06em;text-transform:uppercase" data-i18n="clients.t"><?php echo cms_t('clients.t', $L); ?></p>
    <div class="logo-strip reveal"><?php foreach ($clients as $c): ?><span><?php echo cms_e($c, $L); ?></span><?php endforeach; ?></div>
  </div>
</section>

<section class="section" id="layanan">
  <div class="container">
    <span class="eyebrow reveal" data-i18n="sec.services_eye"><?php echo cms_t('sec.services_eye', $L); ?></span>
    <h2 class="reveal" data-i18n="sec.services_t"><?php echo cms_t('sec.services_t', $L); ?></h2>
    <p class="lead reveal" style="margin-top:12px" data-i18n="sec.services_d"><?php echo cms_t('sec.services_d', $L); ?></p>
    <div class="grid services-grid" style="margin-top:30px">
      <?php foreach ($svcCards as $sc): ?>
      <article class="card reveal">
        <div class="card-icon<?php echo $sc['cls']; ?>"><?php echo $sc['icon']; ?></div>
        <h3 data-i18n="<?php echo $sc['t']; ?>"><?php echo cms_t($sc['t'], $L); ?></h3>
        <p data-i18n="<?php echo $sc['d']; ?>"><?php echo cms_t($sc['d'], $L); ?></p>
        <p style="margin-top:14px"><a class="link-more" href="<?php echo $base . $sc['href']; ?>">→ <span data-i18n="svc.detail"><?php echo cms_t('svc.detail', $L); ?></span></a></p>
      </article>
      <?php endforeach; ?>
      <article class="card reveal" style="background:var(--bg-soft)">
        <h3><?php echo $L === 'en' ? 'Need a combination?' : 'Butuh kombinasi?'; ?></h3>
        <p><?php echo $L === 'en' ? 'Example: Odoo + fleet GPS + AI maintenance prediction. We design the architecture.' : 'Contoh: Odoo + GPS armada + AI prediksi maintenance. Kami rancang arsitekturnya.'; ?></p>
        <p style="margin-top:14px"><a class="btn btn-primary btn-sm" href="<?php echo $base; ?>kontak.php" data-i18n="sec.all"><?php echo cms_t('sec.all', $L); ?></a></p>
      </article>
    </div>
  </div>
</section>

<section class="section section-soft">
  <div class="container split">
    <div>
      <h2 class="reveal" data-i18n="sec.why_t"><?php echo cms_t('sec.why_t', $L); ?></h2>
      <div class="grid grid-2" style="margin-top:26px">
        <?php for ($i = 1; $i <= 4; $i++): ?>
        <div class="reveal"><h3 data-i18n="why.<?php echo $i; ?>t"><?php echo cms_t("why.{$i}t", $L); ?></h3><p class="muted small" data-i18n="why.<?php echo $i; ?>d"><?php echo cms_t("why.{$i}d", $L); ?></p></div>
        <?php endfor; ?>
      </div>
    </div>
    <div class="panel-img reveal">
      <div class="mini-metric"><span class="small muted">Closing keuangan</span><b>3× lebih cepat</b></div>
      <div class="mini-metric"><span class="small muted">Akurasi QC vision</span><b>97,4%</b></div>
      <div class="mini-metric"><span class="small muted">BBM armada</span><b>−11% dalam 6 bulan</b></div>
      <p class="small muted">Angka placeholder dari proyek tipikal. Diganti data asli saat tersedia.</p>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <h2 class="reveal" data-i18n="sec.proc_t"><?php echo cms_t('sec.proc_t', $L); ?></h2>
    <div class="grid steps" style="margin-top:28px">
      <?php for ($i = 1; $i <= 4; $i++): ?>
      <div class="card step reveal"><h3 data-i18n="proc.<?php echo $i; ?>t"><?php echo cms_t("proc.{$i}t", $L); ?></h3><p data-i18n="proc.<?php echo $i; ?>d"><?php echo cms_t("proc.{$i}d", $L); ?></p></div>
      <?php endfor; ?>
    </div>
  </div>
</section>

<section class="section section-soft">
  <div class="container">
    <span class="eyebrow reveal" data-i18n="sec.cases_eye"><?php echo cms_t('sec.cases_eye', $L); ?></span>
    <h2 class="reveal" data-i18n="sec.cases_t"><?php echo cms_t('sec.cases_t', $L); ?></h2>
    <div class="grid cases" style="margin-top:28px">
      <?php foreach ($cases as $idx => $cs): $im = $caseImgs[$idx] ?? []; ?>
      <article class="card case-card reveal">
        <div class="case-top"><img src="<?php echo htmlspecialchars($im['img'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo cms_a($im['alt'] ?? '', $L); ?>" width="640" height="400" loading="lazy"></div>
        <div class="case-body"><p class="case-cat" data-i18n="<?php echo $cs['c']; ?>"><?php echo cms_t($cs['c'], $L); ?></p><h3 data-i18n="<?php echo $cs['t']; ?>"><?php echo cms_t($cs['t'], $L); ?></h3><p class="muted small" data-i18n="<?php echo $cs['d']; ?>"><?php echo cms_t($cs['d'], $L); ?></p></div>
      </article>
      <?php endforeach; ?>
    </div>
    <p style="margin-top:22px"><a class="btn btn-ghost" href="<?php echo $base; ?>portofolio.php" data-i18n="sec.cases_all"><?php echo cms_t('sec.cases_all', $L); ?></a></p>
  </div>
</section>

<section class="section">
  <div class="container">
    <h2 class="reveal" data-i18n="sec.testi_t"><?php echo cms_t('sec.testi_t', $L); ?></h2>
    <div class="grid grid-2" style="margin-top:26px">
      <blockquote class="quote reveal"><p data-i18n="t.1"><?php echo cms_t('t.1', $L); ?></p><cite data-i18n="t.1n"><?php echo cms_t('t.1n', $L); ?></cite></blockquote>
      <blockquote class="quote reveal"><p data-i18n="t.2"><?php echo cms_t('t.2', $L); ?></p><cite data-i18n="t.2n"><?php echo cms_t('t.2n', $L); ?></cite></blockquote>
    </div>
    <div class="cta-band reveal" style="margin-top:40px">
      <div><h2 data-i18n="cta.t"><?php echo cms_t('cta.t', $L); ?></h2><p style="margin-top:8px" data-i18n="cta.d"><?php echo cms_t('cta.d', $L); ?></p></div>
      <div style="display:flex;gap:12px;flex-wrap:wrap">
        <a class="btn btn-primary" href="<?php echo $base; ?>kontak.php" data-i18n="cta.b1"><?php echo cms_t('cta.b1', $L); ?></a>
        <a class="btn btn-ghost" href="<?php echo $base; ?>kontak.php" data-i18n="cta.b2"><?php echo cms_t('cta.b2', $L); ?></a>
      </div>
    </div>
  </div>
</section>
