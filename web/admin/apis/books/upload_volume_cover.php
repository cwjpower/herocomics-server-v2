<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../conf/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['code' => 1, 'msg' => 'POST method required']);
    exit;
}

$volume_id = $_POST['volume_id'] ?? null;

if (!$volume_id) {
    echo json_encode(['code' => 1, 'msg' => 'volume_id required']);
    exit;
}

if (!isset($_FILES['cover_image'])) {
    echo json_encode(['code' => 1, 'msg' => 'No file uploaded']);
    exit;
}

$file = $_FILES['cover_image'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['code' => 1, 'msg' => 'File upload error: ' . $file['error']]);
    exit;
}

$allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

if (!in_array($file_extension, $allowed_extensions)) {
    echo json_encode(['code' => 1, 'msg' => 'Invalid file type. Allowed: jpg, jpeg, png, gif, webp']);
    exit;
}

$max_size = 10 * 1024 * 1024;
if ($file['size'] > $max_size) {
    echo json_encode(['code' => 1, 'msg' => 'File too large. Max 10MB']);
    exit;
}

$upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/volume_covers/';
$new_filename = 'volume_' . $volume_id . '_' . time() . '.' . $file_extension;
$upload_path = $upload_dir . $new_filename;

if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
    echo json_encode(['code' => 1, 'msg' => 'Failed to save file']);
    exit;
}

try {
    $db = getDbConnection();
    
    // 이전 이미지 삭제
    $stmt = $db->prepare("SELECT cover_image FROM bt_volumes WHERE volume_id = ?");
    $stmt->execute([$volume_id]);
    $old_image = $stmt->fetchColumn();
    
    if ($old_image && file_exists($upload_dir . $old_image)) {
        unlink($upload_dir . $old_image);
    }
    
    // 새 이미지 경로 업데이트
    $stmt = $db->prepare("UPDATE bt_volumes SET cover_image = ? WHERE volume_id = ?");
    $stmt->execute([$new_filename, $volume_id]);
    
    echo json_encode([
        'code' => 0,
        'msg' => 'Upload success',
        'data' => [
            'filename' => $new_filename,
            'url' => '/uploads/volume_covers/' . $new_filename
        ]
    ]);
    
} catch (PDOException $e) {
    if (file_exists($upload_path)) {
        unlink($upload_path);
    }
    echo json_encode(['code' => 1, 'msg' => 'Database error: ' . $e->getMessage()]);
}
?>
