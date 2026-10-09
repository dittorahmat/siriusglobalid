<?php
// admin/services.php - daftar 9 layanan.
$ptitle = 'Layanan';
require_once __DIR__ . '/_head.php';
?>
<div class="admin-card"><h2>Sembilan layanan</h2>
<table class="admin-t"><tr><th>Slug</th><th>Judul (ID)</th><th>Diubah</th><th></th></tr>
<?php foreach (CMS_SERVICE_SLUGS as $s): $d = cms_service($s); ?>
<tr><td><code><?php echo $s; ?></code></td><td><?php echo htmlspecialchars(cms_b($d['title'] ?? '-', 'id'), ENT_QUOTES, 'UTF-8'); ?></td><td><?php echo htmlspecialchars((string)($d['updated_at'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?></td><td><a href="service_edit.php?slug=<?php echo urlencode($s); ?>">Ubah</a></td></tr>
<?php endforeach; ?>
</table></div>
<?php require_once __DIR__ . '/_foot.php'; ?>
