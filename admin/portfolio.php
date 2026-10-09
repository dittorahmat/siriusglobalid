<?php
// admin/portfolio.php - editor 8 kartu portofolio.
$ptitle = 'Portofolio';
require_once __DIR__ . '/_head.php';
require_once __DIR__ . '/_form.php';
$msg = ''; $err = '';
$d = cms_load('portfolio');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!cms_csrf_check($_POST['csrf'] ?? null)) { $err = 'Token kedaluwarsa. Muat ulang halaman.'; }
  else {
    $items = [];
    $cats = (array)($_POST['cat'] ?? []);
    $imgs = (array)($_POST['img'] ?? []);
    foreach ($cats as $i => $cat) {
      if (p_del((int)$i)) continue;
      $cat = trim((string)$cat);
      $blank = trim((string)($imgs[$i] ?? '')) === '' && trim((string)($_POST['title_id'][$i] ?? '')) === ''
        && trim((string)($_POST['desc_id'][$i] ?? '')) === '' && trim((string)($_POST['cat_t_id'][$i] ?? '')) === '';
      if ($blank) continue;
      $results = [];
      $rb = preg_split('/\r?\n/', (string)($_POST["res_b_id"][$i] ?? ''));
      $rs = preg_split('/\r?\n/', (string)($_POST["res_s_id"][$i] ?? ''));
      foreach ($rb as $j => $b) {
        $b = trim((string)$b); $s2 = trim((string)($rs[$j] ?? ''));
        if ($b === '' && $s2 === '') continue;
        $results[] = ['b' => ['id' => $b, 'en' => ''], 's' => ['id' => $s2, 'en' => '']];
      }
      $items[] = [
        'cat' => $cat,
        'img' => trim((string)($imgs[$i] ?? '')),
        'alt' => ['id' => trim((string)($_POST['alt_id'][$i] ?? '')), 'en' => trim((string)($_POST['alt_en'][$i] ?? ''))],
        'cat_t' => ['id' => trim((string)($_POST['cat_t_id'][$i] ?? '')), 'en' => trim((string)($_POST['cat_t_en'][$i] ?? ''))],
        'title' => ['id' => trim((string)($_POST['title_id'][$i] ?? '')), 'en' => trim((string)($_POST['title_en'][$i] ?? ''))],
        'desc' => ['id' => trim((string)($_POST['desc_id'][$i] ?? '')), 'en' => trim((string)($_POST['desc_en'][$i] ?? ''))],
        'results' => $results,
      ];
    }
    $d['items'] = $items;
    if (cms_save('portfolio', $d, $_POST['rev'] ?? null)) $msg = 'Tersimpan.';
    else $err = cms_last_error();
    $d = cms_load('portfolio');
  }
}
$items = $d['items'] ?? [];
if (($_GET['add'] ?? '') === '1' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
  $items[] = ['cat' => 'erp', 'img' => '', 'alt' => ['id' => '', 'en' => ''],
    'cat_t' => ['id' => '', 'en' => ''], 'title' => ['id' => '', 'en' => ''],
    'desc' => ['id' => '', 'en' => ''], 'results' => []];
}
?>
<?php if ($msg !== ''): ?><p class="msg-ok"><?php echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8'); ?></p><?php endif; ?>
<?php if ($err !== ''): ?><p class="msg-err"><?php echo htmlspecialchars($err, ENT_QUOTES, 'UTF-8'); ?></p><?php endif; ?>
<?php if (($_GET['uperr'] ?? '') !== ''): ?><p class="msg-err"><?php echo htmlspecialchars($_GET['uperr'], ENT_QUOTES, 'UTF-8'); ?></p><?php endif; ?>
<form method="post" onsubmit="return confirmDel(this)"><input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8'); ?>">
<input type="hidden" name="rev" value="<?php echo htmlspecialchars((string)($d['updated_at'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
<?php f_add_btn('Tambah kartu'); ?>
<?php foreach ($items as $i => $it): ?>
<div class="admin-card"><h2>Kartu <?php echo $i + 1; ?></h2>
<?php f_del_check($i, 'Hapus kartu ini saat simpan'); ?>
<div class="frow"><label>Kategori</label><select name="cat[]"><?php foreach (['erp', 'ai', 'fleet', 'app', 'iot', 'infra', 'sec', 'bi'] as $c): ?><option value="<?php echo $c; ?>"<?php echo ($it['cat'] ?? '') === $c ? ' selected' : ''; ?>><?php echo $c; ?></option><?php endforeach; ?></select></div>
<div class="frow"><label>URL gambar</label><input type="text" name="img[]" value="<?php echo htmlspecialchars((string)($it['img'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"></div>
<div class="frow"><label>Alt gambar (ID / EN)</label><div class="bilingual"><input type="text" name="alt_id[]" value="<?php echo htmlspecialchars((string)(($it['alt']['id'] ?? '')), ENT_QUOTES, 'UTF-8'); ?>"><input type="text" name="alt_en[]" value="<?php echo htmlspecialchars((string)(($it['alt']['en'] ?? '')), ENT_QUOTES, 'UTF-8'); ?>"></div></div>
<div class="frow"><label>Kategori tampil (ID / EN)</label><div class="bilingual"><input type="text" name="cat_t_id[]" value="<?php echo htmlspecialchars((string)(($it['cat_t']['id'] ?? '')), ENT_QUOTES, 'UTF-8'); ?>"><input type="text" name="cat_t_en[]" value="<?php echo htmlspecialchars((string)(($it['cat_t']['en'] ?? '')), ENT_QUOTES, 'UTF-8'); ?>"></div></div>
<div class="frow"><label>Judul (ID / EN)</label><div class="bilingual"><input type="text" name="title_id[]" value="<?php echo htmlspecialchars((string)(($it['title']['id'] ?? '')), ENT_QUOTES, 'UTF-8'); ?>"><input type="text" name="title_en[]" value="<?php echo htmlspecialchars((string)(($it['title']['en'] ?? '')), ENT_QUOTES, 'UTF-8'); ?>"></div></div>
<div class="frow"><label>Deskripsi (ID / EN)</label><div class="bilingual"><textarea name="desc_id[]"><?php echo htmlspecialchars((string)(($it['desc']['id'] ?? '')), ENT_QUOTES, 'UTF-8'); ?></textarea><textarea name="desc_en[]"><?php echo htmlspecialchars((string)(($it['desc']['en'] ?? '')), ENT_QUOTES, 'UTF-8'); ?></textarea></div></div>
<div class="frow"><label>Hasil (angka per baris, sejajar label)</label><div class="bilingual"><textarea name="res_b_id[]" placeholder="−11%"><?php echo htmlspecialchars(implode("\n", array_map(fn($r) => is_array($r['b'] ?? null) ? (string)($r['b']['id'] ?? '') : '', $it['results'] ?? [])), ENT_QUOTES, 'UTF-8'); ?></textarea><textarea name="res_s_id[]" placeholder="BBM"><?php echo htmlspecialchars(implode("\n", array_map(fn($r) => is_array($r['s'] ?? null) ? (string)($r['s']['id'] ?? '') : '', $it['results'] ?? [])), ENT_QUOTES, 'UTF-8'); ?></textarea></div></div>
</div>
<?php endforeach; ?>
<div class="admin-card"><button class="btn btn-primary" type="submit">Simpan semua</button>
<p class="hint">Item yang semua teksnya dikosongkan otomatis dilewati saat render.</p></div></form>
<?php f_upload_box('portfolio.php'); ?>
<?php require_once __DIR__ . '/_foot.php'; ?>
