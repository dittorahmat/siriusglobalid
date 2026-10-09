<?php
// admin/index.php - dashboard.
$ptitle = 'Dashboard';
require_once __DIR__ . '/_head.php';
$dataDir = cms_data_dir();
$domains = ['settings', 'home', 'tentang', 'layanan', 'portfolio', 'kontak', 'privasi', 'syarat'];
$pf = cms_portfolio();
$svcCount = 0;
foreach (CMS_SERVICE_SLUGS as $s) { if (!empty(cms_service($s))) $svcCount++; }
$backups = glob(cms_backup_dir() . '/*.json') ?: [];
?>
<div class="admin-card"><h2>Status</h2>
<table class="admin-t">
<tr><th>Data dir</th><td><code><?php echo htmlspecialchars($dataDir, ENT_QUOTES, 'UTF-8'); ?></code> <?php echo is_dir($dataDir) ? '(ok)' : '(akan dibuat saat save pertama)'; ?></td></tr>
<tr><th>Layanan terisi</th><td><?php echo $svcCount; ?> / 9</td></tr>
<tr><th>Portofolio</th><td><?php echo count($pf['items'] ?? []); ?> item</td></tr>
<tr><th>Snapshot backup</th><td><?php echo count($backups); ?> file</td></tr>
</table>
<p class="hint" style="margin-top:10px">Terakhir diubah:</p>
<table class="admin-t">
<?php foreach ($domains as $d): $dd = cms_load($d); ?>
<tr><th><?php echo $d; ?></th><td><?php echo htmlspecialchars((string)($dd['updated_at'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?></td></tr>
<?php endforeach; ?>
</table></div>
<div class="admin-card"><h2>Panduan singkat</h2>
<p class="hint">Ubah teks lewat menu di atas, simpan, lalu buka halaman situs untuk memeriksa. Setiap save otomatis membuat snapshot backup. Bahasa Inggris boleh dikosongkan: situs menampilkan versi Indonesia sebagai fallback. Jangan edit file di <code>data/</code> manual via File Manager kecuali darurat.</p></div>
<?php require_once __DIR__ . '/_foot.php'; ?>
