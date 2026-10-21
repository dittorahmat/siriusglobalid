<?php
// front-page.php - renderer beranda ID-only. Seluruh copy dibaca dari opsi
// `sgi_home` (Tampilan > Konten Situs); fallback bawaan dipakai bila opsi kosong.
// Kartu layanan otomatis dari CPT layanan. Urutan tampil + on/off section
// diatur via `home_order`: tiap section di-buffer lalu di-echo sesuai urutan.
get_header();
$h = sgi_home();
$hero = $h['hero']; $cta = $h['cta'];
$order = sgi_home_order($h['home_order'] ?? []);
$hp = $h['hero_photo'];
$kpis = array_pad(array_values((array)$h['kpi_counts']), 3, '0');
$kpiLabels = array_pad(array_values((array)$h['kpi_labels']), 3, '');
$stats = array_pad(array_values((array)$h['stat_counts']), 4, '0');
$statLabels = array_pad(array_values((array)$h['stat_labels']), 4, '');
$clients = $h['clients'];
$caseImgs = array_slice((array)$h['case_imgs'], 0, 6);
$svcCards = sgi_home_service_cards($h);
$showCombo = !in_array((string)($h['layanan_combo'] ?? '1'), ['', '0'], true);
$why = array_values((array)$h['why']);
$steps = array_values((array)$h['steps']);
$cases = array_slice(array_values((array)$h['cases']), 0, 6);
$testis = array_values((array)$h['testis']);
$sec = [];

if (in_array('hero', $order, true)): ob_start(); ?>
<section class="hero">
  <div class="container hero-grid">
    <div>
      <span class="eyebrow"><?php echo esc_html($hero['eyebrow']); ?></span>
      <h1><?php echo wp_kses($hero['title_html'], ['span' => []]); ?></h1>
      <p class="lead" style="margin-top:16px"><?php echo esc_html($hero['lead']); ?></p>
      <div class="hero-cta">
        <a class="btn btn-primary" href="<?php echo esc_url(sgi_home_href((string)$hero['cta1_href'], '/kontak/')); ?>"><?php echo esc_html($hero['cta1']); ?></a>
        <a class="btn btn-ghost" href="<?php echo esc_url(sgi_home_href((string)$hero['cta2_href'], '/layanan/')); ?>"><?php echo esc_html($hero['cta2']); ?></a>
      </div>
    </div>
    <div class="hero-visual">
      <img class="photo" src="<?php echo esc_url($hp['src']); ?>" alt="<?php echo esc_attr($hp['alt']); ?>" width="<?php echo (int)$hp['w']; ?>" height="<?php echo (int)$hp['h']; ?>" fetchpriority="high">
      <div class="hero-photo-card">
        <b><?php echo esc_html($hero['card1t']); ?></b>
        <div class="small muted"><?php echo esc_html($hero['card1a']); ?></div>
        <div class="small muted"><?php echo esc_html($hero['card1b']); ?></div>
        <div class="small muted"><?php echo esc_html($hero['card1c']); ?></div>
      </div>
      <div class="dash-card">
        <div class="kpi-row">
          <div class="kpi"><b><span data-count="<?php echo (int)($kpis[0] ?? 18); ?>">0</span>+</b><span><?php echo esc_html($kpiLabels[0]); ?></span></div>
          <div class="kpi"><b><span data-count="<?php echo (int)($kpis[1] ?? 9); ?>">0</span></b><span><?php echo esc_html($kpiLabels[1]); ?></span></div>
          <div class="kpi"><b><span data-count="<?php echo (int)($kpis[2] ?? 40); ?>">0</span>+</b><span><?php echo esc_html($kpiLabels[2]); ?></span></div>
        </div>
      </div>
    </div>
  </div>
</section>
<?php $sec['hero'] = ob_get_clean(); endif; ?>

<?php if (in_array('stats', $order, true)): ob_start(); ?>
<section class="section section-soft">
  <div class="container">
    <div class="stat-band reveal">
      <div class="stat"><b><span data-count="<?php echo (int)($stats[0] ?? 120); ?>">0</span>+</b><span><?php echo esc_html($statLabels[0]); ?></span></div>
      <div class="stat"><b><span data-count="<?php echo (int)($stats[1] ?? 40); ?>">0</span>+</b><span><?php echo esc_html($statLabels[1]); ?></span></div>
      <div class="stat"><b><span data-count="<?php echo (int)($stats[2] ?? 9); ?>">0</span></b><span><?php echo esc_html($statLabels[2]); ?></span></div>
      <div class="stat"><b><span data-count="<?php echo (int)($stats[3] ?? 60); ?>">0</span>+</b><span><?php echo esc_html($statLabels[3]); ?></span></div>
    </div>
    <p class="small muted reveal" style="margin-top:26px;font-weight:700;letter-spacing:.06em;text-transform:uppercase">Dipercaya berbagai industri</p>
    <div class="logo-strip reveal"><?php foreach ($clients as $c): ?><span><?php echo esc_html(is_array($c) ? ($c['id'] ?? '') : $c); ?></span><?php endforeach; ?></div>
  </div>
</section>
<?php $sec['stats'] = ob_get_clean(); endif; ?>

<?php if (in_array('layanan', $order, true)): ob_start(); ?>
<section class="section" id="layanan">
  <div class="container">
    <span class="eyebrow reveal"><?php echo esc_html($h['svc_eye']); ?></span>
    <h2 class="reveal"><?php echo esc_html($h['svc_t']); ?></h2>
    <p class="lead reveal" style="margin-top:12px"><?php echo esc_html($h['svc_d']); ?></p>
    <div class="grid services-grid" style="margin-top:30px">
      <?php foreach ($svcCards as $sc): ?>
      <article class="card reveal">
        <div class="card-icon<?php echo esc_attr($sc['cls']); ?>"><?php echo esc_html($sc['icon']); ?></div>
        <h3><?php echo esc_html($sc['t']); ?></h3>
        <p><?php echo esc_html($sc['d']); ?></p>
        <p style="margin-top:14px"><a class="link-more" href="<?php echo esc_url($sc['url']); ?>">→ <span>Pelajari detail</span></a></p>
      </article>
      <?php endforeach; ?>
      <?php if ($showCombo): ?>
      <article class="card reveal" style="background:var(--bg-soft)">
        <h3><?php echo esc_html($h['combo_t']); ?></h3>
        <p><?php echo esc_html($h['combo_d']); ?></p>
        <p style="margin-top:14px"><a class="btn btn-primary btn-sm" href="<?php echo esc_url(home_url('/kontak/')); ?>"><?php echo esc_html($h['combo_btn']); ?></a></p>
      </article>
      <?php endif; ?>
    </div>
  </div>
</section>
<?php $sec['layanan'] = ob_get_clean(); endif; ?>

<?php if (in_array('why', $order, true) && $why): ob_start(); ?>
<section class="section section-soft">
  <div class="container split">
    <div>
      <h2 class="reveal"><?php echo esc_html($h['why_t']); ?></h2>
      <div class="grid grid-2" style="margin-top:26px">
        <?php foreach ($why as $w): ?>
        <div class="reveal"><h3><?php echo esc_html($w['t'] ?? ''); ?></h3><p class="muted small"><?php echo esc_html($w['d'] ?? ''); ?></p></div>
        <?php endforeach; ?>
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
<?php $sec['why'] = ob_get_clean(); endif; ?>

<?php if (in_array('alur', $order, true) && $steps): ob_start(); ?>
<section class="section">
  <div class="container">
    <h2 class="reveal"><?php echo esc_html($h['proc_t']); ?></h2>
    <div class="grid steps" style="margin-top:28px">
      <?php foreach ($steps as $st): ?>
      <div class="card step reveal"><h3><?php echo esc_html($st['t'] ?? ''); ?></h3><p><?php echo esc_html($st['d'] ?? ''); ?></p></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php $sec['alur'] = ob_get_clean(); endif; ?>

<?php if (in_array('cases', $order, true) && $cases): ob_start(); ?>
<section class="section section-soft">
  <div class="container">
    <span class="eyebrow reveal"><?php echo esc_html($h['cases_eye']); ?></span>
    <h2 class="reveal"><?php echo esc_html($h['cases_t']); ?></h2>
    <div class="grid cases" style="margin-top:28px">
      <?php foreach ($cases as $idx => $cs): $im = $caseImgs[$idx] ?? []; ?>
      <article class="card case-card reveal">
        <div class="case-top"><img src="<?php echo esc_url(is_array($im) ? ($im['img'] ?? '') : ''); ?>" alt="<?php echo esc_attr(is_array($im) && isset($im['alt']) ? sgi_id($im['alt']) : ''); ?>" width="640" height="400" loading="lazy"></div>
        <div class="case-body"><p class="case-cat"><?php echo esc_html($cs['c'] ?? ''); ?></p><h3><?php echo esc_html($cs['t'] ?? ''); ?></h3><p class="muted small"><?php echo esc_html($cs['d'] ?? ''); ?></p></div>
      </article>
      <?php endforeach; ?>
    </div>
    <p style="margin-top:22px"><a class="btn btn-ghost" href="<?php echo esc_url(home_url('/portofolio/')); ?>"><?php echo esc_html($h['cases_all']); ?></a></p>
  </div>
</section>
<?php $sec['cases'] = ob_get_clean(); endif; ?>

<?php
// Testimoni + CTA berbagi satu <section> seperti markup asli. Isi digabung
// mengikuti posisi keduanya di home_order.
$testiHtml = '';
if (in_array('testi', $order, true) && $testis): ob_start(); ?>
    <h2 class="reveal"><?php echo esc_html($h['testi_t']); ?></h2>
    <div class="grid grid-2" style="margin-top:26px">
      <?php foreach ($testis as $t): ?>
      <blockquote class="quote reveal"><p><?php echo esc_html($t['q'] ?? ''); ?></p><cite><?php echo esc_html($t['by'] ?? ''); ?></cite></blockquote>
      <?php endforeach; ?>
    </div>
<?php $testiHtml = ob_get_clean(); endif;
$ctaHtml = '';
if (in_array('cta', $order, true)): ob_start(); ?>
    <div class="cta-band reveal" style="margin-top:40px">
      <div><h2><?php echo esc_html($cta['t']); ?></h2><p style="margin-top:8px"><?php echo esc_html($cta['d']); ?></p></div>
      <div style="display:flex;gap:12px;flex-wrap:wrap">
        <a class="btn btn-primary" href="<?php echo esc_url(home_url('/kontak/')); ?>"><?php echo esc_html($cta['b1']); ?></a>
        <a class="btn btn-ghost" href="<?php echo esc_url(home_url('/kontak/')); ?>"><?php echo esc_html($cta['b2']); ?></a>
      </div>
    </div>
<?php $ctaHtml = ob_get_clean(); endif;
$tc = '';
foreach ($order as $key) {
  if ($key === 'testi') $tc .= $testiHtml;
  if ($key === 'cta') $tc .= $ctaHtml;
}
if ($tc !== '') $sec['__testi_cta'] = '<section class="section"><div class="container">' . $tc . '</div></section>';

foreach ($order as $key) {
  if ($key === 'testi' || $key === 'cta') {
    if (!isset($sec['__testi_cta'])) continue;
    if (isset($sec['__testi_cta_done'])) continue;
    echo $sec['__testi_cta'];
    $sec['__testi_cta_done'] = true;
    continue;
  }
  echo $sec[$key] ?? '';
}
?>
<?php get_footer(); ?>
