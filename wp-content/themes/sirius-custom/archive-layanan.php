<?php
// archive-layanan.php - indeks layanan DINAMIS (query CPT, bukan daftar kunci).
// Layanan baru dari editor otomatis muncul di sini dengan styling kartu mirror beranda.
get_header();
$h = sgi_home();
$showCombo = !in_array((string)($h['layanan_combo'] ?? '1'), ['', '0'], true);
$q = new WP_Query([
  'post_type' => 'layanan',
  'posts_per_page' => -1,
  'orderby' => ['menu_order' => 'ASC', 'title' => 'ASC'],
]);
?>
<section class="page-hero">
  <div class="container">
    <p class="breadcrumb"><a href="<?php echo esc_url(home_url('/')); ?>">Beranda</a> / <span>Layanan</span></p>
    <?php if (!empty($h['archive_svc_eye'])): ?>
      <span class="eyebrow"><?php echo esc_html($h['archive_svc_eye']); ?></span>
    <?php endif; ?>
    <h1><?php
      $title = trim((string)($h['archive_svc_title_html'] ?? ''));
      if ($title === '') {
        $title = 'Sembilan kapabilitas, <span>satu standar kualitas</span>';
      }
      echo wp_kses($title, ['span' => []]);
    ?></h1>
    <?php if (!empty($h['archive_svc_lead'])): ?>
      <p class="lead"><?php echo esc_html($h['archive_svc_lead']); ?></p>
    <?php endif; ?>
  </div>
</section>
<section class="section">
  <div class="container grid services-grid">
    <?php
    while ($q->have_posts()): $q->the_post();
      $lead = get_post_meta(get_the_ID(), '_sgi_lead', true);
      if ($lead === '') {
        $lead = wp_trim_words(get_the_excerpt() ?: get_the_content(), 18);
      }
      $has_bg = has_post_thumbnail(get_the_ID());
      $thumb = $has_bg ? get_the_post_thumbnail_url(get_the_ID(), 'large') : '';
    ?>
    <article class="card reveal<?php echo $has_bg ? ' card-bg-img' : ''; ?>"<?php if ($has_bg): ?> style="--card-bg:url('<?php echo esc_url($thumb); ?>');"<?php endif; ?>>
      <div class="card-inner">
        <h3><?php the_title(); ?></h3>
        <p><?php echo esc_html($lead); ?></p>
        <p style="margin-top:14px"><a class="link-more" href="<?php the_permalink(); ?>">→ <span>Pelajari detail</span></a></p>
      </div>
    </article>
    <?php endwhile; wp_reset_postdata(); ?>
    <?php if ($showCombo): ?>
    <article class="card reveal" style="background:var(--bg-soft)">
      <h3><?php echo esc_html($h['combo_t']); ?></h3>
      <p><?php echo esc_html($h['combo_d']); ?></p>
      <p style="margin-top:14px"><a class="btn btn-primary btn-sm" href="<?php echo esc_url(home_url('/kontak/')); ?>">Konsultasi Gratis</a></p>
    </article>
    <?php endif; ?>
  </div>
</section>
<?php get_footer(); ?>
