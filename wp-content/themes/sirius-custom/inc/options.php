<?php
// inc/options.php - Settings > Sirius: settings + seluruh konten homepage (ID-only).
// Diisi otomatis oleh importer; editor dapat mengubah tanpa sentuh kode.
// Format kartu: textarea satu-baris-per-item "kolom1 | kolom2 | ...".
// Urutan section: teks koma "hero,stats,layanan,why,alur,cases,testi,cta"
// (hapus nama = sembunyikan section).
declare(strict_types=1);

function sgi_options_page(): void {
  add_options_page('Sirius', 'Sirius', 'manage_options', 'sgi', 'sgi_options_render');
}
add_action('admin_menu', 'sgi_options_page');

function sgi_opt_input(string $name, string $label, string $value): void {
  echo '<p><label><strong>' . esc_html($label) . '</strong><br><input type="text" name="' . esc_attr($name) . '" value="' . esc_attr($value) . '" class="regular-text" style="max-width:640px;width:100%"></label></p>';
}

function sgi_opt_area(string $name, string $label, string $value, string $desc = '', int $rows = 4): void {
  echo '<p><label><strong>' . esc_html($label) . '</strong><br><textarea name="' . esc_attr($name) . '" rows="' . $rows . '" style="max-width:640px;width:100%">' . esc_textarea($value) . '</textarea></label>';
  if ($desc !== '') echo '<br><span class="description">' . esc_html($desc) . '</span>';
  echo '</p>';
}

// Gabungkan dua list sejajar jadi teks "a | b" per baris untuk textarea.
function sgi_opt_join_pairs(array $first, array $second): string {
  $out = [];
  foreach ($first as $i => $a) {
    $b = (string)($second[$i] ?? '');
    $out[] = trim((string)$a . ($b !== '' ? ' | ' . $b : ''));
  }
  return implode("\n", $out);
}

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
      $txt = function (string $k): string {
        return sanitize_text_field(wp_unslash($_POST[$k] ?? ''));
      };
      $kpiPairs = sgi_pair_lines(wp_unslash($_POST['sgi_kpi'] ?? ''), 2);
      $statPairs = sgi_pair_lines(wp_unslash($_POST['sgi_stats'] ?? ''), 2);
      $whyPairs = sgi_pair_lines(wp_unslash($_POST['sgi_why'] ?? ''), 2);
      $stepPairs = sgi_pair_lines(wp_unslash($_POST['sgi_steps'] ?? ''), 2);
      $caseTriples = sgi_pair_lines(wp_unslash($_POST['sgi_cases'] ?? ''), 3);
      $testiPairs = sgi_pair_lines(wp_unslash($_POST['sgi_testis'] ?? ''), 2);
      $clients = array_slice(array_values(array_filter(array_map('trim', explode("\n", wp_unslash($_POST['sgi_clients'] ?? ''))))), 0, 12);
      $hide = [];
      foreach (explode("\n", wp_unslash($_POST['sgi_layanan_hide'] ?? '')) as $ln) {
        $slug = sanitize_title(trim($ln));
        if ($slug !== '') $hide[] = $slug;
      }
      foreach ([
        'home.clients' => $clients, 'home.cases' => $caseTriples,
        'home.why' => $whyPairs, 'home.steps' => $stepPairs, 'home.testi' => $testiPairs,
      ] as $k => $items) {
        if ($m = sgi_over_limit($k, $items)) {
          wp_die(esc_html($m . ' Data tidak disimpan.'));
        }
      }
      $col = function (array $pairs, int $i): array {
        return array_map(function ($p) use ($i) { return (string)($p[$i] ?? ''); }, $pairs);
      };
      $td = function (array $pairs): array {
        return array_map(function ($p) { return ['t' => (string)($p[0] ?? ''), 'd' => (string)($p[1] ?? '')]; }, $pairs);
      };
      $cur = (array)get_option('sgi_home', []);
      $oldKpiLabels = $cur['kpi_labels'] ?? [];
      $oldStatLabels = $cur['stat_labels'] ?? [];
      $kpiLabels = $col($kpiPairs, 1);
      $statLabels = $col($statPairs, 1);
      foreach ($kpiLabels as $i => $lb) {
        if ($lb === '') $kpiLabels[$i] = (string)($oldKpiLabels[$i] ?? '');
      }
      foreach ($statLabels as $i => $lb) {
        if ($lb === '') $statLabels[$i] = (string)($oldStatLabels[$i] ?? '');
      }
      $home = [
        'hero' => [
          'eyebrow' => $txt('sgi_hero_eyebrow'), 'lead' => $txt('sgi_hero_lead'),
          'cta1' => $txt('sgi_hero_cta1'), 'cta1_href' => $txt('sgi_hero_cta1_href'),
          'cta2' => $txt('sgi_hero_cta2'), 'cta2_href' => $txt('sgi_hero_cta2_href'),
          'card1t' => $txt('sgi_hero_card1t'), 'card1a' => $txt('sgi_hero_card1a'),
          'card1b' => $txt('sgi_hero_card1b'), 'card1c' => $txt('sgi_hero_card1c'),
          'title_html' => wp_kses(wp_unslash($_POST['sgi_hero_title'] ?? ''), ['span' => []]),
        ],
        'kpi_counts' => array_slice($col($kpiPairs, 0), 0, 3),
        'kpi_labels' => array_slice($kpiLabels, 0, 3),
        'stat_counts' => array_slice($col($statPairs, 0), 0, 4),
        'stat_labels' => array_slice($statLabels, 0, 4),
        'clients' => $clients,
        'svc_eye' => $txt('sgi_svc_eye'), 'svc_t' => $txt('sgi_svc_t'), 'svc_d' => $txt('sgi_svc_d'),
        'combo_t' => $txt('sgi_combo_t'), 'combo_d' => $txt('sgi_combo_d'), 'combo_btn' => $txt('sgi_combo_btn'),
        'layanan_count' => max(1, min(8, (int)($_POST['sgi_layanan_count'] ?? 5))),
        'layanan_hide' => array_slice($hide, 0, 20),
        'layanan_combo' => isset($_POST['sgi_layanan_combo']) ? '1' : '',
        'why_t' => $txt('sgi_why_t'),
        'why' => $td($whyPairs),
        'proc_t' => $txt('sgi_proc_t'),
        'steps' => $td($stepPairs),
        'cases_eye' => $txt('sgi_cases_eye'), 'cases_t' => $txt('sgi_cases_t'), 'cases_all' => $txt('sgi_cases_all'),
        'cases' => array_map(function ($p) { return ['c' => (string)($p[0] ?? ''), 't' => (string)($p[1] ?? ''), 'd' => (string)($p[2] ?? '')]; }, $caseTriples),
        'testi_t' => $txt('sgi_testi_t'),
        'testis' => array_map(function ($p) { return ['q' => (string)($p[0] ?? ''), 'by' => (string)($p[1] ?? '')]; }, $testiPairs),
        'cta' => ['t' => $txt('sgi_cta_t'), 'd' => $txt('sgi_cta_d'), 'b1' => $txt('sgi_cta_b1'), 'b2' => $txt('sgi_cta_b2')],
        'home_order' => sgi_home_order(wp_unslash($_POST['sgi_home_order'] ?? '')),
      ];
      update_option('sgi_home', array_merge($cur, $home));
      $kontak = [
        'hero_h1' => $txt('sgi_k_hero_h1'),
        'form_t' => $txt('sgi_k_form_t'),
        'form_d' => $txt('sgi_k_form_d'),
        'needs' => sgi_kontak_pairs(wp_unslash($_POST['sgi_k_needs'] ?? '')),
        'budgets' => sgi_kontak_pairs(wp_unslash($_POST['sgi_k_budgets'] ?? '')),
        'times' => sgi_kontak_pairs(wp_unslash($_POST['sgi_k_times'] ?? '')),
      ];
      update_option('sgi_kontak', $kontak);
      echo '<div class="notice notice-success"><p>Tersimpan.</p></div>';
    }
  }
  $s = sgi_settings(); $h = sgi_home();
  $hero = $h['hero']; $cta = $h['cta'];
  echo '<div class="wrap"><h1>Sirius</h1>';
  echo '<p>Ganti logo header/footer via <a href="' . esc_url(admin_url('customize.php?autofocus[section]=title_tagline')) . '">Tampilan &gt; Sesuaikan &gt; Identitas Situs</a> (Logo + Logo footer varian terang). Boleh lockup landscape penuh; jika kosong, tampil bintang + teks bawaan. Favicon via Ikon Situs di halaman yang sama.</p><form method="post">';
  wp_nonce_field('sgi_opts');
  echo '<h2>Kontak &amp; situs</h2>';
  foreach (['name' => 'Nama', 'email' => 'Email', 'phone' => 'Telepon', 'phoneHref' => 'Link WA', 'address' => 'Alamat', 'hours' => 'Jam', 'mapEmbed' => 'Embed peta'] as $k => $label) {
    sgi_opt_input('sgi_' . $k, $label, (string)($s[$k] ?? ''));
  }
  echo '<h2>Urutan section beranda</h2>';
  sgi_opt_input('sgi_home_order', 'Urutan (pisah koma)', implode(',', sgi_home_order($h['home_order'] ?? [])));
  echo '<p class="description">Nama valid: <code>hero,stats,layanan,why,alur,cases,testi,cta</code>. Hapus nama untuk menyembunyikan section. Contoh: <code>hero,layanan,alur,cases,cta</code>.</p>';
  echo '<h2>Hero</h2>';
  sgi_opt_input('sgi_hero_eyebrow', 'Eyebrow', (string)($hero['eyebrow'] ?? ''));
  sgi_opt_area('sgi_hero_title', 'Judul (boleh pakai <span> untuk aksen)', (string)($hero['title_html'] ?? ''), '', 2);
  sgi_opt_area('sgi_hero_lead', 'Lead', (string)($hero['lead'] ?? ''), '', 2);
  sgi_opt_input('sgi_hero_cta1', 'Tombol 1', (string)($hero['cta1'] ?? ''));
  sgi_opt_input('sgi_hero_cta1_href', 'Link tombol 1 (/kontak/ atau https://...)', (string)($hero['cta1_href'] ?? ''));
  sgi_opt_input('sgi_hero_cta2', 'Tombol 2', (string)($hero['cta2'] ?? ''));
  sgi_opt_input('sgi_hero_cta2_href', 'Link tombol 2', (string)($hero['cta2_href'] ?? ''));
  sgi_opt_input('sgi_hero_card1t', 'Judul kartu status', (string)($hero['card1t'] ?? ''));
  sgi_opt_input('sgi_hero_card1a', 'Status baris 1', (string)($hero['card1a'] ?? ''));
  sgi_opt_input('sgi_hero_card1b', 'Status baris 2', (string)($hero['card1b'] ?? ''));
  sgi_opt_input('sgi_hero_card1c', 'Status baris 3', (string)($hero['card1c'] ?? ''));
  echo '<h2>Angka &amp; klien</h2>';
  sgi_opt_area('sgi_kpi', 'KPI hero (3 baris "angka | label")', sgi_opt_join_pairs((array)$h['kpi_counts'], (array)$h['kpi_labels']), '', 3);
  sgi_opt_area('sgi_stats', 'Statistik (4 baris "angka | label")', sgi_opt_join_pairs((array)$h['stat_counts'], (array)$h['stat_labels']), '', 4);
  sgi_opt_area('sgi_clients', 'Klien (satu per baris, maks 12)', implode("\n", (array)$h['clients']), '', 6);
  echo '<h2>Layanan (otomatis dari menu Layanan)</h2>';
  sgi_opt_input('sgi_svc_eye', 'Eyebrow', (string)($h['svc_eye'] ?? ''));
  sgi_opt_input('sgi_svc_t', 'Judul', (string)($h['svc_t'] ?? ''));
  sgi_opt_area('sgi_svc_d', 'Deskripsi', (string)($h['svc_d'] ?? ''), '', 2);
  sgi_opt_input('sgi_combo_t', 'Judul kartu kombinasi', (string)($h['combo_t'] ?? ''));
  sgi_opt_area('sgi_combo_d', 'Deskripsi kartu kombinasi', (string)($h['combo_d'] ?? ''), '', 2);
  sgi_opt_input('sgi_combo_btn', 'Tombol kartu kombinasi', (string)($h['combo_btn'] ?? ''));
  sgi_opt_input('sgi_layanan_count', 'Jumlah kartu (1-8)', (string)($h['layanan_count'] ?? 5));
  sgi_opt_area('sgi_layanan_hide', 'Sembunyikan slug (satu per baris)', implode("\n", (array)($h['layanan_hide'] ?? [])));
  echo '<p><label><input type="checkbox" name="sgi_layanan_combo" value="1"' . checked((string)($h['layanan_combo'] ?? '1'), '1', false) . '> Tampilkan kartu kombinasi</label></p>';
  echo '<h2>Keunggulan</h2>';
  sgi_opt_input('sgi_why_t', 'Judul', (string)($h['why_t'] ?? ''));
  sgi_opt_area('sgi_why', 'Item (satu per baris "judul | deskripsi", maks 6)', implode("\n", array_map(function ($w) { return ($w['t'] ?? '') . ' | ' . ($w['d'] ?? ''); }, (array)$h['why'])), '', 6);
  echo '<h2>Alur kerja</h2>';
  sgi_opt_input('sgi_proc_t', 'Judul', (string)($h['proc_t'] ?? ''));
  sgi_opt_area('sgi_steps', 'Tahapan (satu per baris "judul | deskripsi", maks 8)', implode("\n", array_map(function ($st) { return ($st['t'] ?? '') . ' | ' . ($st['d'] ?? ''); }, (array)$h['steps'])), '', 6);
  echo '<h2>Studi kasus</h2>';
  sgi_opt_input('sgi_cases_eye', 'Eyebrow', (string)($h['cases_eye'] ?? ''));
  sgi_opt_input('sgi_cases_t', 'Judul', (string)($h['cases_t'] ?? ''));
  sgi_opt_input('sgi_cases_all', 'Tombol arsip', (string)($h['cases_all'] ?? ''));
  sgi_opt_area('sgi_cases', 'Kasus (satu per baris "kategori | judul | deskripsi", maks 6)', implode("\n", array_map(function ($c) { return ($c['c'] ?? '') . ' | ' . ($c['t'] ?? '') . ' | ' . ($c['d'] ?? ''); }, (array)$h['cases'])), 'Gambar diambil dari data case_imgs (importer).', 6);
  echo '<h2>Testimoni &amp; CTA</h2>';
  sgi_opt_input('sgi_testi_t', 'Judul testimoni', (string)($h['testi_t'] ?? ''));
  sgi_opt_area('sgi_testis', 'Testimoni (satu per baris "kutipan | nama", maks 6)', implode("\n", array_map(function ($t) { return ($t['q'] ?? '') . ' | ' . ($t['by'] ?? ''); }, (array)$h['testis'])), '', 4);
  sgi_opt_input('sgi_cta_t', 'Judul CTA', (string)($cta['t'] ?? ''));
  sgi_opt_area('sgi_cta_d', 'Deskripsi CTA', (string)($cta['d'] ?? ''), '', 2);
  sgi_opt_input('sgi_cta_b1', 'Tombol CTA 1', (string)($cta['b1'] ?? ''));
  sgi_opt_input('sgi_cta_b2', 'Tombol CTA 2', (string)($cta['b2'] ?? ''));
  echo '<h2>Halaman Kontak</h2>';
  echo '<p class="description">Nav header diatur via <a href="' . esc_url(admin_url('nav-menus.php')) . '">Tampilan &gt; Menu</a> (lokasi Navigasi utama). Kosongkan isian di bawah untuk memakai bawaan.</p>';
  $k = function_exists('sgi_kontak') ? sgi_kontak() : [];
  sgi_opt_input('sgi_k_hero_h1', 'Judul hero Kontak', (string)($k['hero_h1'] ?? ''));
  sgi_opt_input('sgi_k_form_t', 'Judul formulir', (string)($k['form_t'] ?? ''));
  sgi_opt_area('sgi_k_form_d', 'Pengantar formulir', (string)($k['form_d'] ?? ''), '', 2);
  sgi_opt_area('sgi_k_needs', 'Kebutuhan (satu per baris "nilai | label", maks 20)', function_exists('sgi_kontak_pairs_text') ? sgi_kontak_pairs_text((array)($k['needs'] ?? [])) : '', 'Nilai dipakai untuk praseleksi ?layanan=. Contoh: app-development | Application Development', 10);
  sgi_opt_area('sgi_k_budgets', 'Budget (satu per baris "nilai | label", maks 20)', function_exists('sgi_kontak_pairs_text') ? sgi_kontak_pairs_text((array)($k['budgets'] ?? [])) : '', '', 5);
  sgi_opt_area('sgi_k_times', 'Target waktu (satu per baris "nilai | label", maks 20)', function_exists('sgi_kontak_pairs_text') ? sgi_kontak_pairs_text((array)($k['times'] ?? [])) : '', '', 3);
  echo '<p><button class="button button-primary" name="sgi_save" value="1">Simpan</button></p></form></div>';
}
