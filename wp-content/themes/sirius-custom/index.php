<?php
// index.php - fallback loop standar (arsip blog bila editor memakai Posts).
get_header();
?>
<section class="page-hero"><div class="container">
<h1><?php echo is_home() ? 'Berita' : get_the_archive_title(); ?></h1>
</div></section>
<section class="section"><div class="container grid grid-3">
<?php while (have_posts()): the_post(); ?>
<article class="card reveal"><h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3><div class="small muted"><?php the_excerpt(); ?></div></article>
<?php endwhile; ?>
</div></section>
<?php get_footer(); ?>
