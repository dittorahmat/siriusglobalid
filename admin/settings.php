<?php
// admin/settings.php
$ptitle = 'Settings';
require_once __DIR__ . '/_head.php';
require_once __DIR__ . '/_form.php';
$msg = ''; $err = '';
$d = cms_load('settings');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!cms_csrf_check($_POST['csrf'] ?? null)) { $err = 'Token kedaluwarsa. Muat ulang halaman.'; }
  else {
    $d['name'] = trim((string)($_POST['name'] ?? ''));
    $d['short'] = trim((string)($_POST['short'] ?? ''));
    $d['tagline'] = trim((string)($_POST['tagline'] ?? ''));
    $d['email'] = trim((string)($_POST['email'] ?? ''));
    $d['phone'] = trim((string)($_POST['phone'] ?? ''));
    $d['phoneHref'] = trim((string)($_POST['phoneHref'] ?? ''));
    $d['address'] = trim((string)($_POST['address'] ?? ''));
    $d['hours'] = trim((string)($_POST['hours'] ?? ''));
    $d['mapEmbed'] = trim((string)($_POST['mapEmbed'] ?? ''));
    if (cms_save('settings', $d, $_POST['rev'] ?? null)) $msg = 'Tersimpan.';
    else $err = cms_last_error();
    $d = cms_load('settings');
  }
}
?>
<?php if ($msg !== ''): ?><p class="msg-ok"><?php echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8'); ?></p><?php endif; ?>
<?php if ($err !== ''): ?><p class="msg-err"><?php echo htmlspecialchars($err, ENT_QUOTES, 'UTF-8'); ?></p><?php endif; ?>
<form method="post"><input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8'); ?>">
<input type="hidden" name="rev" value="<?php echo htmlspecialchars((string)($d['updated_at'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
<div class="admin-card"><h2>Identitas & kontak (pengganti site-config.js)</h2>
<?php foreach (['name' => 'Nama perusahaan', 'short' => 'Nama pendek', 'tagline' => 'Tagline', 'email' => 'Email', 'phone' => 'Telepon/WA tampil', 'phoneHref' => 'Link WA (https://wa.me/...)', 'address' => 'Alamat', 'hours' => 'Jam operasional', 'mapEmbed' => 'URL embed peta'] as $k => $lb): ?>
<div class="frow"><label><?php echo $lb; ?></label><input type="text" name="<?php echo $k; ?>" value="<?php echo htmlspecialchars((string)($d[$k] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"></div>
<?php endforeach; ?>
<button class="btn btn-primary" type="submit">Simpan</button></div></form>
<?php require_once __DIR__ . '/_foot.php'; ?>
