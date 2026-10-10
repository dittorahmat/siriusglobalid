<?php
// page-tentang.php - Template: Tentang (values/timeline/team dari repeater page meta).
/* Template Name: Tentang */
get_header();
$id = get_the_ID();
$values = json_decode((string)get_post_meta($id, '_sgi_values', true), true) ?: [];
$timeline = json_decode((string)get_post_meta($id, '_sgi_timeline', true), true) ?: [];
$team = json_decode((string)get_post_meta($id, '_sgi_team', true), true) ?: [];
?>
<section class="page-hero"><div class="container">
  <p class="breadcrumb"><a href="<?php echo esc_url(home_url('/')); ?>">Beranda</a> / <span>Tentang</span></p>
  <span class="eyebrow">Tentang kami</span>
  <h1>Partner teknologi yang <span>mengerti operasional</span>, bukan sekadar coding</h1>
  <p class="lead" style="margin-top:14px">Kami berdiri untuk satu hal sederhana: software yang benar-benar dipakai di lapangan dan memberi hasil bisnis.</p>
</div></section>
<section class="section"><div class="container split">
  <div class="card reveal"><h3>Misi</h3><p class="muted">Membantu perusahaan Indonesia naik kelas lewat software yang andal, terukur, dan mudah dirawat.</p></div>
  <div class="card reveal"><h3>Prinsip</h3><p class="muted">Jujur soal kelayakan, transparan soal biaya, dan disiplin soal dokumentasi.</p></div>
</div></section>
<section class="section section-soft"><div class="container">
  <h2 class="reveal">Nilai yang kami pegang</h2>
  <div class="grid grid-4" style="margin-top:24px">
    <?php foreach ($values as $v): ?>
    <div class="card reveal"><h3><?php echo esc_html($v['t'] ?? ''); ?></h3><p class="small muted"><?php echo esc_html($v['d'] ?? ''); ?></p></div>
    <?php endforeach; ?>
  </div>
  <h2 class="reveal" style="margin-top:56px">Perjalanan singkat</h2>
  <div class="grid grid-3" style="margin-top:20px">
    <?php foreach ($timeline as $t): ?>
    <div class="card reveal"><b><?php echo esc_html($t['t'] ?? ''); ?></b><p class="small muted"><?php echo esc_html($t['d'] ?? ''); ?></p></div>
    <?php endforeach; ?>
  </div>
  <h2 class="reveal" style="margin-top:56px">Tim inti</h2>
  <p class="muted reveal">Empat peran yang mendampingi proyek Anda dari discovery sampai support purna jual.</p>
  <div class="grid grid-4" style="margin-top:20px">
    <?php foreach ($team as $m): ?>
    <div class="card reveal"><h3><?php echo esc_html($m['t'] ?? ''); ?></h3><p class="small muted"><?php echo esc_html($m['d'] ?? ''); ?></p></div>
    <?php endforeach; ?>
  </div>
  <div class="cta-band reveal" style="margin-top:44px"><div><h2>Punya target digital tahun ini? Diskusikan dulu, gratis.</h2></div><a class="btn btn-primary" href="<?php echo esc_url(home_url('/kontak/')); ?>">Konsultasi Gratis</a></div>
</div></section>
<?php get_footer(); ?>
