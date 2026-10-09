<?php
// admin/home.php - struktur beranda (teks hero dkk lewat menu Teks ID/EN).
$ptitle = 'Beranda';
require_once __DIR__ . '/_head.php';
require_once __DIR__ . '/_form.php';
$msg = ''; $err = '';
$d = cms_load('home');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!cms_csrf_check($_POST['csrf'] ?? null)) { $err = 'Token kedaluwarsa. Muat ulang halaman.'; }
  else {
    $d['hero_photo'] = [
      'src' => trim((string)($_POST['photo'] ?? '')),
      'alt' => p_bi('photo_alt'),
      'w' => (string)max(1, (int)($_POST['photo_w'] ?? 880)),
      'h' => (string)max(1, (int)($_POST['photo_h'] ?? 1000)),
    ];
    $d['kpi_counts'] = array_values(array_filter(array_map('trim', explode(',', (string)($_POST['kpis'] ?? ''))), fn($x) => $x !== ''));
    $d['stat_counts'] = array_values(array_filter(array_map('trim', explode(',', (string)($_POST['stats'] ?? ''))), fn($x) => $x !== ''));
    $d['clients'] = p_bi_list('clients');
    $alts = p_bi_list('case_alts');
    $imgs = array_map('trim', explode("\n", str_replace("\r", '', (string)($_POST['case_imgs'] ?? ''))));
    $imgs = array_values(array_filter($imgs, fn($x) => $x !== ''));
    $cases = [];
    foreach ($imgs as $i => $im) {
      $cases[] = ['img' => $im, 'alt' => $alts[$i] ?? ['id' => '', 'en' => '']];
    }
    $d['case_imgs'] = $cases;
    if (cms_save('home', $d, $_POST['rev'] ?? null)) $msg = 'Tersimpan. Teks hero/statistik diubah lewat menu Teks ID/EN.';
    else $err = cms_last_error();
    $d = cms_load('home');
  }
}
$hp = $d['hero_photo'] ?? [];
?>
<?php if ($msg !== ''): ?><p class="msg-ok"><?php echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8'); ?></p><?php endif; ?>
<?php if ($err !== ''): ?><p class="msg-err"><?php echo htmlspecialchars($err, ENT_QUOTES, 'UTF-8'); ?></p><?php endif; ?>
<?php if (($_GET['uperr'] ?? '') !== ''): ?><p class="msg-err"><?php echo htmlspecialchars($_GET['uperr'], ENT_QUOTES, 'UTF-8'); ?></p><?php endif; ?>
<form method="post" onsubmit="return confirmDel(this)"><input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8'); ?>">
<input type="hidden" name="rev" value="<?php echo htmlspecialchars((string)($d['updated_at'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
<p class="hint">Daftar klien satu industri per baris: tambah baris untuk menambah, hapus baris untuk menghapus.</p>
<div class="admin-card"><h2>Foto hero & angka</h2>
<div class="frow"><label>URL foto hero</label><input type="text" name="photo" value="<?php echo htmlspecialchars((string)($hp['src'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"></div>
<?php f_bi_text('photo_alt', $hp['alt'] ?? '', 'Alt foto hero'); ?>
<div class="frow"><label>Ukuran (lebar, tinggi)</label><div class="bilingual"><input type="text" name="photo_w" value="<?php echo htmlspecialchars((string)($hp['w'] ?? '880'), ENT_QUOTES, 'UTF-8'); ?>"><input type="text" name="photo_h" value="<?php echo htmlspecialchars((string)($hp['h'] ?? '1000'), ENT_QUOTES, 'UTF-8'); ?>"></div></div>
<div class="frow"><label>Angka KPI hero (koma, 3 angka)</label><input type="text" name="kpis" value="<?php echo htmlspecialchars(implode(',', $d['kpi_counts'] ?? []), ENT_QUOTES, 'UTF-8'); ?>"></div>
<div class="frow"><label>Angka statistik (koma, 4 angka)</label><input type="text" name="stats" value="<?php echo htmlspecialchars(implode(',', $d['stat_counts'] ?? []), ENT_QUOTES, 'UTF-8'); ?>"></div>
<?php f_bi_list('clients', $d['clients'] ?? [], 'Industri klien'); ?>
<div class="frow"><label>Gambar studi kasus (satu URL per baris, maks 3 gambar)</label><textarea name="case_imgs"><?php echo htmlspecialchars(implode("\n", array_column($d['case_imgs'] ?? [], 'img')), ENT_QUOTES, 'UTF-8'); ?></textarea></div>
<?php f_bi_list('case_alts', array_column($d['case_imgs'] ?? [], 'alt'), 'Alt gambar studi kasus'); ?>
<button class="btn btn-primary" type="submit">Simpan</button></div></form>
<?php f_upload_box('home.php'); ?>
<?php require_once __DIR__ . '/_foot.php'; ?>
