<?php // footer.php - port templates/footer.php, ID-only. $s dari header scope tidak tersedia; ambil ulang. ?>
<?php $s = sgi_settings(); ?>
</main>
<?php
$footer_bg = (string) get_theme_mod("sgi_footer_bg", "");
$footer_style = ($footer_bg !== "" && $footer_bg !== "#0b2447") ? " style=\"background-color:" . esc_attr($footer_bg) . ";\"" : "";
?>
<footer class="site-footer"<?php echo $footer_style; ?>>
  <div class="container footer-grid">
    <div>
      <a class="<?php echo esc_attr(sgi_brand_class('footer')); ?>" href="<?php echo esc_url(home_url('/')); ?>" style="color:#fff" aria-label="Sirius Global Indonesia"><span class="<?php echo esc_attr(sgi_logo_mark_class('footer')); ?>" aria-hidden="true"><?php sgi_logo_mark('footer'); ?></span><span class="<?php echo esc_attr(sgi_brand_name_class('footer')); ?>" style="color:#fff">Sirius Global<span style="color:#aebddb">Indonesia · IT Consulting</span></span></a>
      <p class="small" style="margin-top:14px;color:#aebddb">Konsultan IT Indonesia untuk application development, AI, IoT, ERP Odoo, dan fleet management.</p>
    </div>
    <div><h4><?php echo esc_html(sgi_t('footer.services', 'Layanan')); ?></h4><ul><li><a href="<?php echo esc_url(home_url('/layanan/app-development/')); ?>">Application Development</a></li><li><a href="<?php echo esc_url(home_url('/layanan/ai-solution/')); ?>">AI Implementation</a></li><li><a href="<?php echo esc_url(home_url('/layanan/iot/')); ?>">IoT Solutions</a></li><li><a href="<?php echo esc_url(home_url('/layanan/odoo-erp/')); ?>">ERP Odoo</a></li><li><a href="<?php echo esc_url(home_url('/layanan/fleet-management/')); ?>">Fleet Management</a></li></ul></div>
    <div><h4><?php echo esc_html(sgi_t('footer.company', 'Perusahaan')); ?></h4><ul><li><a href="<?php echo esc_url(home_url('/kontak/')); ?>">Kontak</a></li></ul></div>
    <div><h4><?php echo esc_html(sgi_t('footer.contact', 'Kontak')); ?></h4><ul><li><a href="mailto:<?php echo esc_attr($s['email']); ?>"><?php echo esc_html($s['email']); ?></a></li><li><span><?php echo esc_html($s['phone']); ?></span></li><li><span><?php echo esc_html($s['address']); ?></span></li><li><span><?php echo esc_html($s['hours']); ?></span></li></ul></div>
  </div>
  <div class="container footer-bottom"><span>© <span data-year><?php echo esc_html(date('Y')); ?></span> <span><?php echo esc_html($s['name']); ?></span>. <span><?php echo esc_html(sgi_t('footer.rights', 'Hak cipta dilindungi.')); ?></span></span><span><a href="<?php echo esc_url(home_url('/privasi/')); ?>"><?php echo esc_html(sgi_t('footer.privacy', 'Privasi')); ?></a> · <a href="<?php echo esc_url(home_url('/syarat/')); ?>"><?php echo esc_html(sgi_t('footer.terms', 'Syarat')); ?></a></span></div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
