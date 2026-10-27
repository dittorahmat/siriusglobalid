<?php
// inc/options-save.php - handler simpan Tampilan > Konten Situs.
// Berjalan di load-{page} (sebelum output) agar bisa redirect setelah simpan.
declare(strict_types=1);

function sgi_options_handle_save(): void {
  if (!current_user_can('edit_theme_options')) wp_die('Anda tidak memiliki izin untuk mengubah konten situs.');
  if (!isset($_POST['sgi_save']) && !isset($_POST['sgi_seed_nav'])) return;
  $isSeed = isset($_POST['sgi_seed_nav']);
  if ($isSeed) {
    check_admin_referer('sgi_opts', '_sgi_seed_nonce');
  } else {
    check_admin_referer('sgi_opts');
  }
  $tab = sgi_options_current_tab(wp_unslash($_POST['sgi_tab'] ?? 'header'));

  // Aksi terpisah: isi Menu lokasi primary dengan tiga label bawaan.
  if ($isSeed) {
    $ok = sgi_seed_primary_menu();
    sgi_options_redirect($tab, $ok ? ['seeded' => '1'] : ['sgi_err' => 'seed']);
    return;
  }

  $settings = [];
  foreach (sgi_settings_fields() as $key => $field) {
    $settings[$key] = sanitize_text_field(wp_unslash($_POST['sgi_' . $key] ?? ''));
  }
  if (!is_email($settings['email'])) {
    sgi_options_redirect($tab, ['sgi_err' => 'email']);
    return;
  }
  update_option('sgi_settings', array_merge((array)get_option('sgi_settings', []), $settings));

  // Label navigasi: hanya judul item yang diubah (URL/struktur tetap di Menu).
  $navResult = ['saved' => 0, 'failed' => 0];
  $navLabels = wp_unslash($_POST['sgi_nav_label'] ?? []);
  if (is_array($navLabels)) $navResult = sgi_save_nav_labels($navLabels);

  $txt = function (string $k): string {
    return sanitize_text_field(wp_unslash($_POST[$k] ?? ''));
  };
  $kpiPairs = sgi_pair_lines(wp_unslash($_POST['sgi_kpi'] ?? ''), 2);
  $statPairs = sgi_pair_lines(wp_unslash($_POST['sgi_stats'] ?? ''), 2);
  $whyPairs = sgi_pair_lines(wp_unslash($_POST['sgi_why'] ?? ''), 2);
  $stepTriples = sgi_pair_lines(wp_unslash($_POST['sgi_steps'] ?? ''), 3);
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
    'home.why' => $whyPairs, 'home.steps' => $stepTriples, 'home.testi' => $testiPairs,
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
      'card1t' => wp_kses(wp_unslash($_POST['sgi_hero_card1t'] ?? ''), ['a' => ['href' => [], 'target' => [], 'rel' => []], 'span' => []]),
      'card1a' => wp_kses(wp_unslash($_POST['sgi_hero_card1a'] ?? ''), ['a' => ['href' => [], 'target' => [], 'rel' => []], 'span' => []]),
      'card1a_link_text' => $txt('sgi_hero_card1a_link_text'),
      'card1a_link_url' => $txt('sgi_hero_card1a_link_url'),
      'card1b' => wp_kses(wp_unslash($_POST['sgi_hero_card1b'] ?? ''), ['a' => ['href' => [], 'target' => [], 'rel' => []], 'span' => []]),
      'card1b_link_text' => $txt('sgi_hero_card1b_link_text'),
      'card1b_link_url' => $txt('sgi_hero_card1b_link_url'),
      'card1c' => wp_kses(wp_unslash($_POST['sgi_hero_card1c'] ?? ''), ['a' => ['href' => [], 'target' => [], 'rel' => []], 'span' => []]),
      'card1c_link_text' => $txt('sgi_hero_card1c_link_text'),
      'card1c_link_url' => $txt('sgi_hero_card1c_link_url'),
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
    'steps' => array_map(function ($p) { return ['t' => (string)($p[0] ?? ''), 'd' => (string)($p[1] ?? ''), 'icon' => (string)($p[2] ?? '')]; }, $stepTriples),
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

  sgi_options_redirect($tab, $navResult['failed'] > 0 ? ['sgi_err' => 'nav'] : ['updated' => '1']);
}
