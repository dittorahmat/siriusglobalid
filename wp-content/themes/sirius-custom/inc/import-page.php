<?php
// inc/import-page.php - Tools > Sirius Import: jalankan importer sekali klik.
// Baca seed yang dibundel di theme (assets/seed/), sisi ID saja.
declare(strict_types=1);

function sgi_import_menu(): void {
  add_management_page('Sirius Import', 'Sirius Import', 'manage_options', 'sgi-import', 'sgi_import_render');
}
add_action('admin_menu', 'sgi_import_menu');

function sgi_import_render(): void {
  if (!current_user_can('manage_options')) return;
  echo '<div class="wrap"><h1>Sirius Import</h1>';
  if (isset($_POST['sgi_run']) && check_admin_referer('sgi_import')) {
    $imp = dirname(__DIR__, 3) . '/sgi-importer/sgi-importer.php';
    if (!is_file($imp)) {
      echo '<div class="notice notice-error"><p>Importer tidak ditemukan di <code>wp-content/sgi-importer/</code>. Deploy tools dulu via Git.</p></div>';
    } else {
      require_once $imp;
      $log = sgi_import_all(get_stylesheet_directory() . '/assets/seed');
      echo '<h2>Hasil</h2><ul>';
      echo '<li>Dibuat/diperbarui: ' . (int)($log['created'] + $log['updated']) . '</li>';
      echo '<li>Dilewati (sama): ' . (int)$log['skipped'] . '</li>';
      foreach ($log['errors'] as $e) echo '<li><strong>Gagal:</strong> ' . esc_html($e) . '</li>';
      echo '</ul><p>Flush permalink sekali di Settings > Permalinks (Save), lalu cek beranda + 1 layanan.</p>';
    }
  } else {
    echo '<p>Membaca seed dari theme (<code>assets/seed/</code>), sisi ID saja. Idempoten: aman dijalankan ulang.</p>';
    echo '<form method="post">';
    wp_nonce_field('sgi_import');
    echo '<p><button class="button button-primary" name="sgi_run" value="1">Jalankan Import</button></p></form>';
  }
  echo '</div>';
}
