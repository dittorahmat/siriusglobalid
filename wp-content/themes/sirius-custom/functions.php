<?php
// functions.php - bootstrap theme Sirius Custom (ID-only).
declare(strict_types=1);

require_once __DIR__ . '/inc/helpers.php';
require_once __DIR__ . '/inc/logo.php';
require_once __DIR__ . '/inc/cpt.php';
require_once __DIR__ . '/inc/meta.php';
require_once __DIR__ . '/inc/options-fields.php';
require_once __DIR__ . '/inc/options-save.php';
require_once __DIR__ . '/inc/options.php';
require_once __DIR__ . '/inc/admin.php';
require_once __DIR__ . '/inc/redirects.php';
require_once __DIR__ . '/inc/import-page.php';

function sgi_setup(): void {
  add_theme_support('title-tag');
  add_theme_support('post-thumbnails');
  add_theme_support('custom-logo', [
    'height' => 60,
    'width' => 240,
    'flex-height' => true,
    'flex-width' => true,
    'header-text' => ['site-title', 'site-description'],
  ]);
  register_nav_menus(['primary' => 'Navigasi utama']);
}
add_action('after_setup_theme', 'sgi_setup');

// Seed Menu bawaan (Beranda, Layanan, Kontak) saat theme diaktifkan.
// Idempoten: tidak menimpa Menu editor yang sudah ada.
add_action('after_switch_theme', 'sgi_seed_primary_menu');

function sgi_assets(): void {
  $uri = get_stylesheet_directory_uri();
  $dir = get_stylesheet_directory();
  $ver = wp_get_theme()->get('Version');
  foreach (['tokens', 'base', 'components', 'pages'] as $css) {
    $rel = 'assets/css/' . $css . '.css';
    wp_enqueue_style('sgi-' . $css, $uri . '/' . $rel, [], file_exists($dir . '/' . $rel) ? (string)filemtime($dir . '/' . $rel) : $ver);
  }
  wp_enqueue_script('sgi-main', $uri . '/assets/js/main.js', [], file_exists($dir . '/assets/js/main.js') ? (string)filemtime($dir . '/assets/js/main.js') : $ver, true);
  $settings = get_option('sgi_settings', []);
  wp_localize_script('sgi-main', 'SGI_SETTINGS', [
    'phoneHref' => (string)($settings['phoneHref'] ?? 'https://wa.me/6281510481010'),
  ]);
}
add_action('wp_enqueue_scripts', 'sgi_assets');

// Opsi site: settings + home (diisi importer, bisa diubah via Tampilan > Konten Situs).
// Satu sumber daftar field: dipakai handler simpan dan render panel.
function sgi_settings_fields(): array {
  return [
    'name' => ['label' => 'Nama', 'panel' => 'kontak'],
    'email' => ['label' => 'Email', 'panel' => 'kontak'],
    'phone' => ['label' => 'Telepon', 'panel' => 'kontak'],
    'phoneHref' => ['label' => 'Link WA', 'panel' => 'kontak'],
    'address' => ['label' => 'Alamat', 'panel' => 'kontak'],
    'hours' => ['label' => 'Jam', 'panel' => 'kontak'],
    'mapEmbed' => ['label' => 'Embed peta', 'panel' => 'kontak'],
    'ctaLabel' => ['label' => 'Label tombol', 'panel' => 'header', 'placeholder' => 'Konsultasi Gratis'],
    'ctaHref' => ['label' => 'Tautan tombol (/kontak/ atau https://...)', 'panel' => 'header', 'placeholder' => '/kontak/'],
  ];
}

function sgi_settings_defaults(): array {
  return [
    'name' => 'PT Sirius Global Indonesia', 'email' => 'sales@siriusglobal.id',
    'phone' => '+62 815-1048-1010', 'phoneHref' => 'https://wa.me/6281510481010',
    'address' => 'Gedung Wirausaha, Jl. HR Rasuna Said Kav. C5, Kuningan, Jakarta Selatan 12920',
    'hours' => 'Senin-Jumat, 09.00-18.00 WIB',
    'mapEmbed' => 'https://www.google.com/maps?q=Gedung+Wirausaha+HR+Rasuna+Said+Kav+C5+Jakarta+Selatan&output=embed',
    'ctaLabel' => 'Konsultasi Gratis', 'ctaHref' => '/kontak/',
  ];
}

function sgi_settings(): array {
  $defaults = sgi_settings_defaults();
  $out = array_merge($defaults, (array)get_option('sgi_settings', []));
  // Kosong = bawaan (khusus CTA header, agar field yang dikosongkan tidak
  // menghasilkan tombol tanpa teks/tautan).
  foreach (['ctaLabel', 'ctaHref'] as $k) {
    if (trim((string)($out[$k] ?? '')) === '') $out[$k] = $defaults[$k];
  }
  return $out;
}

// Nilai mentah untuk form: kunci yang belum pernah disimpan memakai default,
// kunci yang dikosongkan tetap tampil kosong (kosong = bawaan saat dibaca).
function sgi_settings_raw(): array {
  $raw = (array)get_option('sgi_settings', []);
  $defaults = sgi_settings_defaults();
  $out = [];
  foreach (sgi_settings_fields() as $key => $field) {
    $out[$key] = array_key_exists($key, $raw) ? (string)$raw[$key] : (string)($defaults[$key] ?? '');
  }
  return $out;
}

// Tombol CTA header: satu akses untuk label + href yang sudah dinormalisasi.
function sgi_header_cta(): array {
  $s = sgi_settings();
  return [
    'label' => (string)$s['ctaLabel'],
    'href' => sgi_home_href((string)$s['ctaHref'], '/kontak/'),
  ];
}

function sgi_home(): array {
  $defaults = [
    'hero_photo' => ['src' => 'https://picsum.photos/seed/sirius-engineering-team/880/1000', 'alt' => 'Tim engineer Sirius Global Indonesia mengerjakan proyek di kantor', 'w' => 880, 'h' => 1000],
    'hero' => [
      'eyebrow' => 'Konsultan IT · Jakarta, Indonesia',
      'title_html' => 'Aplikasi, AI dan IoT <span>siap produksi</span> untuk bisnis Anda',
      'lead' => 'Kami membantu perusahaan merancang, membangun, dan mengoperasikan solusi digital: aplikasi, AI, IoT, ERP Odoo, dan fleet management.',
      'cta1' => 'Konsultasi Gratis', 'cta1_href' => '/kontak/',
      'cta2' => 'Lihat Layanan', 'cta2_href' => '/layanan/',
      'card1t' => 'Status implementasi',
      'card1a' => 'Odoo ERP, go-live 92%',
      'card1b' => 'AI Vision, akurasi 97,4%',
      'card1c' => 'Fleet GPS, 1.240 unit aktif',
    ],
    'kpi_counts' => ['18', '9', '40'],
    'kpi_labels' => ['Proyek berjalan', 'Tahun pengalaman', 'Klien aktif'],
    'stat_counts' => ['120', '40', '9', '60'],
    'stat_labels' => ['Proyek diselesaikan', 'Klien aktif', 'Tahun pengalaman', 'Modul Odoo live'],
    'clients' => ['Manufaktur', 'Logistik dan Distribusi', 'Pertambangan', 'Ritel', 'Pemerintahan', 'Agribisnis'],
    'svc_eye' => 'Layanan utama',
    'svc_t' => 'Satu partner untuk seluruh kebutuhan digital',
    'svc_d' => 'Dari aplikasi hingga operasional armada, semuanya dirancang agar saling terhubung dan mudah dirawat.',
    'combo_t' => 'Butuh kombinasi?',
    'combo_d' => 'Contoh: Odoo + GPS armada + AI prediksi maintenance. Kami rancang arsitekturnya.',
    'combo_btn' => 'Lihat semua layanan',
    'layanan_count' => 5,
    'layanan_hide' => [],
    'layanan_combo' => '1',
    'why_t' => 'Konsultan yang ikut bertanggung jawab pada hasil',
    'why' => [
      ['t' => 'Senior, in-house', 'd' => 'Tidak dilempar ke freelancer. Arsitek dan PM mendampingi dari discovery sampai go-live.'],
      ['t' => 'Terukur sejak awal', 'd' => 'Setiap proposal mencantumkan KPI: waktu, biaya, dan dampak bisnis yang bisa diverifikasi.'],
      ['t' => 'Dokumentasi & serah terima', 'd' => 'Source code, kredensial, dan SOP diserahkan penuh. Tidak ada vendor lock-in tersembunyi.'],
      ['t' => 'Support purna jual', 'd' => 'SLA respons jelas dan opsi retainer bulanan untuk pengembangan lanjutan.'],
    ],
    'proc_t' => 'Alur yang transparan, tanpa kejutan',
    'steps' => [
      ['t' => 'Discovery', 'd' => 'Workshop 1-2 minggu: proses bisnis, data, dan target sukses.'],
      ['t' => 'Blueprint', 'd' => 'Arsitektur, estimasi terbuka, dan prototipe yang bisa diklik.'],
      ['t' => 'Build & QA', 'd' => 'Sprint mingguan, demo rutin, testing berlapis.'],
      ['t' => 'Go-live & Support', 'd' => 'Migrasi data, pelatihan, dan pendampingan pasca-rilis.'],
    ],
    'cases_eye' => 'Studi kasus',
    'cases_t' => 'Hasil yang bisa diverifikasi',
    'cases_all' => 'Lihat portofolio',
    'cases' => [
      ['c' => 'ERP Odoo untuk Manufaktur', 't' => 'Go-live 6 modul dalam 14 minggu', 'd' => 'Stok akurat, closing 3x lebih cepat, dan audit trail penuh.'],
      ['c' => 'AI Vision untuk Logistik', 't' => 'QC otomatis dengan akurasi 97,4%', 'd' => 'Inspeksi kemasan dari 4 menit menjadi 20 detik per pallet.'],
      ['c' => 'Fleet untuk Distribusi', 't' => '1.240 unit terpantau real-time', 'd' => 'BBM turun 11% dan keterlambatan turun 38% dalam 6 bulan.'],
    ],
    'case_imgs' => [
      ['img' => 'https://picsum.photos/seed/odoo-erp-factory/640/400', 'alt' => 'Pabrik yang memakai ERP Odoo untuk inventaris'],
      ['img' => 'https://picsum.photos/seed/ai-vision-warehouse/640/400', 'alt' => 'Inspeksi kemasan otomatis dengan computer vision di gudang'],
      ['img' => 'https://picsum.photos/seed/fleet-trucks-highway/640/400', 'alt' => 'Armada truk distribusi yang terpantau GPS'],
    ],
    'testi_t' => 'Kata mereka yang sudah jalan bareng',
    'testis' => [
      ['q' => '"Tim Sirius rapi soal dokumentasi. Serah terima beres, tim internal kami bisa lanjut sendiri."', 'by' => 'Operations Director · Perusahaan Manufaktur'],
      ['q' => '"POC AI-nya jujur. Yang tidak feasible dibilang tidak feasible. Itu langka."', 'by' => 'Head of Logistics · Perusahaan Distribusi'],
    ],
    'cta' => [
      't' => 'Punya target digital tahun ini? Diskusikan dulu, gratis.',
      'd' => 'Ceritakan proses bisnis Anda dalam 30 menit. Kami balas dengan rekomendasi dan estimasi kasar, tanpa komitmen.',
      'b1' => 'Konsultasi Gratis', 'b2' => 'Chat WhatsApp',
    ],
    'home_order' => ['hero', 'stats', 'layanan', 'why', 'alur', 'cases', 'testi', 'cta'],
  ];
  $saved = (array)get_option('sgi_home', []);
  // Gabung dalam untuk sub-array asosiatif agar simpanan parsial tidak menghapus default.
  foreach (['hero', 'cta'] as $k) {
    if (isset($saved[$k]) && is_array($saved[$k])) {
      $saved[$k] = array_merge($defaults[$k], $saved[$k]);
    }
  }
  return array_merge($defaults, $saved);
}

// Normalisasi href admin: URL penuh dipakai apa adanya, path relatif via home_url().
function sgi_home_href(string $href, string $fallback = '/kontak/'): string {
  $href = trim($href);
  if ($href === '') $href = $fallback;
  if (preg_match('#^https?://#i', $href)) return $href;
  return home_url($href);
}

// Kartu layanan homepage: otomatis dari CPT layanan (publish, urut judul),
// dikecualikan slug di layanan_hide, dibatasi layanan_count. Fallback ke
// 5 kartu bawaan bila CPT kosong agar homepage tidak pernah bolong.
function sgi_home_service_cards(array $home): array {
  $fallback = [
    ['icon' => '◈', 'cls' => '', 't' => 'Application Development', 'd' => 'Web, mobile & API yang aman, cepat, dan mudah dikembangkan.', 'url' => home_url('layanan/app-development/')],
    ['icon' => '✦', 'cls' => ' teal', 't' => 'AI Implementation', 'd' => 'Chatbot, OCR, computer vision & prediksi yang terukur ROI-nya.', 'url' => home_url('layanan/ai-solution/')],
    ['icon' => '⬡', 'cls' => '', 't' => 'IoT Solutions', 'd' => 'Sensor, gateway & dashboard real-time untuk pabrik dan logistik.', 'url' => home_url('layanan/iot/')],
    ['icon' => '▣', 'cls' => ' teal', 't' => 'ERP Odoo', 'd' => 'Implementasi, migrasi & kustom modul Odoo sesuai proses bisnis.', 'url' => home_url('layanan/odoo-erp/')],
    ['icon' => '⬢', 'cls' => '', 't' => 'Fleet Management', 'd' => 'GPS tracking, BBM, maintenance & driver scoring dalam satu dasbor.', 'url' => home_url('layanan/fleet-management/')],
  ];
  if (!function_exists('get_posts')) return $fallback;
  $count = max(1, min(8, (int)($home['layanan_count'] ?? 5)));
  $hide = array_map('strtolower', array_map('trim', (array)($home['layanan_hide'] ?? [])));
  $posts = get_posts([
    'post_type' => 'layanan', 'post_status' => 'publish',
    'numberposts' => 50, 'orderby' => 'title', 'order' => 'ASC',
    'suppress_filters' => true,
  ]);
  if (!is_array($posts) || !$posts) return $fallback;
  $icons = [['◈', ''], ['✦', ' teal'], ['⬡', ''], ['▣', ' teal'], ['⬢', ''], ['⬣', ' teal'], ['⬔', ''], ['⬓', ' teal']];
  $cards = [];
  foreach ($posts as $p) {
    if (!isset($p->post_name) || in_array(strtolower((string)$p->post_name), $hide, true)) continue;
    $desc = (string)get_post_meta((int)$p->ID, '_sgi_lead', true);
    if ($desc === '') {
      $desc = (string)wp_trim_words((string)($p->post_excerpt ?: $p->post_content), 18);
    }
    $slot = $icons[count($cards) % count($icons)];
    $cards[] = [
      'icon' => $slot[0], 'cls' => $slot[1],
      't' => (string)$p->post_title, 'd' => $desc,
      'url' => get_permalink((int)$p->ID),
    ];
    if (count($cards) >= $count) break;
  }
  return $cards ?: $fallback;
}
