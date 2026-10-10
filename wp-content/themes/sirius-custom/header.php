<?php
// header.php - port templates/header.php, ID-only (tanpa toggle bahasa).
$s = sgi_settings();
$nav = [
  ['href' => home_url('/'), 'label' => 'Beranda', 'key' => 'home'],
  ['href' => home_url('/tentang/'), 'label' => 'Tentang', 'key' => 'tentang'],
  ['href' => home_url('/layanan/'), 'label' => 'Layanan', 'key' => 'layanan'],
  ['href' => home_url('/portofolio/'), 'label' => 'Portofolio', 'key' => 'portofolio'],
  ['href' => home_url('/kontak/'), 'label' => 'Kontak', 'key' => 'kontak'],
];
$current = get_queried_object();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main">Lewati ke konten</a>
<header class="site-header">
  <div class="container header-inner">
    <a class="brand" href="<?php echo esc_url(home_url('/')); ?>" aria-label="Sirius Global Indonesia">
      <span class="brand-mark" aria-hidden="true">
        <svg width="24" height="24" viewBox="0 0 64 64" fill="none"><path d="M32 10l4.6 9.6 10.5 1.4-7.7 7.3 1.9 10.4L32 33.7l-9.3 5 1.9-10.4-7.7-7.3 10.5-1.4z" fill="#fff"/><circle cx="32" cy="46.5" r="3.2" fill="#14A085"/></svg>
      </span>
      <span class="brand-name">Sirius Global<span>Indonesia · IT Consulting</span></span>
    </a>
    <nav class="main-nav" aria-label="Navigasi utama">
      <?php foreach ($nav as $n): ?>
        <a href="<?php echo esc_url($n['href']); ?>"><?php echo esc_html($n['label']); ?></a>
      <?php endforeach; ?>
    </nav>
    <div class="header-actions">
      <a class="btn btn-primary btn-sm" href="<?php echo esc_url(home_url('/kontak/')); ?>">Konsultasi Gratis</a>
    </div>
    <button class="menu-btn" data-menu-btn aria-expanded="false" aria-label="Menu"><i></i><i></i><i></i></button>
  </div>
  <nav class="mobile-menu" data-mobile-menu aria-label="Menu seluler">
    <?php foreach ($nav as $n): ?>
      <a href="<?php echo esc_url($n['href']); ?>"><?php echo esc_html($n['label']); ?></a>
    <?php endforeach; ?>
    <a href="<?php echo esc_url(home_url('/kontak/')); ?>" style="background:var(--navy-900);color:#fff;text-align:center">Konsultasi Gratis</a>
  </nav>
</header>
<main id="main">
