<?php
// inc/cpt.php - CPT layanan (slug bebas) + CPT portfolio + taksonomi.
// Layanan sengaja TIDAK dikunci ke daftar slug: tambah/ubah/hapus murni
// lewat wp-admin. Sembilan slug seed hanya data awal untuk importer.

function sgi_register_types(): void {
  register_post_type('layanan', [
    'label' => 'Layanan',
    'public' => true,
    'has_archive' => true,
    'rewrite' => ['slug' => 'layanan'],
    'show_in_rest' => true,
    'supports' => ['title', 'editor', 'custom-fields'],
    'menu_icon' => 'dashicons-hammer',
  ]);

  register_taxonomy('portfolio_cat', ['portfolio'], [
    'label' => 'Kategori Portofolio',
    'public' => true,
    'hierarchical' => false,
    'show_in_rest' => true,
    'rewrite' => ['slug' => 'portofolio'],
  ]);

  register_post_type('portfolio', [
    'label' => 'Portofolio',
    'public' => true,
    'has_archive' => true,
    'rewrite' => ['slug' => 'portofolio-item'],
    'show_in_rest' => true,
    'supports' => ['title', 'editor', 'thumbnail', 'custom-fields'],
    'menu_icon' => 'dashicons-briefcase',
  ]);

  // Pastikan 8 kategori valid ada (slug = nama).
  foreach (sgi_portfolio_cats() as $cat) {
    if (!term_exists($cat, 'portfolio_cat')) {
      wp_insert_term($cat, 'portfolio_cat', ['slug' => $cat]);
    }
  }
}
add_action('init', 'sgi_register_types');
