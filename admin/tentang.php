<?php
// admin/tentang.php - nilai, timeline, tim.
$ptitle = 'Tentang';
require_once __DIR__ . '/_head.php';
require_once __DIR__ . '/_form.php';
$msg = ''; $err = '';
$d = cms_load('tentang');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!cms_csrf_check($_POST['csrf'] ?? null)) { $err = 'Token kedaluwarsa. Muat ulang halaman.'; }
  else {
    $t = p_bi_list('vals_t'); $dd = p_bi_list('vals_d');
    $vals = [];
    foreach ($t as $i => $x) { $vals[] = ['t' => $x, 'd' => $dd[$i] ?? ['id' => '', 'en' => '']]; }
    $t2 = p_bi_list('tl_t'); $d2 = p_bi_list('tl_d');
    $tl = [];
    foreach ($t2 as $i => $x) { $tl[] = ['t' => $x, 'd' => $d2[$i] ?? ['id' => '', 'en' => '']]; }
    $t3 = p_bi_list('team_t'); $d3 = p_bi_list('team_d');
    $team = [];
    foreach ($t3 as $i => $x) { $team[] = ['t' => $x, 'd' => $d3[$i] ?? ['id' => '', 'en' => '']]; }
    $d['values'] = $vals; $d['timeline'] = $tl; $d['team'] = $team;
    if (cms_save('tentang', $d, $_POST['rev'] ?? null)) $msg = 'Tersimpan.';
    else $err = cms_last_error();
    $d = cms_load('tentang');
  }
}
if (!function_exists('col')) {
function col(array $items, string $k, string $lg): string {
  $o = [];
  foreach ($items as $it) { $o[] = is_array($it[$k] ?? null) ? (string)($it[$k][$lg] ?? '') : ''; }
  return implode("\n", $o);
}
}
?>
<?php if ($msg !== ''): ?><p class="msg-ok"><?php echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8'); ?></p><?php endif; ?>
<?php if ($err !== ''): ?><p class="msg-err"><?php echo htmlspecialchars($err, ENT_QUOTES, 'UTF-8'); ?></p><?php endif; ?>
<form method="post" onsubmit="return confirmDel(this)"><input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8'); ?>">
<input type="hidden" name="rev" value="<?php echo htmlspecialchars((string)($d['updated_at'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
<p class="hint">Setiap daftar satu item per baris: tambah baris baru untuk item baru, hapus baris untuk menghapus item. Baris kosong dilewati.</p>
<div class="admin-card"><h2>Nilai yang dipegang</h2>
<div class="frow"><label>Judul (ID / EN, sejajar)</label><div class="bilingual"><textarea name="vals_t_id"><?php echo htmlspecialchars(col($d['values'] ?? [], 't', 'id'), ENT_QUOTES, 'UTF-8'); ?></textarea><textarea name="vals_t_en"><?php echo htmlspecialchars(col($d['values'] ?? [], 't', 'en'), ENT_QUOTES, 'UTF-8'); ?></textarea></div></div>
<div class="frow"><label>Deskripsi (ID / EN, sejajar)</label><div class="bilingual"><textarea name="vals_d_id"><?php echo htmlspecialchars(col($d['values'] ?? [], 'd', 'id'), ENT_QUOTES, 'UTF-8'); ?></textarea><textarea name="vals_d_en"><?php echo htmlspecialchars(col($d['values'] ?? [], 'd', 'en'), ENT_QUOTES, 'UTF-8'); ?></textarea></div></div></div>
<div class="admin-card"><h2>Perjalanan singkat</h2>
<div class="frow"><label>Periode (ID / EN)</label><div class="bilingual"><textarea name="tl_t_id"><?php echo htmlspecialchars(col($d['timeline'] ?? [], 't', 'id'), ENT_QUOTES, 'UTF-8'); ?></textarea><textarea name="tl_t_en"><?php echo htmlspecialchars(col($d['timeline'] ?? [], 't', 'en'), ENT_QUOTES, 'UTF-8'); ?></textarea></div></div>
<div class="frow"><label>Deskripsi (ID / EN)</label><div class="bilingual"><textarea name="tl_d_id"><?php echo htmlspecialchars(col($d['timeline'] ?? [], 'd', 'id'), ENT_QUOTES, 'UTF-8'); ?></textarea><textarea name="tl_d_en"><?php echo htmlspecialchars(col($d['timeline'] ?? [], 'd', 'en'), ENT_QUOTES, 'UTF-8'); ?></textarea></div></div></div>
<div class="admin-card"><h2>Tim inti</h2>
<div class="frow"><label>Nama peran (ID / EN)</label><div class="bilingual"><textarea name="team_t_id"><?php echo htmlspecialchars(col($d['team'] ?? [], 't', 'id'), ENT_QUOTES, 'UTF-8'); ?></textarea><textarea name="team_t_en"><?php echo htmlspecialchars(col($d['team'] ?? [], 't', 'en'), ENT_QUOTES, 'UTF-8'); ?></textarea></div></div>
<div class="frow"><label>Deskripsi (ID / EN)</label><div class="bilingual"><textarea name="team_d_id"><?php echo htmlspecialchars(col($d['team'] ?? [], 'd', 'id'), ENT_QUOTES, 'UTF-8'); ?></textarea><textarea name="team_d_en"><?php echo htmlspecialchars(col($d['team'] ?? [], 'd', 'en'), ENT_QUOTES, 'UTF-8'); ?></textarea></div></div>
<button class="btn btn-primary" type="submit">Simpan</button></div></form>
<?php require_once __DIR__ . '/_foot.php'; ?>
