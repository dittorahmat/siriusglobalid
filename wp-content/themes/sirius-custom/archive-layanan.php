<?php
// archive-layanan.php - indeks layanan DINAMIS (query CPT, bukan daftar kunci).
// Layanan baru dari editor otomatis muncul di sini.
get_header();
$q = new WP_Query(['post_type' => 'layanan', 'posts_per_page' => -1, 'orderby' => 'menu_order title', 'order' => 'ASC']);
?>
<section class="page-hero"><div class="container">
  <p class="breadcrumb"><a href="<?php echo esc_url(home_url('/')); ?>">Beranda</a> / <span>Layanan</span></p>
  <span class="eyebrow">Layanan</span>
  <h1>Sembilan kapabilitas, <span>satu standar kualitas</span></h1>
</div></section>
<section class="section"><div class="container grid services-grid">
  <?php while ($q->have_posts()): $q->the_post();
    $lead = get_post_meta(get_the_ID(), '_sgi_lead', true);
  ?>
  <article class="card reveal"><h3><?php the_title(); ?></h3><p class="small muted"><?php echo esc_html($lead); ?></p><p style="margin-top:14px"><a class="btn btn-ghost btn-sm" href="<?php the_permalink(); ?>">Pelajari detail →</a></p></article>
  <?php endwhile; wp_reset_postdata(); ?>
  <article class="card reveal" style="background:var(--navy-900);color:#fff;border-color:var(--navy-900)"><h3 style="color:#fff">Butuh kombinasi?</h3><p class="small" style="color:#c6d4ee">Contoh: Odoo + GPS armada + AI prediksi maintenance. Kami rancang arsitekturnya.</p><p style="margin-top:14px"><a class="btn btn-teal btn-sm" href="<?php echo esc_url(home_url('/kontak/')); ?>">Konsultasi Gratis</a></p></article>
</div></section>
<?php get_footer(); ?>
