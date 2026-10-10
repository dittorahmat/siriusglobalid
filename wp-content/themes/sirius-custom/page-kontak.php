<?php
// page-kontak.php - Template: Kontak (info dari sgi_settings + form WA deep link).
/* Template Name: Kontak */
get_header();
$st = sgi_settings();
$needs = ['app-development' => 'Application Development', 'ai-solution' => 'AI Implementation', 'iot' => 'IoT Solutions', 'odoo-erp' => 'ERP Odoo', 'fleet-management' => 'Fleet Management', 'infrastruktur' => 'Network dan Infrastruktur', 'dashboard-bi' => 'Dashboard dan BI', 'cybersecurity' => 'Cybersecurity', 'training' => 'Corporate Training', 'other' => 'Lainnya / kombinasi'];
$budgets = ['lt50' => 'Di bawah Rp 50 jt', '50-150' => 'Rp 50-150 jt', '150-500' => 'Rp 150-500 jt', 'gt500' => 'Di atas Rp 500 jt', 'unknown' => 'Belum tahu, minta arahan'];
$times = ['urgent' => 'Mendesak, di bawah 1 bulan', '1-3mo' => '1-3 bulan', 'explore' => 'Masih eksplorasi'];
$pre = isset($_GET['layanan']) ? sanitize_key($_GET['layanan']) : '';
?>
<section class="page-hero"><div class="container">
<p class="breadcrumb"><a href="<?php echo esc_url(home_url('/')); ?>">Beranda</a> / <span>Kontak</span></p>
<span class="eyebrow">Kontak</span>
<h1>Ceritakan kebutuhan Anda, <span>kami balas &lt; 1 hari kerja</span></h1>
</div></section>
<section class="section"><div class="container contact-grid">
<div class="info-card">
<h2>Info kontak</h2>
<div class="info-row"><span>✉</span><div><b>Email</b><br><a href="mailto:<?php echo esc_attr($st['email']); ?>"><?php echo esc_html($st['email']); ?></a></div></div>
<div class="info-row"><span>☏</span><div><b>WhatsApp / Telepon</b><br><a href="<?php echo esc_attr($st['phoneHref']); ?>"><span><?php echo esc_html($st['phone']); ?></span></a></div></div>
<div class="info-row"><span>◉</span><div><b>Alamat</b><br><span><?php echo esc_html($st['address']); ?></span><br><span class="small muted"><?php echo esc_html($st['hours']); ?></span></div></div>
<iframe title="Peta" style="border:1px solid var(--line);border-radius:14px;width:100%;height:260px" loading="lazy" src="<?php echo esc_attr($st['mapEmbed']); ?>"></iframe>
</div>
<div class="card">
<h2>Formulir konsultasi</h2>
<p class="small muted" style="margin-top:8px">Isi detail kebutuhan. Pesan Anda terkirim sebagai chat WhatsApp terstruktur ke tim kami.</p>
<form data-contact-form novalidate style="margin-top:18px">
<div class="grid grid-2">
<div class="field"><label for="f-name">Nama lengkap</label><input id="f-name" name="name" required autocomplete="name" placeholder="Nama Anda" aria-describedby="e-name"><span class="error" id="e-name">Wajib diisi.</span></div>
<div class="field"><label for="f-company">Perusahaan</label><input id="f-company" name="company" autocomplete="organization" placeholder="PT Contoh Sukses"></div>
</div>
<div class="grid grid-2">
<div class="field"><label for="f-email">Email kerja</label><input id="f-email" name="email" type="email" required autocomplete="email" placeholder="nama@perusahaan.co.id" aria-describedby="e-email"><span class="error" id="e-email">Masukkan email kerja yang valid, contoh nama@perusahaan.co.id</span></div>
<div class="field"><label for="f-wa">Nomor WhatsApp</label><input id="f-wa" name="wa" type="tel" autocomplete="tel" inputmode="tel" placeholder="0812xxxxxxx"></div>
</div>
<div class="field"><label for="f-need">Kebutuhan</label><select id="f-need" name="need"><?php foreach ($needs as $v => $label): ?><option value="<?php echo esc_attr($v); ?>"<?php selected($pre, $v); ?>><?php echo esc_html($label); ?></option><?php endforeach; ?></select></div>
<div class="grid grid-2">
<div class="field"><label for="f-budget">Rentang budget</label><select id="f-budget" name="budget"><?php foreach ($budgets as $v => $label): ?><option value="<?php echo esc_attr($v); ?>"><?php echo esc_html($label); ?></option><?php endforeach; ?></select></div>
<div class="field"><label for="f-time">Target waktu</label><select id="f-time" name="time"><?php foreach ($times as $v => $label): ?><option value="<?php echo esc_attr($v); ?>"><?php echo esc_html($label); ?></option><?php endforeach; ?></select></div>
</div>
<div class="field"><label for="f-msg">Ceritakan singkat</label><textarea id="f-msg" name="message" rows="5" required placeholder="Contoh: kami punya 80 armada, ingin pantau BBM + integrasi ke pembukuan..." aria-describedby="e-msg"></textarea><span class="error" id="e-msg">Wajib diisi.</span></div>
<div class="field"><label for="f-privacy" style="display:flex;gap:10px;align-items:flex-start;font-weight:500;font-size:0.93rem"><input type="checkbox" id="f-privacy" required style="width:24px;height:24px;margin-top:2px;flex:none" aria-describedby="e-privacy"><span>Saya setuju data ini dipakai untuk menghubungi saya, sesuai <a href="<?php echo esc_url(home_url('/privasi/')); ?>">kebijakan privasi</a>.</span></label><span class="error" id="e-privacy">Centang persetujuan dulu agar kami boleh menghubungi Anda.</span></div>
<button class="btn btn-primary" type="submit">Kirim via WhatsApp</button>
<p class="small muted" style="margin-top:12px"><span>Atau hubungi langsung:</span> <a href="mailto:<?php echo esc_attr($st['email']); ?>"><?php echo esc_html($st['email']); ?></a> · <a href="<?php echo esc_attr($st['phoneHref']); ?>"><span><?php echo esc_html($st['phone']); ?></span></a></p>
<p data-form-ok hidden role="status" style="margin-top:14px;background:var(--teal-100);border:1px solid #bfe9da;padding:12px 16px;border-radius:12px"><span>Membuka WhatsApp dengan pesan Anda.</span> <a data-form-wa href="#" target="_blank" rel="noopener">Klik di sini bila tidak terbuka otomatis.</a></p>
</form>
</div>
</div></section>
<?php get_footer(); ?>
