<?php
header('Content-Type: application/json; charset=utf-8');

$dir = __DIR__ . '/../uploads';
if (!is_dir($dir)) mkdir($dir, 0775, true);

if (!isset($_FILES['file'])) { http_response_code(400); echo json_encode(['error'=>'no file']); exit; }
$f = $_FILES['file'];
if ($f['error'] !== UPLOAD_ERR_OK) { http_response_code(400); echo json_encode(['error'=>'upload error']); exit; }
if ($f['size'] > 5*1024*1024) { http_response_code(400); echo json_encode(['error'=>'file too large']); exit; }

$fi = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($fi, $f['tmp_name']);
finfo_close($fi);

if (!in_array($mime, ['image/jpeg','image/png'])) { http_response_code(400); echo json_encode(['error'=>'invalid type']); exit; }

$ext  = $mime === 'image/png' ? '.png' : '.jpg';
$name = bin2hex(random_bytes(8)) . $ext;
$dest = $dir . DIRECTORY_SEPARATOR . $name;

if (!move_uploaded_file($f['tmp_name'], $dest)) {
  http_response_code(500); echo json_encode(['error'=>'move failed']); exit;
}
// đảm bảo web server đọc được
@chmod($dest, 0644);

/**
 * Tạo URL tuyệt đối chính xác ngay cả khi site đặt trong /subfolder
 * Ví dụ: http(s)://domain.com/myapp/uploads/xxxx.jpg
 */
$scheme   = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host     = $_SERVER['HTTP_HOST'];
$script   = $_SERVER['SCRIPT_NAME'];           // /subfolder/api/upload.php
$basePath = rtrim(dirname($script), '/\\');    // /subfolder/api
$public   = preg_replace('#/api$#', '', $basePath) . '/uploads/' . $name;

$url = $scheme . '://' . $host . $public;

echo json_encode(['url' => $url]);
