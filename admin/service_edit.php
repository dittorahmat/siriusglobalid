<?php
// admin/service_edit.php - editor satu layanan (?slug=).
$ptitle = 'Ubah layanan';
require_once __DIR__ . '/_head.php';
require_once __DIR__ . '/_form.php';
$slug = (string)($_GET['slug'] ?? $_POST['slug'] ?? '');
if (!in_array($slug, CMS_SERVICE_SLUGS, true)) { echo '<p class="msg-err">Slug tidak dikenal.</p>'; require_once __DIR__ . '/_foot.php'; exit; }
$msg = ''; $err = '';
$d = cms_service($slug);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!cms_csrf_check($_POST['csrf'] ?? null)) { $err = 'Token kedaluwarsa. Muat ulang halaman.'; }
  else {
    $d['title'] = p_bi('title'); $d['lead'] = p_bi('lead'); $d['h1'] = p_bi('h1');
    $d['meta_desc'] = p_bi('meta_desc');
    $intro = $d['intro'] ?? [];
    $intro['heading'] = p_bi('intro_h'); $intro['body'] = p_bi('intro_b');
    $intro['checklist'] = p_bi_list('checklist');
    $d['intro'] = $intro;
    $ml = p_bi_list('m_label'); $mv = p_bi_list('m_value');
    $mm = [];
    foreach ($ml as $i => $x) { $mm[] = ['label' => $x, 'value' => $mv[$i] ?? ['id' => '', 'en' => '']]; }
    $d['metrics'] = $mm;
    $pr = $d['pricing'] ?? [];
    $pr['heading'] = p_bi('pr_h'); $pr['body'] = p_bi('pr_b'); $pr['quote_btn'] = p_bi('pr_q');
    $tiers = [];
    $nn = count((array)($_POST['tier_n_id'] ?? []));
    $keepFeat = isset($_POST['feat_idx']) ? (int)$_POST['feat_idx'] : -1;
    for ($i = 0; $i < $nn; $i++) {
      if (!empty($_POST['del_t'][$i])) continue;
      $g = function (string $k) use ($i): array {
        return ['id' => trim((string)($_POST[$k . '_id'][$i] ?? '')), 'en' => trim((string)($_POST[$k . '_en'][$i] ?? ''))];
      };
      $tTier = $g('tier_t'); $tName = $g('tier_n'); $tVal = $g('tier_v');
      $iIds = preg_split('/\r?\n/', (string)($_POST['tier_items_id'][$i] ?? ''));
      $iEns = preg_split('/\r?\n/', (string)($_POST['tier_items_en'][$i] ?? ''));
      $tItems = [];
      $m = max(count($iIds), count($iEns));
      for ($j = 0; $j < $m; $j++) {
        $a = trim((string)($iIds[$j] ?? '')); $b = trim((string)($iEns[$j] ?? ''));
        if ($a === '' && $b === '') continue;
        $tItems[] = ['id' => $a, 'en' => $b];
      }
      $blank = $tTier['id'] === '' && $tTier['en'] === '' && $tName['id'] === '' && $tName['en'] === ''
        && $tVal['id'] === '' && $tVal['en'] === '' && $tItems === [];
      if ($blank) continue;
      $tiers[] = ['tier' => $tTier, 'name' => $tName, 'value' => $tVal, 'items' => $tItems,
        'featured' => ($i === $keepFeat), '_ridx' => $i];
    }
    if ($keepFeat < 0) {
      foreach ($tiers as $k => $t) { $tiers[$k]['featured'] = false; }
      if (isset($tiers[1]) && count($tiers) === 3) $tiers[1]['featured'] = true;
      elseif (isset($tiers[0])) $tiers[0]['featured'] = true;
    }
    foreach ($tiers as $k => $t) unset($tiers[$k]['_ridx']);
    $pr['tiers'] = array_values($tiers);
    $d['pricing'] = $pr;
    $st = p_bi_list('step_t'); $sd = p_bi_list('step_d');
    $steps = [];
    foreach ($st as $i => $x) { $steps[] = ['t' => $x, 'd' => $sd[$i] ?? ['id' => '', 'en' => '']]; }
    $d['steps'] = ['heading' => p_bi('steps_h'), 'items' => $steps];
    $fq = p_bi_list('faq_q'); $fa = p_bi_list('faq_a');
    $faqs = [];
    foreach ($fq as $i => $x) { $faqs[] = ['q' => $x, 'a' => $fa[$i] ?? ['id' => '', 'en' => '']]; }
    $d['faqs'] = $faqs;
    $d['cta'] = ['t' => p_bi('cta_t'), 'd' => p_bi('cta_d'), 'btn' => p_bi('cta_b'), 'href' => trim((string)($_POST['cta_href'] ?? ''))];
    if (cms_save('service:' . $slug, $d, $_POST['rev'] ?? null)) $msg = 'Tersimpan.';
    else $err = cms_last_error();
    $d = cms_service($slug);
  }
}
if (!function_exists('col2')) {
function col2(array $items, string $k, string $lg): string {
  $o = [];
  foreach ($items as $it) { $v = $it[$k] ?? ''; $o[] = is_array($v) ? (string)($v[$lg] ?? '') : (string)$v; }
  return implode("\n", $o);
}
}
?>
<?php if ($msg !== ''): ?><p class="msg-ok"><?php echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8'); ?></p><?php endif; ?>
<?php if ($err !== ''): ?><p class="msg-err"><?php echo htmlspecialchars($err, ENT_QUOTES, 'UTF-8'); ?></p><?php endif; ?>
<p><a href="services.php">← Kembali ke daftar</a> · <a href="../layanan/<?php echo urlencode($slug); ?>.php" target="_blank" rel="noopener">Lihat halaman</a></p>
<form method="post" onsubmit="return confirmDel(this)"><input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8'); ?>">
<input type="hidden" name="slug" value="<?php echo htmlspecialchars($slug, ENT_QUOTES, 'UTF-8'); ?>">
<input type="hidden" name="rev" value="<?php echo htmlspecialchars((string)($d['updated_at'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
<div class="admin-card"><h2>Hero</h2>
<?php f_bi_text('title', $d['title'] ?? '', 'Judul (h1)'); ?>
<?php f_bi_area('lead', $d['lead'] ?? '', 'Lead'); ?>
<?php f_bi_text('h1', $d['h1'] ?? '', 'H1 (bila beda dari judul)'); ?>
<?php f_bi_text('meta_desc', $d['meta_desc'] ?? '', 'Meta description'); ?>
</div>
<div class="admin-card"><h2>Intro + checklist + metrik</h2>
<?php f_bi_text('intro_h', $d['intro']['heading'] ?? '', 'Judul intro'); ?>
<?php f_bi_area('intro_b', $d['intro']['body'] ?? '', 'Paragraf intro'); ?>
<?php f_bi_list('checklist', $d['intro']['checklist'] ?? [], 'Checklist (baris baru = item baru)'); ?>
<div class="frow"><label>Label metrik (ID / EN)</label><div class="bilingual"><textarea name="m_label_id"><?php echo htmlspecialchars($mlId, ENT_QUOTES, 'UTF-8'); ?></textarea><textarea name="m_label_en"><?php echo htmlspecialchars($mlEn, ENT_QUOTES, 'UTF-8'); ?></textarea></div></div>
<div class="frow"><label>Nilai metrik (ID / EN)</label><div class="bilingual"><textarea name="m_value_id"><?php echo htmlspecialchars(col2($d['metrics'] ?? [], 'value', 'id'), ENT_QUOTES, 'UTF-8'); ?></textarea><textarea name="m_value_en"><?php echo htmlspecialchars(col2($d['metrics'] ?? [], 'value', 'en'), ENT_QUOTES, 'UTF-8'); ?></textarea></div></div>
</div>
<div class="admin-card"><h2>Harga (tier dinamis)</h2>
<?php f_bi_text('pr_h', $d['pricing']['heading'] ?? '', 'Judul'); ?>
<?php f_bi_area('pr_b', $d['pricing']['body'] ?? '', 'Subjudul'); ?>
<?php f_bi_text('pr_q', $d['pricing']['quote_btn'] ?? '', 'Teks tombol'); ?>
<?php
$tiersView = $d['pricing']['tiers'] ?? [];
if (($_GET['add'] ?? '') === 'tier' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
  $tiersView[] = ['tier' => ['id' => '', 'en' => ''], 'name' => ['id' => '', 'en' => ''],
    'value' => ['id' => '', 'en' => ''], 'items' => [], 'featured' => false];
}
$defFeat = -1;
foreach ($tiersView as $di => $dt) { if (!empty($dt['featured'])) { $defFeat = $di; break; } }
if ($defFeat < 0) $defFeat = (count($tiersView) === 3) ? 1 : 0;
$q = $_GET; $q['add'] = 'tier';
echo '<p><a class="btn btn-ghost btn-add" href="?' . htmlspecialchars(http_build_query($q), ENT_QUOTES, 'UTF-8') . '">+ Tambah tier</a></p>';
foreach ($tiersView as $t => $tier): ?>
<h3 style="margin-top:14px">Tier <?php echo $t + 1; ?></h3>
<?php f_del_named('del_t[' . $t . ']', 'Hapus tier ini saat simpan'); ?>
<div class="frow"><label><input type="radio" name="feat_idx" value="<?php echo $t; ?>"<?php echo $defFeat === $t ? ' checked' : ''; ?> style="width:22px;height:22px;vertical-align:middle"> Jadikan featured (gaya sorot)</label></div>
<div class="frow"><label>Label tier (ID / EN)</label><div class="bilingual"><input type="text" name="tier_t_id[]" value="<?php echo htmlspecialchars((string)(is_array($tier['tier'] ?? null) ? ($tier['tier']['id'] ?? '') : ''), ENT_QUOTES, 'UTF-8'); ?>"><input type="text" name="tier_t_en[]" value="<?php echo htmlspecialchars((string)(is_array($tier['tier'] ?? null) ? ($tier['tier']['en'] ?? '') : ''), ENT_QUOTES, 'UTF-8'); ?>"></div></div>
<div class="frow"><label>Nama paket (ID / EN)</label><div class="bilingual"><input type="text" name="tier_n_id[]" value="<?php echo htmlspecialchars((string)(is_array($tier['name'] ?? null) ? ($tier['name']['id'] ?? '') : ''), ENT_QUOTES, 'UTF-8'); ?>"><input type="text" name="tier_n_en[]" value="<?php echo htmlspecialchars((string)(is_array($tier['name'] ?? null) ? ($tier['name']['en'] ?? '') : ''), ENT_QUOTES, 'UTF-8'); ?>"></div></div>
<div class="frow"><label>Harga (ID / EN)</label><div class="bilingual"><input type="text" name="tier_v_id[]" value="<?php echo htmlspecialchars((string)(is_array($tier['value'] ?? null) ? ($tier['value']['id'] ?? '') : ''), ENT_QUOTES, 'UTF-8'); ?>"><input type="text" name="tier_v_en[]" value="<?php echo htmlspecialchars((string)(is_array($tier['value'] ?? null) ? ($tier['value']['en'] ?? '') : ''), ENT_QUOTES, 'UTF-8'); ?>"></div></div>
<div class="frow"><label>Isi paket (satu per baris, ID / EN sejajar. Baris baru = item baru)</label><div class="bilingual"><textarea name="tier_items_id[]"><?php echo htmlspecialchars(col2($tier['items'] ?? [], 'id', 'id'), ENT_QUOTES, 'UTF-8'); ?></textarea><textarea name="tier_items_en[]"><?php echo htmlspecialchars(col2($tier['items'] ?? [], 'id', 'en'), ENT_QUOTES, 'UTF-8'); ?></textarea></div></div>
<?php endforeach; ?>
</div>
<div class="admin-card"><h2>Tahapan + FAQ + CTA</h2>
<p class="hint">Daftar di bawah satu item per baris: tambah baris baru untuk item baru, hapus baris untuk menghapus item.</p>
<?php f_bi_text('steps_h', $d['steps']['heading'] ?? '', 'Judul tahapan'); ?>
<div class="frow"><label>Judul tahap (ID / EN)</label><div class="bilingual"><textarea name="step_t_id"><?php echo htmlspecialchars(col2($d['steps']['items'] ?? [], 't', 'id'), ENT_QUOTES, 'UTF-8'); ?></textarea><textarea name="step_t_en"><?php echo htmlspecialchars(col2($d['steps']['items'] ?? [], 't', 'en'), ENT_QUOTES, 'UTF-8'); ?></textarea></div></div>
<div class="frow"><label>Deskripsi tahap (ID / EN)</label><div class="bilingual"><textarea name="step_d_id"><?php echo htmlspecialchars(col2($d['steps']['items'] ?? [], 'd', 'id'), ENT_QUOTES, 'UTF-8'); ?></textarea><textarea name="step_d_en"><?php echo htmlspecialchars(col2($d['steps']['items'] ?? [], 'd', 'en'), ENT_QUOTES, 'UTF-8'); ?></textarea></div></div>
<div class="frow"><label>Pertanyaan FAQ (ID / EN)</label><div class="bilingual"><textarea name="faq_q_id"><?php echo htmlspecialchars(col2($d['faqs'] ?? [], 'q', 'id'), ENT_QUOTES, 'UTF-8'); ?></textarea><textarea name="faq_q_en"><?php echo htmlspecialchars(col2($d['faqs'] ?? [], 'q', 'en'), ENT_QUOTES, 'UTF-8'); ?></textarea></div></div>
<div class="frow"><label>Jawaban FAQ (ID / EN)</label><div class="bilingual"><textarea name="faq_a_id"><?php echo htmlspecialchars(col2($d['faqs'] ?? [], 'a', 'id'), ENT_QUOTES, 'UTF-8'); ?></textarea><textarea name="faq_a_en"><?php echo htmlspecialchars(col2($d['faqs'] ?? [], 'a', 'en'), ENT_QUOTES, 'UTF-8'); ?></textarea></div></div>
<?php f_bi_text('cta_t', $d['cta']['t'] ?? '', 'Judul CTA'); ?>
<?php f_bi_area('cta_d', $d['cta']['d'] ?? '', 'Deskripsi CTA'); ?>
<?php f_bi_text('cta_b', $d['cta']['btn'] ?? '', 'Tombol CTA'); ?>
<div class="frow"><label>Link CTA</label><input type="text" name="cta_href" value="<?php echo htmlspecialchars((string)($d['cta']['href'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"></div>
<button class="btn btn-primary" type="submit">Simpan</button></div></form>
<?php require_once __DIR__ . '/_foot.php'; ?>
