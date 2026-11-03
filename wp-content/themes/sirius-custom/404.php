<?php
// 404.php - port 404 lama, ID-only.
get_header();
http_response_code(404);
?>
<section class="page-hero"><div class="container">
<p class="breadcrumb"><a href="<?php echo esc_url(home_url('/')); ?>"><?php echo esc_html(sgi_t('common.home', 'Beranda')); ?></a> / <span>404</span></p>
<h1><?php echo esc_html(sgi_t('error.404_title', 'Halaman tidak ditemukan')); ?></h1>
<p class="lead" style="margin-top:12px"><?php echo esc_html(sgi_t('error.404_desc', 'Tautan yang Anda buka sudah dipindah atau dihapus. Silakan kembali ke beranda atau hubungi kami.')); ?></p>
<p style="margin-top:20px;display:flex;gap:12px;flex-wrap:wrap">
  <a class="btn btn-primary" href="<?php echo esc_url(home_url('/')); ?>"><?php echo esc_html(sgi_t('common.back_to_home', 'Kembali ke Beranda')); ?></a>
  <a class="btn btn-ghost" href="<?php echo esc_url(home_url('/kontak/')); ?>"><?php echo esc_html(sgi_t('common.contact_us', 'Hubungi Kami')); ?></a>
</p>
</div></section>
<?php get_footer(); ?>
