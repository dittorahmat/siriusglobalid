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
    '_sgi_metrics', '_sgi_pricing_heading', '_sgi_pricing_body',
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
  add_meta_box('sgi-intro', 'Intro + Checklist + Metrik', 'sgi_box_intro', 'layanan', 'normal', 'default');
  add_meta_box('sgi-pricing', 'Harga (tiers JSON)', 'sgi_box_pricing', 'layanan', 'normal', 'default');
  add_meta_box('sgi-steps', 'Tahapan + FAQ + CTA', 'sgi_box_steps', 'layanan', 'normal', 'default');
}
add_action('add_meta_boxes', 'sgi_layanan_boxes');

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
}

function sgi_box_pricing($post): void {
  sgi_field('_sgi_pricing_heading', 'Judul harga', (string)get_post_meta($post->ID, '_sgi_pricing_heading', true));
  sgi_field('_sgi_pricing_body', 'Subjudul harga', (string)get_post_meta($post->ID, '_sgi_pricing_body', true), 'area');
  sgi_field('_sgi_quote_btn', 'Teks tombol quotation', (string)get_post_meta($post->ID, '_sgi_quote_btn', true));
  echo '<p><strong>Tiers (JSON, maks 6; satu boleh "featured": true)</strong><br><textarea name="_sgi_tiers" rows="8" style="width:100%;font-family:monospace">' . esc_textarea((string)get_post_meta($post->ID, '_sgi_tiers', true)) . '</textarea></p>';
  echo '<p class="description">Format tiap tier: {"tier":"Mulai","name":"Prototype","value":"Rp 25-60 jt","items":["a","b"],"featured":false}</p>';
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
  $tiers = json_decode((string)($_POST['_sgi_tiers'] ?? '[]'), true);
  if (!is_array($tiers)) wp_die('Tiers bukan JSON valid. Data tidak disimpan.');
  if ($m = sgi_over_limit('service.tiers', $tiers)) wp_die(esc_html($m . ' Data tidak disimpan.'));

  remove_action('save_post_layanan', 'sgi_save_layanan');
  foreach (sgi_layanan_meta_keys() as $key) {
    if (array_key_exists($key, $_POST)) {
      update_post_meta($post_id, $key, wp_kses_post(wp_unslash($_POST[$key])));
    }
  }
}
add_action('save_post_layanan', 'sgi_save_layanan');
