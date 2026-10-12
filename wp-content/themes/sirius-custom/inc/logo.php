<?php
// inc/logo.php - Logo dinamis via Customizer (ID-only).
// Header memakai core `custom_logo`; footer memakai `sgi_footer_logo` (varian putih).
// Jika belum diupload, fallback ke SVG bintang bawaan agar tidak kosong.
declare(strict_types=1);

function sgi_logo_fallback_mark(bool $footer = false): string {
  if ($footer) {
    return '<span aria-hidden="true">★</span>';
  }
  return '<svg width="24" height="24" viewBox="0 0 64 64" fill="none" aria-hidden="true" focusable="false"><path d="M32 10l4.6 9.6 10.5 1.4-7.7 7.3 1.9 10.4L32 33.7l-9.3 5 1.9-10.4-7.7-7.3 10.5-1.4z" fill="#fff"/><circle cx="32" cy="46.5" r="3.2" fill="#14A085"/></svg>';
}

// Kembalikan ID attachment logo untuk konteks terkait (0 = belum diupload).
function sgi_logo_id(string $context = 'header'): int {
  if ($context === 'footer') {
    $footer_id = (int) get_theme_mod('sgi_footer_logo', 0);
    if ($footer_id > 0) {
      return $footer_id;
    }
  }
  return (int) get_theme_mod('custom_logo', 0);
}

// Cetak isi `.brand-mark`: <img> logo jika ada, SVG bintang jika belum.
function sgi_logo_mark(string $context = 'header'): void {
  $id = sgi_logo_id($context);
  $footer = ($context === 'footer');
  if ($id > 0) {
    $img = wp_get_attachment_image($id, 'full', false, [
      'class' => 'custom-logo',
      'alt' => '',
      'decoding' => 'async',
    ]);
    if (is_string($img) && $img !== '') {
      echo $img;
      return;
    }
  }
  echo sgi_logo_fallback_mark($footer); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

// Tandai `.brand-mark` dengan class `has-logo` saat memakai gambar upload.
function sgi_logo_mark_class(string $context = 'header'): string {
  return sgi_logo_id($context) > 0 ? 'brand-mark has-logo' : 'brand-mark';
}

// Class tambahan untuk anchor `.brand`: lockup landscape penuh saat logo ada
// (teks theme disembunyikan visual tapi tetap terbaca screen reader).
function sgi_brand_class(string $context = 'header'): string {
  return sgi_logo_id($context) > 0 ? 'brand brand-full-logo' : 'brand';
}

// Class untuk teks brand: disembunyikan visual saat logo lockup tampil.
function sgi_brand_name_class(string $context = 'header'): string {
  return sgi_logo_id($context) > 0 ? 'brand-name visually-hidden' : 'brand-name';
}

function sgi_customize_logo(WP_Customize_Manager $wp_customize): void {
  $wp_customize->add_setting('sgi_footer_logo', [
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    'sanitize_callback' => 'absint',
  ]);
  $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'sgi_footer_logo', [
    'label' => 'Logo footer (varian terang)',
    'description' => 'Opsional. Dipakai di footer gelap. Jika kosong, memakai logo utama.',
    'section' => 'title_tagline',
    'mime_type' => 'image',
    'priority' => 9,
  ]));
}
add_action('customize_register', 'sgi_customize_logo');
