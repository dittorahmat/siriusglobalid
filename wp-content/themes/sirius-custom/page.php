<?php
// page.php - Template generik (privasi, syarat, halaman baru editor).
get_header();
?>
<section class="page-hero"><div class="container">
<p class="breadcrumb"><a href="<?php echo esc_url(home_url('/')); ?>">Beranda</a> / <?php the_title(); ?></p>
<h1><?php the_title(); ?></h1>
</div></section>
<section class="section"><div class="container" style="max-width:760px">
<?php while (have_posts()): the_post(); the_content(); endwhile; ?>
</div></section>
<?php get_footer(); ?>
