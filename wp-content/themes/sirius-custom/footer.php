<?php // footer.php - port templates/footer.php, ID-only. $s dari header scope tidak tersedia; ambil ulang. ?>
<?php $s = sgi_settings(); ?>
</main>
<footer class="site-footer">
  <div class="container footer-grid">
    <div>
      <a class="brand" href="<?php echo esc_url(home_url('/')); ?>" style="color:#fff"><span class="brand-mark">★</span><span class="brand-name" style="color:#fff">Sirius Global<span style="color:#aebddb">Indonesia · IT Consulting</span></span></a>
      <p class="small" style="margin-top:14px;color:#aebddb">Konsultan IT Indonesia untuk application development, AI, IoT, ERP Odoo, dan fleet management.</p>
    </div>
    <div><h4>Layanan</h4><ul><li><a href="<?php echo esc_url(home_url('/layanan/app-development/')); ?>">Application Development</a></li><li><a href="<?php echo esc_url(home_url('/layanan/ai-solution/')); ?>">AI Implementation</a></li><li><a href="<?php echo esc_url(home_url('/layanan/iot/')); ?>">IoT Solutions</a></li><li><a href="<?php echo esc_url(home_url('/layanan/odoo-erp/')); ?>">ERP Odoo</a></li><li><a href="<?php echo esc_url(home_url('/layanan/fleet-management/')); ?>">Fleet Management</a></li></ul></div>
    <div><h4>Perusahaan</h4><ul><li><a href="<?php echo esc_url(home_url('/tentang/')); ?>">Tentang kami</a></li><li><a href="<?php echo esc_url(home_url('/portofolio/')); ?>">Portofolio</a></li><li><a href="<?php echo esc_url(home_url('/kontak/')); ?>">Kontak</a></li></ul></div>
    <div><h4>Kontak</h4><ul><li><a href="mailto:<?php echo esc_attr($s['email']); ?>"><?php echo esc_html($s['email']); ?></a></li><li><span><?php echo esc_html($s['phone']); ?></span></li><li><span><?php echo esc_html($s['address']); ?></span></li><li><span><?php echo esc_html($s['hours']); ?></span></li></ul></div>
  </div>
  <div class="container footer-bottom"><span>© <span data-year><?php echo esc_html(date('Y')); ?></span> <span><?php echo esc_html($s['name']); ?></span>. <span>Hak cipta dilindungi.</span></span><span><a href="<?php echo esc_url(home_url('/privasi/')); ?>">Privasi</a> · <a href="<?php echo esc_url(home_url('/syarat/')); ?>">Syarat</a></span></div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
