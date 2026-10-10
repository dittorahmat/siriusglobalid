<?php
// page-portofolio.php - Template: Portofolio (filter 8 kategori, loop CPT portfolio).
/* Template Name: Portofolio */
get_header();
$filters = [['all', 'Semua'], ['erp', 'ERP'], ['ai', 'AI'], ['fleet', 'Fleet'], ['app', 'Aplikasi'], ['iot', 'IoT'], ['infra', 'Infrastruktur'], ['sec', 'Keamanan'], ['bi', 'Dashboard BI']];
$q = new WP_Query(['post_type' => 'portfolio', 'posts_per_page' => 24, 'orderby' => 'menu_order title', 'order' => 'ASC']);
?>
<section class="page-hero"><div class="container">
<p class="breadcrumb"><a href="<?php echo esc_url(home_url('/')); ?>">Beranda</a> / <span>Portofolio</span></p>
<span class="eyebrow">Portofolio</span>
<h1>Proyek pilihan dengan <span>dampak terukur</span></h1>
<div class="pills" style="margin-top:20px">
<?php $fi = 0; foreach ($filters as [$f, $label]): $fi++; ?>
<button class="pill<?php echo $fi === 1 ? ' active' : ''; ?>" data-filter="<?php echo esc_attr($f); ?>"><?php echo esc_html($label); ?></button>
<?php endforeach; ?>
</div></div></section>
<section class="section"><div class="container grid cases">
<?php while ($q->have_posts()): $q->the_post();
  $terms = get_the_terms(get_the_ID(), 'portfolio_cat');
  $cat = ($terms && !is_wp_error($terms)) ? $terms[0]->slug : '';
  if (!in_array($cat, sgi_portfolio_cats(), true)) continue;
  $img = get_the_post_thumbnail_url(get_the_ID(), 'large') ?: '';
?>
<article class="card case-card" data-cat="<?php echo esc_attr($cat); ?>"><div class="case-top"><img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" width="640" height="400" loading="lazy"></div><div class="case-body"><p class="case-cat"><?php echo esc_html($cat); ?></p><h3><?php the_title(); ?></h3><div class="small muted"><?php the_excerpt(); ?></div></div></article>
<?php endwhile; wp_reset_postdata(); ?>
</div><div class="container"><div class="cta-band" style="margin-top:36px"><div><h2>Punya target digital tahun ini? Diskusikan dulu, gratis.</h2></div><a class="btn btn-primary" href="<?php echo esc_url(home_url('/kontak/')); ?>">Konsultasi Gratis</a></div></div></section>
<?php get_footer(); ?>
