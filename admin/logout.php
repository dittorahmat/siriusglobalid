<?php
// admin/logout.php
require_once dirname(__DIR__) . '/includes/store.php';
require_once dirname(__DIR__) . '/includes/auth.php';
cms_logout();
header('Location: login.php');
exit;
