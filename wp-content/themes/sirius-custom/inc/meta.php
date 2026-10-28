<?php
// inc/meta.php - Meta fields layanan ID-only + validasi batas list.
// Pola editor: field teks untuk judul/lead/h1/meta, textarea satu-baris-per-item
// untuk checklist, textarea format "label | value" per baris untuk pasangan,
// dan textarea JSON untuk tiers/steps/faq/cta. Semua tervalidasi saat simpan.
declare(strict_types=1);

function sgi_layanan_meta_keys(): array {
  return [
    '_sgi_h1', '_sgi_meta_desc', '_sgi_lead',
    '_sgi_intro_heading', '_sgi_intro_body', '_sgi_checklist',
    '_sgi_metrics', '_sgi_detail_image', '_sgi_pricing_heading', '_sgi_pricing_body',
    '_sgi_quote_btn', '_sgi_tiers',
    '_sgi_steps_heading', '_sgi_steps',
    '_sgi_faqs', '_sgi_cta_title', '_sgi_cta_desc', '_sgi_cta_btn', '_sgi_cta_href',
  ];
}

function sgi_register_meta(): void {
  foreach (sgi_layanan_meta_keys() as $key) {
    register_post_meta('layanan', $key, [
      'type' => 'string',
      'single' => true,
      'show_in_rest' => true,
      'auth_callback' => function () { return current_user_can('edit_posts'); },
    ]);
  }
  register_post_meta('portfolio', '_sgi_cat_label', [
    'type' => 'string', 'single' => true, 'show_in_rest' => true,
    'auth_callback' => function () { return current_user_can('edit_posts'); },
  ]);
}
add_action('init', 'sgi_register_meta');

function sgi_layanan_boxes(): void {
  add_meta_box('sgi-hero', 'Hero Layanan', 'sgi_box_hero', 'layanan', 'normal', 'high');
  add_meta_box('sgi-intro', 'Intro + Checklist + Metrik + Gambar', 'sgi_box_intro', 'layanan', 'normal', 'default');
  add_meta_box('sgi-pricing', 'Harga (tiers JSON)', 'sgi_box_pricing', 'layanan', 'normal', 'default');
  add_meta_box('sgi-steps', 'Tahapan + FAQ + CTA', 'sgi_box_steps', 'layanan', 'normal', 'default');
}
add_action('add_meta_boxes', 'sgi_layanan_boxes');

function sgi_layanan_admin_scripts(string $hook): void {
  global $post_type;
  if (!in_array($hook, ['post.php', 'post-new.php'], true) || $post_type !== 'layanan') {
    return;
  }
  wp_enqueue_media();
  $uri = get_stylesheet_directory_uri();
  $dir = get_stylesheet_directory();
  $ver = wp_get_theme()->get('Version');
  $js = '/assets/js/admin.js';
  wp_enqueue_script('sgi-admin-media', $uri . $js, ['jquery'], file_exists($dir . $js) ? (string)filemtime($dir . $js) : $ver, true);
}
add_action('admin_enqueue_scripts', 'sgi_layanan_admin_scripts');

function sgi_field(string $name, string $label, string $value, string $type = 'text'): void {
  $n = esc_attr($name); $v = esc_attr($value);
  if ($type === 'area') {
    echo '<p><label><strong>' . esc_html($label) . '</strong><br><textarea name="' . $n . '" rows="3" style="width:100%">' . esc_textarea($value) . '</textarea></label></p>';
  } else {
    echo '<p><label><strong>' . esc_html($label) . '</strong><br><input type="text" name="' . $n . '" value="' . $v . '" style="width:100%"></label></p>';
  }
}

function sgi_box_hero($post): void {
  wp_nonce_field('sgi_save', 'sgi_nonce');
  sgi_field('_sgi_h1', 'H1 (bila beda dari judul)', (string)get_post_meta($post->ID, '_sgi_h1', true));
  sgi_field('_sgi_lead', 'Lead', (string)get_post_meta($post->ID, '_sgi_lead', true), 'area');
  sgi_field('_sgi_meta_desc', 'Meta description', (string)get_post_meta($post->ID, '_sgi_meta_desc', true));
}

function sgi_box_intro($post): void {
  sgi_field('_sgi_intro_heading', 'Judul intro', (string)get_post_meta($post->ID, '_sgi_intro_heading', true));
  sgi_field('_sgi_intro_body', 'Paragraf intro', (string)get_post_meta($post->ID, '_sgi_intro_body', true), 'area');
  sgi_field('_sgi_checklist', 'Checklist (satu per baris, maks 20)', (string)get_post_meta($post->ID, '_sgi_checklist', true), 'area');
  sgi_field('_sgi_metrics', 'Metrik (satu per baris "label | value", maks 8)', (string)get_post_meta($post->ID, '_sgi_metrics', true), 'area');
  sgi_image_field('_sgi_detail_image', 'Gambar Ilustrasi Detail Layanan (Tampil di samping intro)', (string)get_post_meta($post->ID, '_sgi_detail_image', true));
}

function sgi_image_field(string $name, string $label, string $value): void {
  $n = esc_attr($name);
  $v = esc_attr($value);
  $hasImg = !empty($value);
  echo '<p><label><strong>' . esc_html($label) . '</strong></label></p>';
  echo '<div class="sgi-media-field" style="margin-top:6px;display:flex;align-items:center;gap:12px">';
  echo '<input type="hidden" id="' . $n . '" name="' . $n . '" value="' . $v . '">';
  echo '<div id="' . $n . '_preview" style="max-width:180px;max-height:120px;border-radius:8px;overflow:hidden;border:1px solid #ccd0d4;background:#f0f0f1;' . ($hasImg ? '' : 'display:none;') . '">';
  echo '<img src="' . ($hasImg ? esc_url($value) : '') . '" style="max-width:100%;height:auto;display:block">';
  echo '</div>';
  echo '<div style="display:flex;gap:8px;align-items:center">';
  echo '<button type="button" class="button sgi-upload-btn" data-target="#' . $n . '">' . ($hasImg ? 'Ganti Gambar' : 'Pilih / Unggah Gambar') . '</button>';
  echo '<button type="button" class="button sgi-remove-btn" data-target="#' . $n . '" style="' . ($hasImg ? '' : 'display:none;') . 'color:#b32d2e;">Hapus</button>';
  echo '</div>';
  echo '</div>';
  echo '<p class="description" style="margin-top:4px">Gambar khusus untuk halaman detail layanan ini (tidak mempengaruhi Featured Image yang menjadi cover kartu di arsip).</p>';
}


function sgi_parse_tiers_input(string $raw): array {
  $raw = trim($raw);
  if ($raw === "") return [];
  // Jika formatnya sudah JSON (misal hasil lama), decode langsung
  if (str_starts_with($raw, "[") || str_starts_with($raw, "{")) {
    $decoded = json_decode($raw, true);
    if (is_array($decoded)) return $decoded;
  }
  // Format per baris: Tier | Nama | Harga | Fitur 1, Fitur 2 | featured
  $lines = preg_split("/\r?\n/", $raw);
  $tiers = [];
  foreach ($lines as $ln) {
    $ln = trim($ln);
    if ($ln === "") continue;
    $parts = array_map("trim", explode("|", $ln));
    if (count($parts) < 3) continue;
    $items = [];
    if (!empty($parts[3])) {
      $items = array_values(array_filter(array_map("trim", explode(",", $parts[3]))));
    }
    $isFeatured = isset($parts[4]) && in_array(strtolower($parts[4]), ["featured", "yes", "true", "1"], true);
    $tiers[] = [
      "tier" => $parts[0] ?? "",
      "name" => $parts[1] ?? "",
      "value" => $parts[2] ?? "",
      "items" => $items,
      "featured" => $isFeatured,
    ];
  }
  return $tiers;
}

function sgi_format_tiers_for_editor(string $saved): string {
  $saved = trim($saved);
  if ($saved === "") return "";
  $decoded = json_decode($saved, true);
  if (!is_array($decoded)) return $saved;
  $lines = [];
  foreach ($decoded as $t) {
    $items = implode(", ", (array)($t["items"] ?? []));
    $feat = !empty($t["featured"]) ? " | featured" : "";
    $lines[] = ($t["tier"] ?? "") . " | " . ($t["name"] ?? "") . " | " . ($t["value"] ?? "") . " | " . $items . $feat;
  }
  return implode("\n", $lines);
}

function sgi_box_pricing($post): void {
  sgi_field('_sgi_pricing_heading', 'Judul harga', (string)get_post_meta($post->ID, '_sgi_pricing_heading', true));
  sgi_field('_sgi_pricing_body', 'Subjudul harga', (string)get_post_meta($post->ID, '_sgi_pricing_body', true), 'area');
  sgi_field('_sgi_quote_btn', 'Teks tombol quotation', (string)get_post_meta($post->ID, '_sgi_quote_btn', true));
  $val = (string)get_post_meta($post->ID, '_sgi_tiers', true);
  $displayVal = sgi_format_tiers_for_editor($val);
  echo '<p><strong>Paket Harga (1 baris per paket, maks 6)</strong><br><textarea name="_sgi_tiers" rows="8" style="width:100%;font-family:monospace" placeholder="Mulai | Prototype | Rp 25-60 jt | Web dashboard, REST API dasar&#10;Populer | MVP Siap Rilis | Rp 75-180 jt | Web + mobile, Payment gateway | featured&#10;Enterprise | Scale | Custom quote | Multi-tenant, Audit ISO/SOC">' . esc_textarea($displayVal) . '</textarea></p>';
  echo '<p class="description">Format mudah (pisahkan dengan tanda |):<br><code>Tier | Nama Paket | Harga | Fitur 1, Fitur 2, Fitur 3 | featured (opsional)</code><br><em>*Boleh dikosongkan jika layanan tidak menggunakan paket harga.</em></p>';
}

function sgi_box_steps($post): void {
  sgi_field('_sgi_steps_heading', 'Judul tahapan', (string)get_post_meta($post->ID, '_sgi_steps_heading', true));
  sgi_field('_sgi_steps', 'Tahapan (satu per baris "judul | deskripsi", maks 8)', (string)get_post_meta($post->ID, '_sgi_steps', true), 'area');
  sgi_field('_sgi_faqs', 'FAQ (satu per baris "pertanyaan | jawaban", maks 20)', (string)get_post_meta($post->ID, '_sgi_faqs', true), 'area');
  sgi_field('_sgi_cta_title', 'Judul CTA', (string)get_post_meta($post->ID, '_sgi_cta_title', true));
  sgi_field('_sgi_cta_desc', 'Deskripsi CTA', (string)get_post_meta($post->ID, '_sgi_cta_desc', true), 'area');
  sgi_field('_sgi_cta_btn', 'Tombol CTA', (string)get_post_meta($post->ID, '_sgi_cta_btn', true));
  sgi_field('_sgi_cta_href', 'Link CTA (mis. /kontak/?layanan=' . esc_attr($post->post_name) . ')', (string)get_post_meta($post->ID, '_sgi_cta_href', true));
}

// Validasi batas saat simpan: tolak dengan pesan, data lama tidak rusak.
function sgi_save_layanan(int $post_id): void {
  if (get_post_type($post_id) !== 'layanan') return;
  if (!isset($_POST['sgi_nonce']) || !wp_verify_nonce($_POST['sgi_nonce'], 'sgi_save')) return;
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
  if (!current_user_can('edit_post', $post_id)) return;

  $lines = function (string $k): array {
    $raw = (string)($_POST[$k] ?? '');
    $out = [];
    foreach (preg_split('/\r?\n/', $raw) as $ln) {
      $ln = trim($ln);
      if ($ln !== '') $out[] = $ln;
    }
    return $out;
  };
  $checks = [
    'service.checklist' => $lines('_sgi_checklist'),
    'service.metrics' => $lines('_sgi_metrics'),
    'service.steps' => $lines('_sgi_steps'),
    'service.faqs' => $lines('_sgi_faqs'),
  ];
  foreach ($checks as $k => $items) {
    if ($m = sgi_over_limit($k, $items)) {
      wp_die(esc_html($m . ' Data tidak disimpan.'));
    }
  }
  $raw_tiers = trim((string)($_POST['_sgi_tiers'] ?? ''));
  $tiers = sgi_parse_tiers_input($raw_tiers);
  if ($m = sgi_over_limit('service.tiers', $tiers)) wp_die(esc_html($m . ' Data tidak disimpan.'));

  remove_action('save_post_layanan', 'sgi_save_layanan');
  foreach (sgi_layanan_meta_keys() as $key) {
    if ($key === '_sgi_tiers') {
      update_post_meta($post_id, $key, wp_json_encode($tiers));
    } elseif (array_key_exists($key, $_POST)) {
      update_post_meta($post_id, $key, wp_kses_post(wp_unslash($_POST[$key])));
    }
  }
}
add_action('save_post_layanan', 'sgi_save_layanan');
