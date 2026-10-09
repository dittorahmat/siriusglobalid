<?php
// admin/i18n.php - editor kamus teks ID/EN (nav, hero, footer, form, dll).
$ptitle = 'Teks ID/EN';
require_once __DIR__ . '/_head.php';
$msg = ''; $err = '';
$id = cms_load('i18n.id'); $en = cms_load('i18n.en');
$keysId = $id['keys'] ?? []; $keysEn = $en['keys'] ?? [];
$q = trim((string)($_GET['q'] ?? ''));
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!cms_csrf_check($_POST['csrf'] ?? null)) { $err = 'Token kedaluwarsa. Muat ulang halaman.'; }
  else {
    $names = (array)($_POST['kname'] ?? []);
    $vids = (array)($_POST['vid'] ?? []);
    $vens = (array)($_POST['ven'] ?? []);
    $newId = []; $newEn = [];
    foreach ($names as $i => $kn) {
      $kn = trim((string)$kn);
      if ($kn === '' || !preg_match('/^[a-z0-9._\-]+$/i', $kn)) continue;
      $newId[$kn] = (string)($vids[$i] ?? '');
      $newEn[$kn] = (string)($vens[$i] ?? '');
    }
    $nk = trim((string)($_POST['newkey'] ?? ''));
    if ($nk !== '' && preg_match('/^[a-z0-9._\-]+$/i', $nk)) {
      $newId[$nk] = trim((string)($_POST['newid'] ?? ''));
      $newEn[$nk] = trim((string)($_POST['newen'] ?? ''));
    }
    $id['keys'] = $newId; $en['keys'] = $newEn;
    if (cms_save('i18n.id', $id, $_POST['rev_id'] ?? null) && cms_save('i18n.en', $en, $_POST['rev_en'] ?? null)) {
      $msg = 'Tersimpan (' . count($newId) . ' kunci).';
    } else { $err = cms_last_error(); }
    $id = cms_load('i18n.id'); $en = cms_load('i18n.en');
    $keysId = $id['keys'] ?? []; $keysEn = $en['keys'] ?? [];
  }
}
$all = array_unique(array_merge(array_keys($keysId), array_keys($keysEn)));
sort($all);
if ($q !== '') $all = array_values(array_filter($all, fn($k) => stripos($k, $q) !== false));
?>
<?php if ($msg !== ''): ?><p class="msg-ok"><?php echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8'); ?></p><?php endif; ?>
<?php if ($err !== ''): ?><p class="msg-err"><?php echo htmlspecialchars($err, ENT_QUOTES, 'UTF-8'); ?></p><?php endif; ?>
<form method="get" style="margin-bottom:12px"><input type="text" name="q" value="<?php echo htmlspecialchars($q, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Cari kunci, mis. hero." style="font-size:16px;padding:10px 12px;border:1px solid var(--line);border-radius:10px;width:min(420px,100%)"> <button class="btn btn-ghost btn-sm" type="submit">Cari</button></form>
<form method="post"><input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8'); ?>">
<input type="hidden" name="rev_id" value="<?php echo htmlspecialchars((string)($id['updated_at'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
<input type="hidden" name="rev_en" value="<?php echo htmlspecialchars((string)($en['updated_at'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
<div class="admin-card"><h2>Kamus (<?php echo count($all); ?> kunci<?php echo $q !== '' ? ', filter: ' . htmlspecialchars($q, ENT_QUOTES, 'UTF-8') : ''; ?>)</h2>
<p class="hint">Hati-hati: mengganti kunci yang dipakai template merusak toggle bahasa. Kosongkan nilai EN bila belum ada terjemahan (fallback ID).</p>
<div class="table-scroll"><table class="admin-t"><tr><th>Kunci</th><th>ID</th><th>EN</th></tr>
<?php foreach ($all as $k): ?>
<tr><td><code><?php echo htmlspecialchars($k, ENT_QUOTES, 'UTF-8'); ?></code><input type="hidden" name="kname[]" value="<?php echo htmlspecialchars($k, ENT_QUOTES, 'UTF-8'); ?>"></td>
<td><textarea name="vid[]" rows="2" style="width:100%;font-size:15px"><?php echo htmlspecialchars((string)($keysId[$k] ?? ''), ENT_QUOTES, 'UTF-8'); ?></textarea></td>
<td><textarea name="ven[]" rows="2" style="width:100%;font-size:15px"><?php echo htmlspecialchars((string)($keysEn[$k] ?? ''), ENT_QUOTES, 'UTF-8'); ?></textarea></td></tr>
<?php endforeach; ?>
</table></div></div>
<div class="admin-card"><h2>Tambah kunci</h2>
<div class="frow"><label>Kunci baru (huruf, angka, titik, dash)</label><input type="text" name="newkey" placeholder="mis. home.baru"></div>
<div class="frow"><label>ID / EN</label><div class="bilingual"><textarea name="newid"></textarea><textarea name="newen"></textarea></div></div>
<button class="btn btn-primary" type="submit">Simpan semua</button></div></form>
<?php require_once __DIR__ . '/_foot.php'; ?>
