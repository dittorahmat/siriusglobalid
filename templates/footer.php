<?php
// templates/footer.php - dipakai semua halaman publik. Variabel: $lang, $settings, $full (footer-grid penuh).
$settings = $settings ?? cms_settings();
$base = $base ?? './';
$full = $full ?? true;
?>
<footer class="site-footer">
  <?php if ($full): ?>
  <div class="container footer-grid">
    <div>
      <a class="brand" href="<?php echo $base; ?>index.php" style="color:#fff"><span class="brand-mark">★</span><span class="brand-name" style="color:#fff">Sirius Global<span style="color:#aebddb">Indonesia · IT Consulting</span></span></a>
      <p class="small" style="margin-top:14px;color:#aebddb" data-i18n="foot.desc"><?php echo cms_t('foot.desc', $lang); ?></p>
    </div>
    <div><h4 data-i18n="foot.svc"><?php echo cms_t('foot.svc', $lang); ?></h4><ul><li><a href="<?php echo $base; ?>layanan/app-development.php">Application Development</a></li><li><a href="<?php echo $base; ?>layanan/ai-solution.php">AI Implementation</a></li><li><a href="<?php echo $base; ?>layanan/iot.php">IoT Solutions</a></li><li><a href="<?php echo $base; ?>layanan/odoo-erp.php">ERP Odoo</a></li><li><a href="<?php echo $base; ?>layanan/fleet-management.php">Fleet Management</a></li></ul></div>
    <div><h4 data-i18n="foot.co"><?php echo cms_t('foot.co', $lang); ?></h4><ul><li><a href="<?php echo $base; ?>tentang.php" data-i18n="foot.about"><?php echo cms_t('foot.about', $lang); ?></a></li><li><a href="<?php echo $base; ?>portofolio.php" data-i18n="foot.work"><?php echo cms_t('foot.work', $lang); ?></a></li><li><a href="<?php echo $base; ?>kontak.php" data-i18n="nav.contact"><?php echo cms_t('nav.contact', $lang); ?></a></li></ul></div>
    <div><h4 data-i18n="foot.contact"><?php echo cms_t('foot.contact', $lang); ?></h4><ul><li><a href="mailto:<?php echo cms_a($settings['email'] ?? ''); ?>"><?php echo cms_e($settings['email'] ?? ''); ?></a></li><li><span><?php echo cms_e($settings['phone'] ?? ''); ?></span></li><li><span><?php echo cms_e($settings['address'] ?? ''); ?></span></li><li><span><?php echo cms_e($settings['hours'] ?? ''); ?></span></li></ul></div>
  </div>
  <?php endif; ?>
  <div class="container footer-bottom"><span>© <span data-year><?php echo date('Y'); ?></span> <span><?php echo cms_e($settings['name'] ?? 'PT Sirius Global Indonesia'); ?></span>. <span data-i18n="foot.rights"><?php echo cms_t('foot.rights', $lang); ?></span></span><span><a href="<?php echo $base; ?>privasi.php" data-i18n="foot.privacy"><?php echo cms_t('foot.privacy', $lang); ?></a> · <a href="<?php echo $base; ?>syarat.php" data-i18n="foot.terms"><?php echo cms_t('foot.terms', $lang); ?></a></span></div>
</footer>
