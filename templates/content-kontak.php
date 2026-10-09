<?php
// templates/content-kontak.php - Formulir tetap WA deep link (tanpa backend).
// Variabel: $lang, $base, $settings.
$L = $lang;
$st = $settings;
$needs = [['app-development', 'q.n1'], ['ai-solution', 'q.n2'], ['iot', 'q.n3'], ['odoo-erp', 'q.n4'], ['fleet', 'q.n5'], ['infrastruktur', 'q.n6'], ['dashboard-bi', 'q.n7'], ['cybersecurity', 'q.n8'], ['training', 'q.n9'], ['other', 'q.n10']];
$budgets = [['lt50', 'q.b1'], ['50-150', 'q.b2'], ['150-500', 'q.b3'], ['gt500', 'q.b4'], ['unknown', 'q.b5']];
$times = [['urgent', 'q.t1'], ['1-3mo', 'q.t2'], ['explore', 'q.t3']];
?>
<section class="page-hero"><div class="container">
<p class="breadcrumb"><a href="<?php echo $base; ?>index.php" data-i18n="common.home"><?php echo cms_t('common.home', $L); ?></a> / <span data-i18n="nav.contact"><?php echo cms_t('nav.contact', $L); ?></span></p>
<span class="eyebrow" data-i18n="contact.hero_eye"><?php echo cms_t('contact.hero_eye', $L); ?></span>
<h1 data-i18n-html="contact.hero_title_html"><?php echo cms_html(cms_t('contact.hero_title_html', $L)); ?></h1>
</div></section>
<section class="section"><div class="container contact-grid">
<div class="info-card">
<h2 data-i18n="contact.info_t"><?php echo cms_t('contact.info_t', $L); ?></h2>
<div class="info-row"><span>✉</span><div><b>Email</b><br><a href="mailto:<?php echo cms_a($st['email'] ?? ''); ?>"><?php echo cms_e($st['email'] ?? ''); ?></a></div></div>
<div class="info-row"><span>☏</span><div><b>WhatsApp / Telepon</b><br><a href="<?php echo cms_a($st['phoneHref'] ?? ''); ?>"><span><?php echo cms_e($st['phone'] ?? ''); ?></span></a></div></div>
<div class="info-row"><span>◉</span><div><b>Alamat</b><br><span><?php echo cms_e($st['address'] ?? ''); ?></span><br><span class="small muted"><?php echo cms_e($st['hours'] ?? ''); ?></span></div></div>
<iframe title="Peta" style="border:1px solid var(--line);border-radius:14px;width:100%;height:260px" loading="lazy" src="<?php echo cms_a($st['mapEmbed'] ?? ''); ?>"></iframe>
</div>
<div class="card">
<h2 data-i18n="contact.form_t"><?php echo cms_t('contact.form_t', $L); ?></h2>
<p class="small muted" style="margin-top:8px" data-i18n="contact.form_d"><?php echo cms_t('contact.form_d', $L); ?></p>
<form data-contact-form novalidate style="margin-top:18px">
<div class="grid grid-2">
<div class="field"><label for="f-name" data-i18n="contact.name"><?php echo cms_t('contact.name', $L); ?></label><input id="f-name" name="name" required autocomplete="name" placeholder="Nama Anda" aria-describedby="e-name"><span class="error" id="e-name" data-i18n="contact.req"><?php echo cms_t('contact.req', $L); ?></span></div>
<div class="field"><label for="f-company" data-i18n="contact.company"><?php echo cms_t('contact.company', $L); ?></label><input id="f-company" name="company" autocomplete="organization" placeholder="PT Contoh Sukses"></div>
</div>
<div class="grid grid-2">
<div class="field"><label for="f-email" data-i18n="contact.email"><?php echo cms_t('contact.email', $L); ?></label><input id="f-email" name="email" type="email" required autocomplete="email" placeholder="nama@perusahaan.co.id" aria-describedby="e-email"><span class="error" id="e-email" data-i18n="contact.email_err"><?php echo cms_t('contact.email_err', $L); ?></span></div>
<div class="field"><label for="f-wa" data-i18n="contact.wa"><?php echo cms_t('contact.wa', $L); ?></label><input id="f-wa" name="wa" type="tel" autocomplete="tel" inputmode="tel" placeholder="0812xxxxxxx"></div>
</div>
<div class="field"><label for="f-need" data-i18n="contact.need"><?php echo cms_t('contact.need', $L); ?></label><select id="f-need" name="need"><?php foreach ($needs as [$v, $k]): ?><option value="<?php echo $v; ?>" data-i18n="<?php echo $k; ?>"><?php echo cms_t($k, $L); ?></option><?php endforeach; ?></select></div>
<div class="grid grid-2">
<div class="field"><label for="f-budget" data-i18n="contact.budget"><?php echo cms_t('contact.budget', $L); ?></label><select id="f-budget" name="budget"><?php foreach ($budgets as [$v, $k]): ?><option value="<?php echo $v; ?>" data-i18n="<?php echo $k; ?>"><?php echo cms_t($k, $L); ?></option><?php endforeach; ?></select></div>
<div class="field"><label for="f-time" data-i18n="contact.timeline"><?php echo cms_t('contact.timeline', $L); ?></label><select id="f-time" name="time"><?php foreach ($times as [$v, $k]): ?><option value="<?php echo $v; ?>" data-i18n="<?php echo $k; ?>"><?php echo cms_t($k, $L); ?></option><?php endforeach; ?></select></div>
</div>
<div class="field"><label for="f-msg" data-i18n="contact.msg"><?php echo cms_t('contact.msg', $L); ?></label><textarea id="f-msg" name="message" rows="5" required placeholder="Contoh: kami punya 80 armada, ingin pantau BBM + integrasi ke pembukuan..." aria-describedby="e-msg"></textarea><span class="error" id="e-msg" data-i18n="contact.req"><?php echo cms_t('contact.req', $L); ?></span></div>
<div class="field"><label for="f-privacy" style="display:flex;gap:10px;align-items:flex-start;font-weight:500;font-size:0.93rem"><input type="checkbox" id="f-privacy" required style="width:24px;height:24px;margin-top:2px;flex:none" aria-describedby="e-privacy"><span data-i18n-html="contact.privacy_html"><?php echo cms_html(cms_t('contact.privacy_html', $L)); ?></span></label><span class="error" id="e-privacy" data-i18n="contact.privacy_err"><?php echo cms_t('contact.privacy_err', $L); ?></span></div>
<button class="btn btn-primary" type="submit" data-i18n="contact.send_wa"><?php echo cms_t('contact.send_wa', $L); ?></button>
<p class="small muted" style="margin-top:12px"><span data-i18n="contact.alt"><?php echo cms_t('contact.alt', $L); ?></span> <a href="mailto:<?php echo cms_a($st['email'] ?? ''); ?>"><?php echo cms_e($st['email'] ?? ''); ?></a> · <a href="<?php echo cms_a($st['phoneHref'] ?? ''); ?>"><span><?php echo cms_e($st['phone'] ?? ''); ?></span></a></p>
<p data-form-ok hidden role="status" style="margin-top:14px;background:var(--teal-100);border:1px solid #bfe9da;padding:12px 16px;border-radius:12px"><span data-i18n="contact.ok_wa"><?php echo cms_t('contact.ok_wa', $L); ?></span> <a data-form-wa href="#" target="_blank" rel="noopener" data-i18n="contact.ok_link"><?php echo cms_t('contact.ok_link', $L); ?></a></p>
</form>
</div>
</div></section>
