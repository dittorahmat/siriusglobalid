<?php
// admin/_form.php - helper field bilingual + textarea list + upload box.
declare(strict_types=1);

function f_bi_text(string $name, $v, string $label): void {
  $id = is_array($v) ? (string)($v['id'] ?? '') : (string)$v;
  $en = is_array($v) ? (string)($v['en'] ?? '') : '';
  echo '<div class="frow"><label>' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</label><div class="bilingual">'
    . '<input type="text" name="' . $name . '_id" value="' . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . '" placeholder="Indonesia">'
    . '<input type="text" name="' . $name . '_en" value="' . htmlspecialchars($en, ENT_QUOTES, 'UTF-8') . '" placeholder="English (opsional)">'
    . '</div></div>';
}

function f_bi_area(string $name, $v, string $label): void {
  $id = is_array($v) ? (string)($v['id'] ?? '') : (string)$v;
  $en = is_array($v) ? (string)($v['en'] ?? '') : '';
  echo '<div class="frow"><label>' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</label><div class="bilingual">'
    . '<textarea name="' . $name . '_id" placeholder="Indonesia">' . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . '</textarea>'
    . '<textarea name="' . $name . '_en" placeholder="English (opsional)">' . htmlspecialchars($en, ENT_QUOTES, 'UTF-8') . '</textarea>'
    . '</div></div>';
}

/** Satu item per baris; ID dan EN sejajar per nomor baris. */
function f_bi_list(string $name, array $items, string $label): void {
  $ids = []; $ens = [];
  foreach ($items as $it) {
    $ids[] = is_array($it) ? (string)($it['id'] ?? '') : (string)$it;
    $ens[] = is_array($it) ? (string)($it['en'] ?? '') : '';
  }
  echo '<div class="frow"><label>' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . ' <span class="hint">(satu per baris, ID dan EN sejajar)</span></label><div class="bilingual">'
    . '<textarea name="' . $name . '_id" placeholder="Baris Indonesia">' . htmlspecialchars(implode("\n", $ids), ENT_QUOTES, 'UTF-8') . '</textarea>'
    . '<textarea name="' . $name . '_en" placeholder="Baris English">' . htmlspecialchars(implode("\n", $ens), ENT_QUOTES, 'UTF-8') . '</textarea>'
    . '</div></div>';
}

function p_bi(string $name): array {
  return ['id' => trim((string)($_POST[$name . '_id'] ?? '')), 'en' => trim((string)($_POST[$name . '_en'] ?? ''))];
}

function p_bi_list(string $name): array {
  $ids = preg_split('/\r?\n/', (string)($_POST[$name . '_id'] ?? ''));
  $ens = preg_split('/\r?\n/', (string)($_POST[$name . '_en'] ?? ''));
  $out = [];
  $n = max(count($ids), count($ens));
  for ($i = 0; $i < $n; $i++) {
    $id = trim((string)($ids[$i] ?? ''));
    $en = trim((string)($ens[$i] ?? ''));
    if ($id === '' && $en === '') continue;
    $out[] = ['id' => $id, 'en' => $en];
  }
  return $out;
}

/** Tombol tambah item: GET ?add=1 me-render satu blok kosong (tanpa menulis apa pun). */
function f_add_btn(string $label): void {
  $q = $_GET;
  $q['add'] = '1';
  $href = htmlspecialchars('?' . http_build_query($q), ENT_QUOTES, 'UTF-8');
  echo '<p><a class="btn btn-ghost btn-add" href="' . $href . '">+ ' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</a></p>';
}

/** Checkbox hapus per item: diabaikan saat save bila dicentang + konfirmasi submit. */
function f_del_check(int $idx, string $label = 'Hapus item ini saat simpan'): void {
  f_del_named('del[' . $idx . ']', $label);
}

/** Varian nama bebas (mis. del_t[0] untuk tier). */
function f_del_named(string $name, string $label = 'Hapus item ini saat simpan'): void {
  echo '<label class="del-row"><input type="checkbox" name="' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '" value="1"> '
    . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</label>';
}

/** Apakah indeks diminta hapus. */
function p_del(int $idx): bool {
  return !empty($_POST['del'][$idx]);
}

/** Kotak upload cepat: hasil path ditampilkan untuk disalin ke field gambar. */function f_upload_box(string $back): void {
  $up = $_GET['uploaded'] ?? '';
  echo '<div class="admin-card"><h2>Upload foto</h2>'
    . '<p class="hint">JPG/PNG/WebP, maks 2MB. Setelah upload, salin path ke field gambar.</p>'
    . ($up !== '' ? '<p class="msg-ok">Tersimpan: <code>' . htmlspecialchars($up, ENT_QUOTES, 'UTF-8') . '</code></p>' : '')
    . '<form method="post" action="upload.php" enctype="multipart/form-data">'
    . '<input type="hidden" name="csrf" value="' . htmlspecialchars(cms_csrf(), ENT_QUOTES, 'UTF-8') . '">'
    . '<input type="hidden" name="back" value="' . htmlspecialchars($back, ENT_QUOTES, 'UTF-8') . '">'
    . '<div class="frow"><input type="file" name="img" accept=".jpg,.jpeg,.png,.webp" required aria-label="Pilih file gambar"></div>'
    . '<button class="btn btn-primary btn-sm" type="submit">Upload</button></form></div>';
}
