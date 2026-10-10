<?php
// inc/options.php - Settings > Sirius: settings + home (ID-only).
// Diisi otomatis oleh importer; editor dapat mengubah tanpa sentuh kode.
declare(strict_types=1);

function sgi_options_page(): void {
  add_options_page('Sirius', 'Sirius', 'manage_options', 'sgi', 'sgi_options_render');
}
add_action('admin_menu', 'sgi_options_page');

function sgi_options_render(): void {
  if (!current_user_can('manage_options')) return;
  if (isset($_POST['sgi_save']) && check_admin_referer('sgi_opts')) {
    $settings = [];
    foreach (['name', 'email', 'phone', 'phoneHref', 'address', 'hours', 'mapEmbed'] as $k) {
      $settings[$k] = sanitize_text_field(wp_unslash($_POST['sgi_' . $k] ?? ''));
    }
    if (!is_email($settings['email'])) {
      echo '<div class="notice notice-error"><p>Email tidak valid. Data tidak disimpan.</p></div>';
    } else {
      update_option('sgi_settings', $settings);
      $home = [
        'kpi_counts' => array_slice(array_map('sanitize_text_field', explode(',', wp_unslash($_POST['sgi_kpi'] ?? ''))), 0, 3),
        'stat_counts' => array_slice(array_map('sanitize_text_field', explode(',', wp_unslash($_POST['sgi_stats'] ?? ''))), 0, 4),
        'clients' => array_slice(array_values(array_filter(array_map('trim', explode("\n", wp_unslash($_POST['sgi_clients'] ?? ''))))), 0, 12),
      ];
      $cur = (array)get_option('sgi_home', []);
      update_option('sgi_home', array_merge($cur, $home));
      echo '<div class="notice notice-success"><p>Tersimpan.</p></div>';
    }
  }
  $s = sgi_settings(); $h = sgi_home();
  echo '<div class="wrap"><h1>Sirius</h1><form method="post">';
  wp_nonce_field('sgi_opts');
  foreach (['name' => 'Nama', 'email' => 'Email', 'phone' => 'Telepon', 'phoneHref' => 'Link WA', 'address' => 'Alamat', 'hours' => 'Jam', 'mapEmbed' => 'Embed peta'] as $k => $label) {
    echo '<p><label><strong>' . esc_html($label) . '</strong><br><input type="text" name="sgi_' . esc_attr($k) . '" value="' . esc_attr($s[$k]) . '" class="regular-text" style="max-width:640px;width:100%"></label></p>';
  }
  echo '<p><label><strong>KPI beranda (3 angka, pisah koma)</strong><br><input type="text" name="sgi_kpi" value="' . esc_attr(implode(',', $h['kpi_counts'])) . '" class="regular-text"></label></p>';
  echo '<p><label><strong>Statistik (4 angka, pisah koma)</strong><br><input type="text" name="sgi_stats" value="' . esc_attr(implode(',', $h['stat_counts'])) . '" class="regular-text"></label></p>';
  echo '<p><label><strong>Klien (satu per baris, maks 12)</strong><br><textarea name="sgi_clients" rows="6" style="max-width:640px;width:100%">' . esc_textarea(implode("\n", $h['clients'])) . '</textarea></label></p>';
  echo '<p><button class="button button-primary" name="sgi_save" value="1">Simpan</button></p></form></div>';
}
