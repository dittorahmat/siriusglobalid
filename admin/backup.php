<?php
// admin/backup.php - download ZIP + restore tervalidasi + daftar snapshot.
$ptitle = 'Backup';
require_once __DIR__ . '/_head.php';
$msg = ''; $err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['act'] ?? '') === 'restore') {
  if (!cms_csrf_check($_POST['csrf'] ?? null)) { $err = 'Token kedaluwarsa. Muat ulang halaman.'; }
  elseif (empty($_FILES['zip']['tmp_name']) || !is_uploaded_file($_FILES['zip']['tmp_name'])) { $err = 'Pilih file backup (.zip) dulu.'; }
  else {
    $zip = new ZipArchive();
    if ($zip->open($_FILES['zip']['tmp_name']) !== true) { $err = 'Berkas bukan ZIP valid.'; }
    else {
      $all = [];
      for ($i = 0; $i < $zip->numFiles; $i++) {
        $name = $zip->getNameIndex($i);
        if (substr($name, -5) !== '.json') continue;
        $raw = $zip->getFromIndex($i);
        $dec = json_decode((string)$raw, true);
        if (!is_array($dec)) { $err = 'Berkas rusak: ' . $name; break; }
        $all[$name] = $dec;
      }
      $zip->close();
      if ($err === '') {
        [$ok, $m] = cms_backup_validate_array($all);
        if (!$ok) { $err = $m; }
        else {
          foreach ($all as $name => $data) {
            $domain = preg_replace('/\.json$/', '', $name);
            $domain = str_starts_with($domain, 'services/') ? 'service:' . basename($domain) : $domain;
            if (!cms_save($domain, $data)) { $err = $name . ': ' . cms_last_error(); break; }
          }
          if ($err === '') $msg = 'Restore berhasil (' . count($all) . ' berkas).';
        }
      }
    }
  }
}
$snaps = glob(cms_backup_dir() . '/*.json') ?: [];
rsort($snaps);
?>
<?php if ($msg !== ''): ?><p class="msg-ok"><?php echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8'); ?></p><?php endif; ?>
<?php if ($err !== ''): ?><p class="msg-err"><?php echo htmlspecialchars($err, ENT_QUOTES, 'UTF-8'); ?></p><?php endif; ?>
<div class="admin-card"><h2>Unduh backup penuh</h2>
<p class="hint">ZIP berisi semua <code>data/*.json</code> + manifest. Simpan di tempat aman.</p>
<p><a class="btn btn-primary btn-sm" href="backup_download.php">Download ZIP</a></p></div>
<div class="admin-card"><h2>Restore dari ZIP</h2>
<p class="hint">Isi divalidasi dulu; bila ada satu berkas rusak, tidak ada yang ditimpa.</p>
<form method="post" enctype="multipart/form-data" onsubmit="return confirm('Restore menimpa SEMUA data dengan isi ZIP. Snapshot otomatis sudah dibuat tiap save. Lanjutkan?');"><input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8'); ?>">
<input type="hidden" name="act" value="restore">
<div class="frow"><input type="file" name="zip" accept=".zip" required aria-label="Pilih file backup ZIP"></div>
<button class="btn btn-ghost" type="submit">Restore</button></form></div>
<div class="admin-card"><h2>Snapshot otomatis (<?php echo count($snaps); ?>)</h2>
<p class="hint">Dibuat tiap save. 20 terbaru per domain disimpan.</p>
<table class="admin-t"><?php foreach (array_slice($snaps, 0, 30) as $s): ?><tr><td><code><?php echo htmlspecialchars(basename($s), ENT_QUOTES, 'UTF-8'); ?></code></td><td><?php echo (int)(filesize($s) / 1024); ?> KB</td></tr><?php endforeach; ?></table></div>
<?php require_once __DIR__ . '/_foot.php'; ?>
