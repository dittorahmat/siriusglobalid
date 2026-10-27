<?php
// inc/options-fields.php - field helper + panel render untuk Tampilan > Konten Situs.
// Dipisah dari inc/options.php agar registration/render skeleton dan handler
// simpan punya satu alasan masing-masing untuk berubah.
declare(strict_types=1);

function sgi_opt_input(string $name, string $label, string $value, string $placeholder = ''): void {
  $ph = $placeholder !== '' ? ' placeholder="' . esc_attr($placeholder) . '"' : '';
  echo '<p><label><strong>' . esc_html($label) . '</strong><br><input type="text" name="' . esc_attr($name) . '" value="' . esc_attr($value) . '"' . $ph . ' class="regular-text" style="max-width:640px;width:100%"></label></p>';
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

// Panel dari sgi_settings_fields() untuk satu panel ('header' atau 'kontak').
function sgi_opt_settings_fields(string $panel, array $raw): void {
  foreach (sgi_settings_fields() as $key => $field) {
    if (($field['panel'] ?? '') !== $panel) continue;
    sgi_opt_input('sgi_' . $key, (string)$field['label'], (string)($raw[$key] ?? ''), (string)($field['placeholder'] ?? ''));
  }
}

function sgi_options_panel_header(array $s): void {
  $nav = sgi_nav_menu_for_edit();
  echo '<section class="sgi-panel" id="sgi-panel-header" aria-labelledby="sgi-tab-header" data-sgi-panel="header">';
  echo '<h2>Logo</h2>';
  echo '<p class="description">Ganti logo header/footer via <a href="' . esc_url(admin_url('customize.php?autofocus[section]=title_tagline')) . '">Tampilan &gt; Sesuaikan &gt; Identitas Situs</a> (Logo + Logo footer varian terang). Boleh lockup landscape penuh; jika kosong, tampil bintang + teks bawaan. Favicon via Ikon Situs di halaman yang sama.</p>';

  echo '<h2>Warna Latar Footer</h2>';
  $footerBg = get_theme_mod('sgi_footer_bg', '#0b2447');
  sgi_opt_input('sgi_footer_bg', 'Warna Latar Footer (Hex: #0b2447)', (string)$footerBg, '#0b2447');

  echo '<h2>Label navigasi</h2>';
  if ($nav['items']) {
    echo '<p class="description">Ubah teks yang tampil di header. Tautan, urutan, dan struktur tetap diatur di Tampilan &gt; Menu.</p>';
    echo '<table class="form-table" role="presentation"><tbody>';
    foreach ($nav['items'] as $it) {
      $isChild = ((int)$it['parent'] > 0);
      echo '<tr' . ($isChild ? ' class="sgi-nav-child"' : '') . '><th scope="row"><label for="sgi-nav-' . (int)$it['id'] . '">' . esc_html($it['title']) . '</label></th>';
      echo '<td><input type="text" class="regular-text" id="sgi-nav-' . (int)$it['id'] . '" name="sgi_nav_label[' . (int)$it['id'] . ']" value="' . esc_attr($it['title']) . '">';
      echo '<p class="description">' . esc_html($it['url']) . '</p></td></tr>';
    }
    echo '</tbody></table>';
    echo '<p><a class="button" href="' . esc_url(admin_url('nav-menus.php')) . '">Atur struktur menu</a></p>';
  } else {
    echo '<p>Situs memakai tiga label bawaan karena Menu lokasi <strong>Navigasi utama</strong> belum dibuat atau masih kosong.</p>';
    echo '<ul class="sgi-nav-preview">';
    foreach (sgi_default_nav() as $n) {
      echo '<li>' . esc_html($n['label']) . ' <span class="description">' . esc_html($n['href']) . '</span></li>';
    }
    echo '</ul>';
    echo '<p><button type="submit" class="button" form="sgi-seed" name="sgi_seed_nav" value="1">Buat menu bawaan</button></p>';
    echo '<p class="description">Setelah menu dibuat, ketiga label bisa diubah di sini.</p>';
  }

  echo '<h2>Tombol header</h2>';
  sgi_opt_settings_fields('header', $s);
  echo '<p class="description">Tombol ini tampil di header desktop dan menu seluler. Kosongkan untuk memakai bawaan "Konsultasi Gratis" dan /kontak/.</p>';
  echo '</section>';
}

function sgi_options_panel_hero(array $h): void {
  $hero = $h['hero'];
  echo '<section class="sgi-panel" id="sgi-panel-hero" aria-labelledby="sgi-tab-hero" data-sgi-panel="hero">';
  echo '<h2>Hero</h2>';
  sgi_opt_input('sgi_hero_eyebrow', 'Eyebrow', (string)($hero['eyebrow'] ?? ''));
  sgi_opt_area('sgi_hero_title', 'Judul (boleh pakai <span> untuk aksen)', (string)($hero['title_html'] ?? ''), '', 2);
  sgi_opt_area('sgi_hero_lead', 'Lead', (string)($hero['lead'] ?? ''), '', 2);
  sgi_opt_input('sgi_hero_cta1', 'Tombol 1', (string)($hero['cta1'] ?? ''));
  sgi_opt_input('sgi_hero_cta1_href', 'Link tombol 1 (/kontak/ atau https://...)', (string)($hero['cta1_href'] ?? ''), '/kontak/');
  sgi_opt_input('sgi_hero_cta2', 'Tombol 2', (string)($hero['cta2'] ?? ''));
  sgi_opt_input('sgi_hero_cta2_href', 'Link tombol 2', (string)($hero['cta2_href'] ?? ''), '/layanan/');
  sgi_opt_input('sgi_hero_card1t', 'Judul kartu status', (string)($hero['card1t'] ?? ''));
  
  echo '<div style="background:#fff;border:1px solid #c3c4c7;border-radius:8px;padding:12px;margin:12px 0;max-width:640px">';
  echo '<strong>Status Baris 1</strong>';
  sgi_opt_input('sgi_hero_card1a', 'Teks baris 1', (string)($hero['card1a'] ?? ''), 'AI doing Administrative Faster 29%');
  sgi_opt_input('sgi_hero_card1a_link_text', 'Kata yang jadi link (opsional)', (string)($hero['card1a_link_text'] ?? ''), 'Microsoft');
  sgi_opt_input('sgi_hero_card1a_link_url', 'URL link (opsional)', (string)($hero['card1a_link_url'] ?? ''), 'https://...');
  echo '</div>';

  echo '<div style="background:#fff;border:1px solid #c3c4c7;border-radius:8px;padding:12px;margin:12px 0;max-width:640px">';
  echo '<strong>Status Baris 2</strong>';
  sgi_opt_input('sgi_hero_card1b', 'Teks baris 2', (string)($hero['card1b'] ?? ''), 'IoT reduce downtime 54%');
  sgi_opt_input('sgi_hero_card1b_link_text', 'Kata yang jadi link (opsional)', (string)($hero['card1b_link_text'] ?? ''), 'Deloitte');
  sgi_opt_input('sgi_hero_card1b_link_url', 'URL link (opsional)', (string)($hero['card1b_link_url'] ?? ''), 'https://...');
  echo '</div>';

  echo '<div style="background:#fff;border:1px solid #c3c4c7;border-radius:8px;padding:12px;margin:12px 0;max-width:640px">';
  echo '<strong>Status Baris 3</strong>';
  sgi_opt_input('sgi_hero_card1c', 'Teks baris 3', (string)($hero['card1c'] ?? ''), 'Cloud modernization lower ops costs 41%');
  sgi_opt_input('sgi_hero_card1c_link_text', 'Kata yang jadi link (opsional)', (string)($hero['card1c_link_text'] ?? ''), 'IDC');
  sgi_opt_input('sgi_hero_card1c_link_url', 'URL link (opsional)', (string)($hero['card1c_link_url'] ?? ''), 'https://...');
  echo '</div>';
  echo '<h2>Angka &amp; klien</h2>';
  sgi_opt_area('sgi_kpi', 'KPI hero (3 baris "angka | label")', sgi_opt_join_pairs((array)$h['kpi_counts'], (array)$h['kpi_labels']), '', 3);
  sgi_opt_area('sgi_stats', 'Statistik (4 baris "angka | label")', sgi_opt_join_pairs((array)$h['stat_counts'], (array)$h['stat_labels']), '', 4);
  sgi_opt_area('sgi_clients', 'Klien (satu per baris, maks 12)', implode("\n", (array)$h['clients']), '', 6);
  echo '</section>';
}

function sgi_options_panel_sections(array $h): void {
  $cta = $h['cta'];
  echo '<section class="sgi-panel" id="sgi-panel-sections" aria-labelledby="sgi-tab-sections" data-sgi-panel="sections">';
  echo '<h2>Urutan section beranda</h2>';
  sgi_opt_input('sgi_home_order', 'Urutan (pisah koma)', implode(',', sgi_home_order($h['home_order'] ?? [])));
  echo '<p class="description">Nama valid: <code>hero,stats,layanan,why,alur,cases,testi,cta</code>. Hapus nama untuk menyembunyikan section. Contoh: <code>hero,layanan,alur,cases,cta</code>.</p>';
  echo '<h2>Layanan (otomatis dari menu Layanan)</h2>';
  sgi_opt_input('sgi_svc_eye', 'Eyebrow', (string)($h['svc_eye'] ?? ''));
  sgi_opt_input('sgi_svc_t', 'Judul', (string)($h['svc_t'] ?? ''));
  sgi_opt_area('sgi_svc_d', 'Deskripsi', (string)($h['svc_d'] ?? ''), '', 2);
  sgi_opt_input('sgi_combo_t', 'Judul kartu kombinasi', (string)($h['combo_t'] ?? ''));
  sgi_opt_area('sgi_combo_d', 'Deskripsi kartu kombinasi', (string)($h['combo_d'] ?? ''), '', 2);
  sgi_opt_input('sgi_combo_btn', 'Tombol kartu kombinasi', (string)($h['combo_btn'] ?? ''));
  sgi_opt_input('sgi_layanan_count', 'Jumlah kartu (1-24)', (string)($h['layanan_count'] ?? 5));
  sgi_opt_area('sgi_layanan_hide', 'Sembunyikan slug (satu per baris)', implode("\n", (array)($h['layanan_hide'] ?? [])));
  echo '<p><label><input type="checkbox" name="sgi_layanan_combo" value="1"' . checked((string)($h['layanan_combo'] ?? '1'), '1', false) . '> Tampilkan kartu kombinasi</label></p>';
  echo '<h2>Keunggulan</h2>';
  sgi_opt_input('sgi_why_t', 'Judul', (string)($h['why_t'] ?? ''));
  sgi_opt_area('sgi_why', 'Item (satu per baris "judul | deskripsi", maks 6)', implode("\n", array_map(function ($w) { return ($w['t'] ?? '') . ' | ' . ($w['d'] ?? ''); }, (array)$h['why'])), '', 6);
  echo '<h2>Alur kerja</h2>';
  sgi_opt_input('sgi_proc_t', 'Judul', (string)($h['proc_t'] ?? ''));
  sgi_opt_area('sgi_steps', 'Tahapan / Solusi Inovatif (satu per baris "judul | deskripsi | icon", maks 8)', implode("\n", array_map(function ($st) { $ic = trim((string)($st['icon'] ?? '')); return ($st['t'] ?? '') . ' | ' . ($st['d'] ?? '') . ($ic !== '' ? ' | ' . $ic : ''); }, (array)$h['steps'])), 'Pilihan nama icon library: ai, bot, sales, legal, finance, cloud, backup, iot, chart, server, shield, code. Atau masukkan URL gambar icon (https://...).', 8);
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
  echo '</section>';
}

function sgi_options_panel_kontak(array $s): void {
  echo '<section class="sgi-panel" id="sgi-panel-kontak" aria-labelledby="sgi-tab-kontak" data-sgi-panel="kontak">';
  echo '<h2>Kontak &amp; situs</h2>';
  sgi_opt_settings_fields('kontak', $s);
  echo '<h2>Halaman Kontak</h2>';
  echo '<p class="description">Kosongkan isian di bawah untuk memakai bawaan; yang tampil adalah nilai tersimpan.</p>';
  // Tampilkan nilai tersimpan mentah (bukan hasil merge) agar kolom yang
  // dikosongkan benar-benar terlihat kosong.
  $kRaw = function_exists('get_option') ? (array)get_option('sgi_kontak', []) : [];
  $kDef = function_exists('sgi_kontak_defaults') ? sgi_kontak_defaults() : [];
  $kVal = function (string $key) use ($kRaw, $kDef): string {
    return array_key_exists($key, $kRaw) ? (string)$kRaw[$key] : (string)($kDef[$key] ?? '');
  };
  $kPairs = function (string $key) use ($kRaw, $kDef): string {
    if (!function_exists('sgi_kontak_pairs_text')) return '';
    $pairs = (array_key_exists($key, $kRaw) && is_array($kRaw[$key])) ? $kRaw[$key] : ($kDef[$key] ?? []);
    return sgi_kontak_pairs_text((array)$pairs);
  };
  sgi_opt_input('sgi_k_hero_h1', 'Judul hero Kontak', $kVal('hero_h1'));
  sgi_opt_input('sgi_k_form_t', 'Judul formulir', $kVal('form_t'));
  sgi_opt_area('sgi_k_form_d', 'Pengantar formulir', $kVal('form_d'), '', 2);
  sgi_opt_area('sgi_k_needs', 'Kebutuhan (satu per baris "nilai | label", maks 20)', $kPairs('needs'), 'Nilai dipakai untuk praseleksi ?layanan=. Contoh: app-development | Application Development', 10);
  sgi_opt_area('sgi_k_budgets', 'Budget (satu per baris "nilai | label", maks 20)', $kPairs('budgets'), '', 5);
  sgi_opt_area('sgi_k_times', 'Target waktu (satu per baris "nilai | label", maks 20)', $kPairs('times'), '', 3);
  echo '</section>';
}
