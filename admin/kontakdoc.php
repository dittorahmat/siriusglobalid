<?php
// admin/kontakdoc.php - meta + konten privasi/syarat (+ meta kontak).
$ptitle = 'Kontak, Privasi, Syarat';
require_once __DIR__ . '/_head.php';
require_once __DIR__ . '/_form.php';
$msg = ''; $err = '';
$kd = cms_load('kontak'); $pd = cms_load('privasi'); $sd = cms_load('syarat');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!cms_csrf_check($_POST['csrf'] ?? null)) { $err = 'Token kedaluwarsa. Muat ulang halaman.'; }
  else {
    $kd['meta_desc'] = p_bi('k_meta');
    $pd['h1'] = p_bi('p_h1'); $pd['lead'] = p_bi('p_lead'); $pd['meta_desc'] = p_bi('p_meta');
    $sd['h1'] = p_bi('s_h1'); $sd['lead'] = p_bi('s_lead'); $sd['meta_desc'] = p_bi('s_meta');
    $ok = cms_save('kontak', $kd, $_POST['rev_k'] ?? null)
       && cms_save('privasi', $pd, $_POST['rev_p'] ?? null)
       && cms_save('syarat', $sd, $_POST['rev_s'] ?? null);
    if ($ok) $msg = 'Tersimpan.';
    else $err = cms_last_error();
    $kd = cms_load('kontak'); $pd = cms_load('privasi'); $sd = cms_load('syarat');
  }
}
?>
<?php if ($msg !== ''): ?><p class="msg-ok"><?php echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8'); ?></p><?php endif; ?>
<?php if ($err !== ''): ?><p class="msg-err"><?php echo htmlspecialchars($err, ENT_QUOTES, 'UTF-8'); ?></p><?php endif; ?>
<form method="post"><input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8'); ?>">
<input type="hidden" name="rev_k" value="<?php echo htmlspecialchars((string)($kd['updated_at'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
<input type="hidden" name="rev_p" value="<?php echo htmlspecialchars((string)($pd['updated_at'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
<input type="hidden" name="rev_s" value="<?php echo htmlspecialchars((string)($sd['updated_at'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
<div class="admin-card"><h2>Meta description</h2>
<?php f_bi_text('k_meta', $kd['meta_desc'] ?? '', 'Kontak'); ?>
<?php f_bi_text('p_meta', $pd['meta_desc'] ?? '', 'Privasi'); ?>
<?php f_bi_text('s_meta', $sd['meta_desc'] ?? '', 'Syarat'); ?>
</div>
<div class="admin-card"><h2>Privasi: judul + lead</h2>
<?php f_bi_text('p_h1', $pd['h1'] ?? '', 'H1'); ?>
<?php f_bi_area('p_lead', $pd['lead'] ?? '', 'Lead'); ?>
</div>
<div class="admin-card"><h2>Syarat: judul + lead</h2>
<?php f_bi_text('s_h1', $sd['h1'] ?? '', 'H1'); ?>
<?php f_bi_area('s_lead', $sd['lead'] ?? '', 'Lead'); ?>
<button class="btn btn-primary" type="submit">Simpan</button>
<p class="hint">Isi pasal privasi/syarat (4 seksi + CTA) saat ini hanya lewat file seed; form judul/lead di atas mencakup yang paling sering berubah.</p></div></form>
<?php require_once __DIR__ . '/_foot.php'; ?>
