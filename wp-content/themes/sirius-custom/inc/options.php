<?php
// inc/options.php - Tampilan > Konten Situs: registrasi halaman + render skeleton.
// Handler simpan ada di inc/options-save.php, field/panel di inc/options-fields.php.
// Semua panel tab dirender di DOM (satu form, satu submit) agar field pada tab
// yang tidak aktif tidak ikut terhapus saat menyimpan.
declare(strict_types=1);

function sgi_options_page(): void {
  add_theme_page('Konten Situs', 'Konten Situs', 'edit_theme_options', 'sgi', 'sgi_options_render');
}
add_action('admin_menu', 'sgi_options_page');
add_action('load-appearance_page_sgi', 'sgi_options_handle_save');

// Tab halaman. Urutan array = urutan tampil; kunci dipakai di URL + hidden input.
function sgi_options_tabs(): array {
  return [
    'header' => 'Header & navigasi',
    'hero' => 'Hero & angka',
    'sections' => 'Section beranda',
    'kontak' => 'Kontak & situs',
  ];
}

function sgi_options_current_tab($raw): string {
  $key = is_string($raw) ? sanitize_key($raw) : '';
  $tabs = sgi_options_tabs();
  return isset($tabs[$key]) ? $key : 'header';
}

function sgi_options_redirect(string $tab, array $args = []): void {
  wp_safe_redirect(sgi_admin_page_url(array_merge(['tab' => $tab], $args)));
  exit;
}

function sgi_options_render(): void {
  if (!current_user_can('edit_theme_options')) return;
  $tab = sgi_options_current_tab(wp_unslash($_GET['tab'] ?? 'header'));
  $s = sgi_settings_raw();
  $h = sgi_home();
  echo '<div class="wrap sgi-admin">';
  echo '<h1 class="wp-heading-inline">Konten Situs</h1> ';
  echo '<a href="' . esc_url(home_url('/')) . '" class="page-title-action" target="_blank" rel="noopener">Lihat beranda</a>';
  echo '<hr class="wp-header-end">';
  if (isset($_GET['updated'])) {
    echo '<div class="notice notice-success is-dismissible"><p>Tersimpan.</p></div>';
  }
  if (isset($_GET['seeded'])) {
    echo '<div class="notice notice-success is-dismissible"><p>Menu bawaan dibuat. Label navigasi sekarang dapat diubah di bawah.</p></div>';
  }
  $err = (string)($_GET['sgi_err'] ?? '');
  if ($err === 'email') {
    echo '<div class="notice notice-error"><p>Email tidak valid. Data tidak disimpan.</p></div>';
  } elseif ($err === 'nav') {
    echo '<div class="notice notice-error"><p>Perubahan tersimpan, tetapi sebagian label navigasi gagal diperbarui. Coba lagi dari Tampilan &gt; Menu.</p></div>';
  } elseif ($err === 'seed') {
    echo '<div class="notice notice-error"><p>Menu bawaan gagal dibuat. Buat manual di Tampilan &gt; Menu.</p></div>';
  }
  echo '<p class="description">Perubahan di sini langsung tampil di situs setelah disimpan.</p>';

  // Tab berupa button: tanpa JS panel tidak bisa disembunyikan, jadi semua
  // panel tampil dan tombol tab tidak melakukan navigasi (tidak ada isian
  // yang hilang karena pindah tab).
  echo '<nav class="nav-tab-wrapper sgi-tabs" aria-label="Bagian pengaturan konten">';
  foreach (sgi_options_tabs() as $key => $label) {
    echo '<button type="button" class="nav-tab' . ($key === $tab ? ' nav-tab-active' : '') . '"'
      . ' id="sgi-tab-' . esc_attr($key) . '"'
      . ' aria-controls="sgi-panel-' . esc_attr($key) . '"'
      . ' data-sgi-tab="' . esc_attr($key) . '">' . esc_html($label) . '</button>';
  }
  echo '</nav>';
  echo '<noscript><style>.sgi-admin .sgi-tabs{display:none}</style><p class="description">JavaScript nonaktif: semua bagian ditampilkan sekaligus.</p></noscript>';

  echo '<form method="post" action="' . esc_url(sgi_admin_page_url()) . '" class="sgi-form">';
  wp_nonce_field('sgi_opts');
  echo '<input type="hidden" name="sgi_tab" value="' . esc_attr($tab) . '">';
  echo '<div class="sgi-panels">';
  sgi_options_panel_header($s);
  sgi_options_panel_hero($h);
  sgi_options_panel_sections($h);
  sgi_options_panel_kontak($s);
  echo '</div>';
  echo '<div class="sgi-savebar"><button type="submit" class="button button-primary" name="sgi_save" value="1">Simpan</button></div>';
  echo '</form>';
  // Form terpisah untuk aksi seed: tombolnya berada di panel Header, tapi
  // tidak boleh menjadi tombol submit pertama pada form utama (Enter di field
  // akan memicu aksi yang salah).
  echo '<form id="sgi-seed" method="post" action="' . esc_url(sgi_admin_page_url()) . '">';
  wp_nonce_field('sgi_opts', '_sgi_seed_nonce', false);
  echo '<input type="hidden" name="sgi_tab" value="' . esc_attr($tab) . '">';
  echo '</form>';
  echo '</div>';
}
