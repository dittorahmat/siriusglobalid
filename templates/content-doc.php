<?php
// templates/content-doc.php - privasi/syarat. Variabel: $page, $lang, $base, $crumb (teks breadcrumb).
$L = $lang;
$cta = $page['cta'] ?? [];
?>
<section class="page-hero"><div class="container">
<p class="breadcrumb"><a href="<?php echo $base; ?>index.php"><?php echo $L === 'en' ? 'Home' : 'Beranda'; ?></a> / <?php echo cms_e($page['h1'] ?? '', $L); ?></p>
<h1><?php echo cms_e($page['h1'] ?? '', $L); ?></h1>
<p class="lead" style="margin-top:12px"><?php echo cms_e($page['lead'] ?? '', $L); ?></p>
</div></section>
<section class="section"><div class="container" style="max-width:760px;display:grid;gap:22px">
<?php foreach (($page['sections'] ?? []) as $sec): ?>
<div><h2 style="font-size:1.3rem"><?php echo cms_e($sec['h'] ?? '', $L); ?></h2><p class="muted" style="margin-top:8px"><?php echo cms_html($sec['p'] ?? '', $L); ?></p></div>
<?php endforeach; ?>
<?php if (!empty($cta)): ?><div class="cta-band"><div><h2><?php echo cms_e($cta['t'] ?? '', $L); ?></h2><p><?php echo cms_e($cta['d'] ?? '', $L); ?></p></div><a class="btn btn-primary" href="<?php echo $base . htmlspecialchars($cta['href'] ?? 'kontak.php', ENT_QUOTES, 'UTF-8'); ?>"><?php echo cms_e($cta['btn'] ?? '', $L); ?></a></div><?php endif; ?>
</div></section>
