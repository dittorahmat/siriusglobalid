<?php
// admin/login.php
require_once dirname(__DIR__) . '/includes/store.php';
require_once dirname(__DIR__) . '/includes/auth.php';
cms_session_start();
if (cms_logged_in()) { header('Location: index.php'); exit; }
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  [$ok, $msg] = cms_try_login(trim((string)($_POST['user'] ?? '')), (string)($_POST['pass'] ?? ''));
  if ($ok) { header('Location: index.php'); exit; }
  $err = $msg;
}
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Masuk - CMS Sirius</title>
<link rel="stylesheet" href="../css/tokens.css"><link rel="stylesheet" href="../css/base.css"><link rel="stylesheet" href="../css/components.css">
<style>.login{max-width:420px;margin:12vh auto;padding:24px}.frow{display:grid;gap:6px;margin:12px 0}.frow input{font-size:16px;padding:10px 12px;border:1px solid var(--line);border-radius:10px;width:100%}.msg-err{background:#fdecec;border:1px solid #f5c6c6;padding:12px 16px;border-radius:12px;margin-bottom:16px}</style></head>
<body><div class="login admin-card" style="background:#fff;border:1px solid var(--line);border-radius:14px">
<h1>CMS Sirius</h1>
<?php if ($err !== ''): ?><p class="msg-err"><?php echo htmlspecialchars($err, ENT_QUOTES, 'UTF-8'); ?></p><?php endif; ?>
<form method="post">
<div class="frow"><label for="u">Pengguna</label><input id="u" name="user" type="text" autocomplete="username" required></div>
<div class="frow"><label for="p">Kata sandi</label><input id="p" name="pass" type="password" autocomplete="current-password" required></div>
<button class="btn btn-primary" type="submit">Masuk</button>
</form></div></body></html>
