<?php
// admin/backup_download.php - streaming ZIP semua data runtime + manifest.
require_once dirname(__DIR__) . '/includes/store.php';
require_once dirname(__DIR__) . '/includes/auth.php';
cms_require_admin();
$files = cms_backup_files();
$tmp = tempnam(sys_get_temp_dir(), 'sgi-backup') . '.zip';
$zip = new ZipArchive();
if ($zip->open($tmp, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
  http_response_code(500);
  exit('Gagal membuat arsip.');
}
foreach ($files as $name => $path) $zip->addFile($path, $name);
$zip->addFromString('manifest.json', json_encode([
  'at' => gmdate('c'), 'files' => array_keys($files),
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
$zip->close();
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="sirius-cms-' . gmdate('Ymd-His') . '.zip"');
header('Content-Length: ' . filesize($tmp));
readfile($tmp);
@unlink($tmp);
exit;
