<?php
// page-kontak.php - Template: Kontak (info dari sgi_settings + form WA deep link).
// Hero + pilihan dropdown dari option sgi_kontak (Tampilan > Konten Situs),
// fallback ke nilai bawaan bila kosong.
/* Template Name: Kontak */
get_header();
$st = sgi_settings();
$kontak = function_exists('sgi_kontak') ? sgi_kontak() : [];
$needs = $kontak['needs'] ?? ['app-development' => 'Application Development', 'ai-solution' => 'AI Implementation', 'iot' => 'IoT Solutions', 'odoo-erp' => 'ERP Odoo', 'fleet-management' => 'Fleet Management', 'infrastruktur' => 'Network dan Infrastruktur', 'dashboard-bi' => 'Dashboard dan BI', 'cybersecurity' => 'Cybersecurity', 'training' => 'Corporate Training', 'other' => 'Lainnya / kombinasi'];
$budgets = $kontak['budgets'] ?? ['lt50' => 'Di bawah Rp 50 jt', '50-150' => 'Rp 50-150 jt', '150-500' => 'Rp 150-500 jt', 'gt500' => 'Di atas Rp 500 jt', 'unknown' => 'Belum tahu, minta arahan'];
$times = $kontak['times'] ?? ['urgent' => 'Mendesak, di bawah 1 bulan', '1-3mo' => '1-3 bulan', 'explore' => 'Masih eksplorasi'];
$heroH1 = $kontak['hero_h1'] ?? 'Ceritakan kebutuhan Anda, kami balas < 1 hari kerja';
$formT = $kontak['form_t'] ?? 'Formulir konsultasi';
$formD = $kontak['form_d'] ?? 'Isi detail kebutuhan. Pesan Anda terkirim sebagai chat WhatsApp terstruktur ke tim kami.';
$pre = isset($_GET['layanan']) ? sanitize_key($_GET['layanan']) : '';
?>
<section class="page-hero"><div class="container">
<p class="breadcrumb"><a href="<?php echo esc_url(home_url('/')); ?>"><?php echo esc_html(sgi_t('contact.home', 'Beranda')); ?></a> / <span><?php echo esc_html(sgi_t('contact.breadcrumb', 'Kontak')); ?></span></p>
<span class="eyebrow">Kontak</span>
<h1><?php echo esc_html($heroH1); ?></h1>
</div></section>
<section class="section"><div class="container contact-grid">
<div class="info-card">
<h2><?php echo esc_html(sgi_t('contact.info_title', 'Info kontak')); ?></h2>
<div class="info-row"><span>✉</span><div><b><?php echo esc_html(sgi_t('contact.email', 'Email')); ?></b><br><a href="mailto:<?php echo esc_attr($st['email']); ?>"><?php echo esc_html($st['email']); ?></a></div></div>
<div class="info-row"><span>☏</span><div><b><?php echo esc_html(sgi_t('contact.phone', 'WhatsApp / Telepon')); ?></b><br><a href="<?php echo esc_attr($st['phoneHref']); ?>"><span><?php echo esc_html($st['phone']); ?></span></a></div></div>
<div class="info-row"><span>◉</span><div><b><?php echo esc_html(sgi_t('contact.address', 'Alamat')); ?></b><br><span><?php echo esc_html($st['address']); ?></span><br><span class="small muted"><?php echo esc_html($st['hours']); ?></span></div></div>
<iframe title="Peta" style="border:1px solid var(--line);border-radius:14px;width:100%;height:260px" loading="lazy" src="<?php echo esc_attr($st['mapEmbed']); ?>"></iframe>
</div>
<div class="card">
<h2><?php echo esc_html($formT); ?></h2>
<p class="small muted" style="margin-top:8px"><?php echo esc_html($formD); ?></p>
<form data-contact-form novalidate style="margin-top:18px">
<div class="grid grid-2">
<div class="field"><label for="f-name"><?php echo esc_html(sgi_t('contact.name', 'Nama lengkap')); ?></label><input id="f-name" name="name" required autocomplete="name" placeholder="<?php echo esc_attr(sgi_t('contact.name_placeholder', 'Nama Anda')); ?>" aria-describedby="e-name"><span class="error" id="e-name"><?php echo esc_html(sgi_t('contact.err_required', 'Wajib diisi.')); ?></span></div>
<div class="field"><label for="f-company"><?php echo esc_html(sgi_t('contact.company', 'Perusahaan')); ?></label><input id="f-company" name="company" autocomplete="organization" placeholder="<?php echo esc_attr(sgi_t('contact.company_placeholder', 'PT Contoh Sukses')); ?>"></div>
</div>
<div class="grid grid-2">
<div class="field"><label for="f-email"><?php echo esc_html(sgi_t('contact.work_email', 'Email kerja')); ?></label><input id="f-email" name="email" type="email" required autocomplete="email" placeholder="<?php echo esc_attr(sgi_t('contact.email_placeholder', 'nama@perusahaan.co.id')); ?>" aria-describedby="e-email"><span class="error" id="e-email"><?php echo esc_html(sgi_t('contact.err_email', 'Masukkan email kerja yang valid, contoh nama@perusahaan.co.id')); ?></span></div>
<div class="field"><label for="f-wa"><?php echo esc_html(sgi_t('contact.wa', 'Nomor WhatsApp')); ?></label><input id="f-wa" name="wa" type="tel" autocomplete="tel" inputmode="tel" placeholder="<?php echo esc_attr(sgi_t('contact.wa_placeholder', '0812xxxxxxx')); ?>"></div>
</div>
<div class="field"><label for="f-need"><?php echo esc_html(sgi_t('contact.need', 'Kebutuhan')); ?></label><select id="f-need" name="need"><?php foreach ($needs as $v => $label): ?><option value="<?php echo esc_attr($v); ?>"<?php selected($pre, $v); ?>><?php echo esc_html($label); ?></option><?php endforeach; ?></select></div>
<div class="grid grid-2">
<div class="field"><label for="f-budget"><?php echo esc_html(sgi_t('contact.budget', 'Rentang budget')); ?></label><select id="f-budget" name="budget"><?php foreach ($budgets as $v => $label): ?><option value="<?php echo esc_attr($v); ?>"><?php echo esc_html($label); ?></option><?php endforeach; ?></select></div>
<div class="field"><label for="f-time"><?php echo esc_html(sgi_t('contact.time', 'Target waktu')); ?></label><select id="f-time" name="time"><?php foreach ($times as $v => $label): ?><option value="<?php echo esc_attr($v); ?>"><?php echo esc_html($label); ?></option><?php endforeach; ?></select></div>
</div>
<div class="field"><label for="f-msg"><?php echo esc_html(sgi_t('contact.message', 'Ceritakan singkat')); ?></label><textarea id="f-msg" name="message" rows="5" required placeholder="<?php echo esc_attr(sgi_t('contact.message_placeholder', 'Contoh: kami punya 80 armada, ingin pantau BBM + integrasi ke pembukuan...')); ?>" aria-describedby="e-msg"></textarea><span class="error" id="e-msg"><?php echo esc_html(sgi_t('contact.err_required', 'Wajib diisi.')); ?></span></div>
<div class="field"><label for="f-privacy" style="display:flex;gap:10px;align-items:flex-start;font-weight:500;font-size:0.93rem"><input type="checkbox" id="f-privacy" required style="width:24px;height:24px;margin-top:2px;flex:none" aria-describedby="e-privacy"><span><?php echo esc_html(sgi_is_en() ? "I agree to the processing of this information to contact me, in accordance with the " : "Saya setuju data ini dipakai untuk menghubungi saya, sesuai "); ?><a href="<?php echo esc_url(home_url('/privasi/')); ?>"><?php echo esc_html(sgi_t('contact.privacy_link', 'kebijakan privasi')); ?></a>.</span></label><span class="error" id="e-privacy"><?php echo esc_html(sgi_t('contact.err_privacy', 'Centang persetujuan dulu agar kami boleh menghubungi Anda.')); ?></span></div>
<button class="btn btn-primary" type="submit"><?php echo esc_html(sgi_t('contact.submit', 'Kirim via WhatsApp')); ?></button>
<p class="small muted" style="margin-top:12px"><span><?php echo esc_html(sgi_t('contact.direct_reach', 'Atau hubungi langsung:')); ?></span> <a href="mailto:<?php echo esc_attr($st['email']); ?>"><?php echo esc_html($st['email']); ?></a> · <a href="<?php echo esc_attr($st['phoneHref']); ?>"><span><?php echo esc_html($st['phone']); ?></span></a></p>
<p data-form-ok hidden role="status" style="margin-top:14px;background:var(--teal-100);border:1px solid #bfe9da;padding:12px 16px;border-radius:12px"><span>Membuka WhatsApp dengan pesan Anda.</span> <a data-form-wa href="#" target="_blank" rel="noopener">Klik di sini bila tidak terbuka otomatis.</a></p>
</form>
</div>
</div></section>
<?php get_footer(); ?>
