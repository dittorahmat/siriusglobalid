<?php
// inc/i18n.php - Helper terjemahan antarmuka bilingual (ID / EN) berbasis locale aktif.
declare(strict_types=1);

function sgi_is_en(): bool {
  $loc = get_locale();
  return str_starts_with($loc, 'en');
}

function sgi_t(string $key, ?string $fallback = null): string {
  static $dict = null;
  if ($dict === null) {
    $dict = [
      // Kontak & Form
      'contact.h1' => ['id' => 'Ceritakan kebutuhan Anda, kami balas < 1 hari kerja', 'en' => 'Tell us your needs, we respond in less than 1 business day'],
      'contact.breadcrumb' => ['id' => 'Kontak', 'en' => 'Contact'],
      'contact.home' => ['id' => 'Beranda', 'en' => 'Home'],
      'contact.info_title' => ['id' => 'Info kontak', 'en' => 'Contact info'],
      'contact.email' => ['id' => 'Email', 'en' => 'Email'],
      'contact.phone' => ['id' => 'WhatsApp / Telepon', 'en' => 'WhatsApp / Phone'],
      'contact.address' => ['id' => 'Alamat', 'en' => 'Address'],
      'contact.form_title' => ['id' => 'Formulir konsultasi', 'en' => 'Consultation form'],
      'contact.form_desc' => ['id' => 'Isi detail kebutuhan. Pesan Anda terkirim sebagai chat WhatsApp terstruktur ke tim kami.', 'en' => 'Share your business challenges and processes. Your messages also send to our Sirius WhatsApp.'],
      'contact.name' => ['id' => 'Nama lengkap', 'en' => 'Full name'],
      'contact.name_placeholder' => ['id' => 'Nama Anda', 'en' => 'Your name'],
      'contact.company' => ['id' => 'Perusahaan', 'en' => 'Company'],
      'contact.company_placeholder' => ['id' => 'PT Contoh Sukses', 'en' => 'Company Name'],
      'contact.work_email' => ['id' => 'Email kerja', 'en' => 'Work email'],
      'contact.email_placeholder' => ['id' => 'nama@perusahaan.co.id', 'en' => 'name@company.com'],
      'contact.wa' => ['id' => 'Nomor WhatsApp', 'en' => 'WhatsApp number'],
      'contact.wa_placeholder' => ['id' => '0812xxxxxxx', 'en' => '0812xxxxxxx'],
      'contact.need' => ['id' => 'Kebutuhan', 'en' => 'Needs'],
      'contact.budget' => ['id' => 'Rentang budget', 'en' => 'Budget range'],
      'contact.time' => ['id' => 'Target waktu', 'en' => 'Target timeline'],
      'contact.message' => ['id' => 'Ceritakan singkat', 'en' => 'Brief description'],
      'contact.message_placeholder' => ['id' => 'Contoh: kami punya 80 armada, ingin pantau BBM + integrasi ke pembukuan...', 'en' => 'Example: We have 80 fleets and need fuel monitoring + ERP integration...'],
      'contact.privacy_consent' => ['id' => 'Saya setuju data ini dipakai untuk menghubungi saya, sesuai kebijakan privasi.', 'en' => 'I agree to the processing of this information to contact me, in accordance with the privacy policy.'],
      'contact.privacy_link' => ['id' => 'kebijakan privasi', 'en' => 'privacy policy'],
      'contact.submit' => ['id' => 'Kirim via WhatsApp', 'en' => 'Send via WhatsApp'],
      'contact.direct_reach' => ['id' => 'Atau hubungi langsung:', 'en' => 'Or reach out directly:'],
      'contact.err_required' => ['id' => 'Wajib diisi.', 'en' => 'Required field.'],
      'contact.err_email' => ['id' => 'Masukkan email kerja yang valid, contoh nama@perusahaan.co.id', 'en' => 'Please enter a valid work email, e.g. name@company.com'],
      'contact.err_privacy' => ['id' => 'Centang persetujuan dulu agar kami boleh menghubungi Anda.', 'en' => 'Please agree to the privacy policy so we can contact you.'],
      // Footer & Umum
      'footer.services' => ['id' => 'Layanan', 'en' => 'Services'],
      'footer.company' => ['id' => 'Perusahaan', 'en' => 'Company'],
      'footer.contact' => ['id' => 'Kontak', 'en' => 'Contact'],
      'footer.rights' => ['id' => 'Hak cipta dilindungi.', 'en' => 'All rights reserved.'],
      'footer.privacy' => ['id' => 'Privasi', 'en' => 'Privacy Policy'],
      'footer.terms' => ['id' => 'Syarat', 'en' => 'Terms of Service'],
    ];
  }

  $isEn = sgi_is_en();
  $lang = $isEn ? 'en' : 'id';
  if (isset($dict[$key][$lang])) {
    return $dict[$key][$lang];
  }
  return $fallback ?? $key;
}
