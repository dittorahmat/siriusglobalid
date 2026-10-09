<?php
// admin/_head.php - guard + kerangka atas admin. Variabel: $ptitle.
require_once dirname(__DIR__) . '/includes/store.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/upload.php';
cms_require_admin();
$csrf = cms_csrf();
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($ptitle ?? 'Admin', ENT_QUOTES, 'UTF-8'); ?> - CMS Sirius</title>
<link rel="stylesheet" href="../css/tokens.css"><link rel="stylesheet" href="../css/base.css"><link rel="stylesheet" href="../css/components.css">
<style>
.admin-wrap{max-width:1080px;margin:0 auto;padding:28px 20px 80px}
.admin-nav{display:flex;gap:8px;flex-wrap:wrap;margin:16px 0 24px}
.admin-nav a{padding:10px 14px;border:1px solid var(--line);border-radius:10px;text-decoration:none;font-size:.9rem;min-height:44px;display:inline-flex;align-items:center}
.admin-nav a.on{background:var(--navy-900);color:#fff;border-color:var(--navy-900)}
.admin-card{background:#fff;border:1px solid var(--line);border-radius:14px;padding:20px;margin-bottom:18px}
.admin-card h2{font-size:1.1rem;margin-bottom:6px}
.frow{display:grid;gap:6px;margin:12px 0}
.frow label{font-weight:600;font-size:.9rem}
.frow input[type=text],.frow input[type=url],.frow input[type=email],.frow textarea,.frow select{font-size:16px;padding:10px 12px;border:1px solid var(--line);border-radius:10px;width:100%}
.frow textarea{min-height:90px;font-family:inherit}
.bilingual{display:grid;grid-template-columns:1fr 1fr;gap:12px}
@media(max-width:620px){.bilingual{grid-template-columns:1fr}}
.hint{font-size:.82rem;color:#5b6b82}
.msg-ok{background:#e6f6ef;border:1px solid #bfe9da;padding:12px 16px;border-radius:12px;margin-bottom:16px}
.msg-err{background:#fdecec;border:1px solid #f5c6c6;padding:12px 16px;border-radius:12px;margin-bottom:16px}
table.admin-t{width:100%;border-collapse:collapse;font-size:.9rem}
table.admin-t th,table.admin-t td{border:1px solid var(--line);padding:8px 10px;text-align:left;vertical-align:top}
.table-scroll{overflow-x:auto;-webkit-overflow-scrolling:touch}
.btn-add{min-height:44px;display:inline-flex;align-items:center}
.del-row{display:inline-flex;gap:8px;align-items:center;font-size:.85rem;color:#8a2b2b;margin-top:10px}
.del-row input{width:22px;height:22px}
</style></head>
<body><div class="admin-wrap">
<p><a href="../index.php">← Lihat situs</a> · <a href="logout.php">Keluar</a></p>
<h1>CMS Sirius</h1>
<nav class="admin-nav" aria-label="Menu admin">
<a href="index.php">Dashboard</a><a href="settings.php">Settings</a><a href="home.php">Beranda</a><a href="tentang.php">Tentang</a><a href="services.php">Layanan</a><a href="portfolio.php">Portofolio</a><a href="kontakdoc.php">Kontak/Privasi/Syarat</a><a href="i18n.php">Teks ID/EN</a><a href="backup.php">Backup</a>
</nav>
