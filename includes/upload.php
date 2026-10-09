<?php
// upload.php - upload foto tervalidasi: finfo + getimagesize, allowlist,
// tolak ekstensi ganda, rename acak, simpan assets/uploads/.
declare(strict_types=1);

const CMS_UPLOAD_MAX = 2097152; // 2MB
const CMS_UPLOAD_MIMES = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

function cms_upload_dir(): string {
  $docroot = !empty($_SERVER['DOCUMENT_ROOT']) ? (realpath($_SERVER['DOCUMENT_ROOT']) ?: '') : '';
  $base = $docroot !== '' ? $docroot : dirname(__DIR__);
  $d = $base . '/assets/uploads';
  if (!is_dir($d)) @mkdir($d, 0755, true);
  return $d;
}

/** Proses satu file $_FILES. Mengembalikan [ok, path-atau-pesan]. Path relatif web. */
function cms_handle_upload(array $file): array {
  if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) return [false, 'Tidak ada file.'];
  if ($file['error'] !== UPLOAD_ERR_OK) return [false, 'Upload gagal (kode ' . (int)$file['error'] . ').'];
  if (($file['size'] ?? 0) > CMS_UPLOAD_MAX) return [false, 'File melebihi 2MB.'];
  $tmp = (string)($file['tmp_name'] ?? '');
  if (!is_uploaded_file($tmp)) return [false, 'File tidak valid.'];
  $finfo = new finfo(FILEINFO_MIME_TYPE);
  $mime = $finfo->file($tmp);
  if (!isset(CMS_UPLOAD_MIMES[$mime])) return [false, 'Tipe file tidak diizinkan. Hanya JPG, PNG, WebP.'];
  $img = @getimagesize($tmp);
  if ($img === false) return [false, 'Berkas bukan gambar valid.'];
  $orig = (string)($file['name'] ?? '');
  if (preg_match('/\.(php|phtml|phar|cgi|pl|asp|aspx|jsp|sh)\b/i', $orig)) {
    return [false, 'Nama file mengandung ekstensi berbahaya.'];
  }
  $ext = CMS_UPLOAD_MIMES[$mime];
  $name = bin2hex(random_bytes(12)) . '.' . $ext;
  $dest = cms_upload_dir() . '/' . $name;
  if (!@move_uploaded_file($tmp, $dest)) return [false, 'Gagal menyimpan file.'];
  @chmod($dest, 0644);
  return [true, 'assets/uploads/' . $name];
}
