<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../../conf/db.php';

try {
    $db = db_connect();
    
    // POST 데이터 받기
    $publisher_id = (int)($_POST['publisher_id'] ?? 0);
    
    // 필수 값 체크
    if ($publisher_id <= 0) {
        throw new Exception('출판사 ID가 필요합니다.');
    }
    
    // 존재 여부 체크
    $check_sql = "SELECT COUNT(*) as cnt FROM bt_publishers WHERE publisher_id = ?";
    $stmt = $db->prepare($check_sql);
    $stmt->execute([$publisher_id]);
    $exists = $stmt->fetch(PDO::FETCH_ASSOC)['cnt'];
    
    if ($exists == 0) {
        throw new Exception('존재하지 않는 출판사입니다.');
    }
    
    // 시리즈가 있는지 체크
    $series_check = "SELECT COUNT(*) as cnt FROM bt_series WHERE publisher_id = ?";
    $stmt = $db->prepare($series_check);
    $stmt->execute([$publisher_id]);
    $series_count = $stmt->fetch(PDO::FETCH_ASSOC)['cnt'];
    
    if ($series_count > 0) {
        throw new Exception("이 출판사에 {$series_count}개의 시리즈가 있습니다. 시리즈를 먼저 삭제해주세요.");
    }
    
    // 삭제
    $sql = "DELETE FROM bt_publishers WHERE publisher_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$publisher_id]);
    
    echo json_encode([
        'success' => true,
        'message' => '출판사가 삭제되었습니다.'
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
