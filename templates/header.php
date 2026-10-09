<?php
// templates/header.php - dipakai semua halaman publik. Variabel: $active, $lang, $settings.
$active = $active ?? '';
$base = $base ?? './';
$settings = $settings ?? cms_settings();
$nav = [
  'home' => ['href' => $base . 'index.php', 'key' => 'nav.home', 'id' => 'Beranda'],
  'tentang' => ['href' => $base . 'tentang.php', 'key' => 'nav.about', 'id' => 'Tentang'],
  'layanan' => ['href' => $base . 'layanan.php', 'key' => 'nav.services', 'id' => 'Layanan'],
  'portofolio' => ['href' => $base . 'portofolio.php', 'key' => 'nav.work', 'id' => 'Portofolio'],
  'kontak' => ['href' => $base . 'kontak.php', 'key' => 'nav.contact', 'id' => 'Kontak'],
];
?>
<a class="skip-link" href="#main"><?php echo $lang === 'en' ? 'Skip to content' : 'Lewati ke konten'; ?></a>
<header class="site-header">
  <div class="container header-inner">
    <a class="brand" href="<?php echo $base; ?>index.php" aria-label="Sirius Global Indonesia">
      <span class="brand-mark" aria-hidden="true">
        <svg width="24" height="24" viewBox="0 0 64 64" fill="none"><path d="M32 10l4.6 9.6 10.5 1.4-7.7 7.3 1.9 10.4L32 33.7l-9.3 5 1.9-10.4-7.7-7.3 10.5-1.4z" fill="#fff"/><circle cx="32" cy="46.5" r="3.2" fill="#14A085"/></svg>
      </span>
      <span class="brand-name">Sirius Global<span>Indonesia · IT Consulting</span></span>
    </a>
    <nav class="main-nav" aria-label="Navigasi utama">
      <?php foreach ($nav as $k => $n): ?>
        <a href="<?php echo $n['href']; ?>"<?php echo $active === $k ? ' class="active"' : ''; ?> data-i18n="<?php echo $n['key']; ?>"><?php echo htmlspecialchars($n['id'], ENT_QUOTES, 'UTF-8'); ?></a>
      <?php endforeach; ?>
    </nav>
    <div class="header-actions">
      <div class="lang-toggle" role="group" aria-label="Language">
        <button type="button" data-lang="id" aria-pressed="<?php echo $lang === 'id' ? 'true' : 'false'; ?>">ID</button>
        <button type="button" data-lang="en" aria-pressed="<?php echo $lang === 'en' ? 'true' : 'false'; ?>">EN</button>
      </div>
      <a class="btn btn-primary btn-sm" href="<?php echo $base; ?>kontak.php" data-i18n="nav.cta"><?php echo cms_t('nav.cta', $lang); ?></a>
    </div>
    <button class="menu-btn" data-menu-btn aria-expanded="false" aria-label="Menu"><i></i><i></i><i></i></button>
  </div>
  <nav class="mobile-menu" data-mobile-menu aria-label="Menu seluler">
    <?php foreach ($nav as $k => $n): ?>
      <a href="<?php echo $n['href']; ?>"<?php echo $active === $k ? ' class="active"' : ''; ?> data-i18n="<?php echo $n['key']; ?>"><?php echo htmlspecialchars($n['id'], ENT_QUOTES, 'UTF-8'); ?></a>
    <?php endforeach; ?>
    <a href="<?php echo $base; ?>kontak.php" style="background:var(--navy-900);color:#fff;text-align:center" data-i18n="nav.cta"><?php echo cms_t('nav.cta', $lang); ?></a>
  </nav>
</header>
