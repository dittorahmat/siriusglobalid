<?php
// front-page.php - port templates/content-home.php, ID-only.
// Copy section memakai string ID dari i18n.id.json (baked, bukan i18n runtime).
get_header();
$h = sgi_home();
$hp = $h['hero_photo']; $kpis = $h['kpi_counts']; $stats = $h['stat_counts'];
$clients = $h['clients']; $caseImgs = array_slice($h['case_imgs'], 0, 3);
$svcCards = [
  ['icon' => '◈', 'cls' => '', 't' => 'Application Development', 'd' => 'Web, mobile & API yang aman, cepat, dan mudah dikembangkan.', 'href' => 'layanan/app-development/'],
  ['icon' => '✦', 'cls' => ' teal', 't' => 'AI Implementation', 'd' => 'Chatbot, OCR, computer vision & prediksi yang terukur ROI-nya.', 'href' => 'layanan/ai-solution/'],
  ['icon' => '⬡', 'cls' => '', 't' => 'IoT Solutions', 'd' => 'Sensor, gateway & dashboard real-time untuk pabrik dan logistik.', 'href' => 'layanan/iot/'],
  ['icon' => '▣', 'cls' => ' teal', 't' => 'ERP Odoo', 'd' => 'Implementasi, migrasi & kustom modul Odoo sesuai proses bisnis.', 'href' => 'layanan/odoo-erp/'],
  ['icon' => '⬢', 'cls' => '', 't' => 'Fleet Management', 'd' => 'GPS tracking, BBM, maintenance & driver scoring dalam satu dasbor.', 'href' => 'layanan/fleet-management/'],
];
$cases = [
  ['c' => 'ERP Odoo untuk Manufaktur', 't' => 'Go-live 6 modul dalam 14 minggu', 'd' => 'Stok akurat, closing 3x lebih cepat, dan audit trail penuh.'],
  ['c' => 'AI Vision untuk Logistik', 't' => 'QC otomatis dengan akurasi 97,4%', 'd' => 'Inspeksi kemasan dari 4 menit menjadi 20 detik per pallet.'],
  ['c' => 'Fleet untuk Distribusi', 't' => '1.240 unit terpantau real-time', 'd' => 'BBM turun 11% dan keterlambatan turun 38% dalam 6 bulan.'],
];
?>
<section class="hero">
  <div class="container hero-grid">
    <div>
      <span class="eyebrow">Konsultan IT · Jakarta, Indonesia</span>
      <h1>Aplikasi, AI dan IoT <span>siap produksi</span> untuk bisnis Anda</h1>
      <p class="lead" style="margin-top:16px">Kami membantu perusahaan merancang, membangun, dan mengoperasikan solusi digital: aplikasi, AI, IoT, ERP Odoo, dan fleet management.</p>
      <div class="hero-cta">
        <a class="btn btn-primary" href="<?php echo esc_url(home_url('/kontak/')); ?>">Konsultasi Gratis</a>
        <a class="btn btn-ghost" href="<?php echo esc_url(home_url('/layanan/')); ?>">Lihat Layanan</a>
      </div>
    </div>
    <div class="hero-visual">
      <img class="photo" src="<?php echo esc_url($hp['src']); ?>" alt="<?php echo esc_attr($hp['alt']); ?>" width="<?php echo (int)$hp['w']; ?>" height="<?php echo (int)$hp['h']; ?>" fetchpriority="high">
      <div class="hero-photo-card">
        <b>Status implementasi</b>
        <div class="small muted">Odoo ERP, go-live 92%</div>
        <div class="small muted">AI Vision, akurasi 97,4%</div>
        <div class="small muted">Fleet GPS, 1.240 unit aktif</div>
      </div>
      <div class="dash-card">
        <div class="kpi-row">
          <div class="kpi"><b><span data-count="<?php echo (int)($kpis[0] ?? 18); ?>">0</span>+</b><span>Proyek berjalan</span></div>
          <div class="kpi"><b><span data-count="<?php echo (int)($kpis[1] ?? 9); ?>">0</span></b><span>Tahun pengalaman</span></div>
          <div class="kpi"><b><span data-count="<?php echo (int)($kpis[2] ?? 40); ?>">0</span>+</b><span>Klien aktif</span></div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="section section-soft">
  <div class="container">
    <div class="stat-band reveal">
      <div class="stat"><b><span data-count="<?php echo (int)($stats[0] ?? 120); ?>">0</span>+</b><span>Proyek diselesaikan</span></div>
      <div class="stat"><b><span data-count="<?php echo (int)($stats[1] ?? 40); ?>">0</span>+</b><span>Klien aktif</span></div>
      <div class="stat"><b><span data-count="<?php echo (int)($stats[2] ?? 9); ?>">0</span></b><span>Tahun pengalaman</span></div>
      <div class="stat"><b><span data-count="<?php echo (int)($stats[3] ?? 60); ?>">0</span>+</b><span>Modul Odoo live</span></div>
    </div>
    <p class="small muted reveal" style="margin-top:26px;font-weight:700;letter-spacing:.06em;text-transform:uppercase">Dipercaya berbagai industri</p>
    <div class="logo-strip reveal"><?php foreach ($clients as $c): ?><span><?php echo esc_html(is_array($c) ? ($c['id'] ?? '') : $c); ?></span><?php endforeach; ?></div>
  </div>
</section>

<section class="section" id="layanan">
  <div class="container">
    <span class="eyebrow reveal">Layanan utama</span>
    <h2 class="reveal">Satu partner untuk seluruh kebutuhan digital</h2>
    <p class="lead reveal" style="margin-top:12px">Dari aplikasi hingga operasional armada, semuanya dirancang agar saling terhubung dan mudah dirawat.</p>
    <div class="grid services-grid" style="margin-top:30px">
      <?php foreach ($svcCards as $sc): ?>
      <article class="card reveal">
        <div class="card-icon<?php echo esc_attr($sc['cls']); ?>"><?php echo esc_html($sc['icon']); ?></div>
        <h3><?php echo esc_html($sc['t']); ?></h3>
        <p><?php echo esc_html($sc['d']); ?></p>
        <p style="margin-top:14px"><a class="link-more" href="<?php echo esc_url(home_url($sc['href'])); ?>">→ <span>Pelajari detail</span></a></p>
      </article>
      <?php endforeach; ?>
      <article class="card reveal" style="background:var(--bg-soft)">
        <h3>Butuh kombinasi?</h3>
        <p>Contoh: Odoo + GPS armada + AI prediksi maintenance. Kami rancang arsitekturnya.</p>
        <p style="margin-top:14px"><a class="btn btn-primary btn-sm" href="<?php echo esc_url(home_url('/kontak/')); ?>">Lihat semua layanan</a></p>
      </article>
    </div>
  </div>
</section>

<section class="section section-soft">
  <div class="container split">
    <div>
      <h2 class="reveal">Konsultan yang ikut bertanggung jawab pada hasil</h2>
      <div class="grid grid-2" style="margin-top:26px">
        <div class="reveal"><h3>Senior, in-house</h3><p class="muted small">Tidak dilempar ke freelancer. Arsitek dan PM mendampingi dari discovery sampai go-live.</p></div>
        <div class="reveal"><h3>Terukur sejak awal</h3><p class="muted small">Setiap proposal mencantumkan KPI: waktu, biaya, dan dampak bisnis yang bisa diverifikasi.</p></div>
        <div class="reveal"><h3>Dokumentasi &amp; serah terima</h3><p class="muted small">Source code, kredensial, dan SOP diserahkan penuh. Tidak ada vendor lock-in tersembunyi.</p></div>
        <div class="reveal"><h3>Support purna jual</h3><p class="muted small">SLA respons jelas dan opsi retainer bulanan untuk pengembangan lanjutan.</p></div>
      </div>
    </div>
    <div class="panel-img reveal">
      <div class="mini-metric"><span class="small muted">Closing keuangan</span><b>3× lebih cepat</b></div>
      <div class="mini-metric"><span class="small muted">Akurasi QC vision</span><b>97,4%</b></div>
      <div class="mini-metric"><span class="small muted">BBM armada</span><b>−11% dalam 6 bulan</b></div>
      <p class="small muted">Angka placeholder dari proyek tipikal. Diganti data asli saat tersedia.</p>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <h2 class="reveal">Alur yang transparan, tanpa kejutan</h2>
    <div class="grid steps" style="margin-top:28px">
      <div class="card step reveal"><h3>Discovery</h3><p>Workshop 1-2 minggu: proses bisnis, data, dan target sukses.</p></div>
      <div class="card step reveal"><h3>Blueprint</h3><p>Arsitektur, estimasi terbuka, dan prototipe yang bisa diklik.</p></div>
      <div class="card step reveal"><h3>Build &amp; QA</h3><p>Sprint mingguan, demo rutin, testing berlapis.</p></div>
      <div class="card step reveal"><h3>Go-live &amp; Support</h3><p>Migrasi data, pelatihan, dan pendampingan pasca-rilis.</p></div>
    </div>
  </div>
</section>

<section class="section section-soft">
  <div class="container">
    <span class="eyebrow reveal">Studi kasus</span>
    <h2 class="reveal">Hasil yang bisa diverifikasi</h2>
    <div class="grid cases" style="margin-top:28px">
      <?php foreach ($cases as $idx => $cs): $im = $caseImgs[$idx] ?? []; ?>
      <article class="card case-card reveal">
        <div class="case-top"><img src="<?php echo esc_url(is_array($im) ? ($im['img'] ?? '') : ''); ?>" alt="<?php echo esc_attr(is_array($im) && isset($im['alt']) ? sgi_id($im['alt']) : ''); ?>" width="640" height="400" loading="lazy"></div>
        <div class="case-body"><p class="case-cat"><?php echo esc_html($cs['c']); ?></p><h3><?php echo esc_html($cs['t']); ?></h3><p class="muted small"><?php echo esc_html($cs['d']); ?></p></div>
      </article>
      <?php endforeach; ?>
    </div>
    <p style="margin-top:22px"><a class="btn btn-ghost" href="<?php echo esc_url(home_url('/portofolio/')); ?>">Lihat portofolio</a></p>
  </div>
</section>

<section class="section">
  <div class="container">
    <h2 class="reveal">Kata mereka yang sudah jalan bareng</h2>
    <div class="grid grid-2" style="margin-top:26px">
      <blockquote class="quote reveal"><p>"Tim Sirius rapi soal dokumentasi. Serah terima beres, tim internal kami bisa lanjut sendiri."</p><cite>Operations Director · Perusahaan Manufaktur</cite></blockquote>
      <blockquote class="quote reveal"><p>"POC AI-nya jujur. Yang tidak feasible dibilang tidak feasible. Itu langka."</p><cite>Head of Logistics · Perusahaan Distribusi</cite></blockquote>
    </div>
    <div class="cta-band reveal" style="margin-top:40px">
      <div><h2>Punya target digital tahun ini? Diskusikan dulu, gratis.</h2><p style="margin-top:8px">Ceritakan proses bisnis Anda dalam 30 menit. Kami balas dengan rekomendasi dan estimasi kasar, tanpa komitmen.</p></div>
      <div style="display:flex;gap:12px;flex-wrap:wrap">
        <a class="btn btn-primary" href="<?php echo esc_url(home_url('/kontak/')); ?>">Konsultasi Gratis</a>
        <a class="btn btn-ghost" href="<?php echo esc_url(home_url('/kontak/')); ?>">Chat WhatsApp</a>
      </div>
    </div>
  </div>
</section>
<?php get_footer(); ?>
