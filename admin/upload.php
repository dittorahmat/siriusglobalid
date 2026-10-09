<?php
// admin/upload.php - terima upload, redirect kembali dengan ?uploaded=path.
require_once dirname(__DIR__) . '/includes/store.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/upload.php';
cms_require_admin();
$back = (string)($_POST['back'] ?? 'index.php');
if (!preg_match('/^[a-z0-9_\-\.]+\.php$/i', $back)) $back = 'index.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && cms_csrf_check($_POST['csrf'] ?? null)) {
  [$ok, $res] = cms_handle_upload($_FILES['img'] ?? []);
  if ($ok) {
    header('Location: ' . $back . '?uploaded=' . urlencode($res));
    exit;
  }
  header('Location: ' . $back . '?uperr=' . urlencode($res));
  exit;
}
header('Location: ' . $back);
exit;
