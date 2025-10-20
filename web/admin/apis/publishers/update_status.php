<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// OPTIONS preflight 요청 처리
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['code' => 1, 'msg' => 'POST 요청만 허용됩니다.'], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['code' => 1, 'msg' => 'POST 요청만 허용됩니다.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$db_host = 'herocomics-mariadb';
$db_name = 'herocomics';
$db_user = 'root';
$db_pass = 'rootpass';

try {
    $pdo = new PDO(
        "mysql:host={$db_host};dbname={$db_name};charset=utf8mb4",
        $db_user,
        $db_pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $publisher_id = $_POST['publisher_id'] ?? 0;
    $status = $_POST['status'] ?? '';

    if (empty($publisher_id)) {
        echo json_encode(['code' => 1, 'msg' => 'publisher_id가 필요합니다.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (!in_array($status, ['pending', 'active', 'suspended'])) {
        echo json_encode(['code' => 1, 'msg' => '잘못된 상태값입니다.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $check = $pdo->prepare("SELECT publisher_id FROM bt_publishers WHERE publisher_id = ?");
    $check->execute([$publisher_id]);
    
    if (!$check->fetch()) {
        echo json_encode(['code' => 1, 'msg' => '출판사를 찾을 수 없습니다.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $stmt = $pdo->prepare("
        UPDATE bt_publishers 
        SET status = ?, updated_at = NOW()
        WHERE publisher_id = ?
    ");

    $stmt->execute([$status, $publisher_id]);

    echo json_encode([
        'code' => 0,
        'msg' => '출판사 상태가 변경되었습니다.',
        'data' => [
            'publisher_id' => $publisher_id,
            'status' => $status
        ]
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    echo json_encode([
        'code' => 1,
        'msg' => 'DB 오류: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
