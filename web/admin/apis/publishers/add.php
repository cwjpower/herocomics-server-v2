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
    $publisher_name = trim($_POST['publisher_name'] ?? '');
    $publisher_code = trim($_POST['publisher_code'] ?? '');
    $publisher_name_ko = trim($_POST['publisher_name_ko'] ?? '');
    $contact_name = trim($_POST['contact_name'] ?? '');
    $contact_email = trim($_POST['contact_email'] ?? '');
    $contact_phone = trim($_POST['contact_phone'] ?? '');
    $commission_rate = isset($_POST['commission_rate']) ? (float)$_POST['commission_rate'] : 30.00;
    $description = trim($_POST['description'] ?? '');
    $website = trim($_POST['website'] ?? '');
    $status = trim($_POST['status'] ?? 'active'); // 기본값 active
    
    // 필수 값 체크
    if (empty($publisher_name)) {
        throw new Exception('출판사명은 필수입니다.');
    }
    
    if (empty($publisher_code)) {
        throw new Exception('출판사 코드는 필수입니다.');
    }
    
    // 중복 체크 (이름)
    $check_sql = "SELECT COUNT(*) as cnt FROM bt_publishers WHERE publisher_name = ?";
    $stmt = $db->prepare($check_sql);
    $stmt->execute([$publisher_name]);
    $exists = $stmt->fetch(PDO::FETCH_ASSOC)['cnt'];
    
    if ($exists > 0) {
        throw new Exception('이미 존재하는 출판사명입니다.');
    }
    
    // 중복 체크 (코드)
    $check_sql = "SELECT COUNT(*) as cnt FROM bt_publishers WHERE publisher_code = ?";
    $stmt = $db->prepare($check_sql);
    $stmt->execute([$publisher_code]);
    $exists = $stmt->fetch(PDO::FETCH_ASSOC)['cnt'];
    
    if ($exists > 0) {
        throw new Exception('이미 존재하는 출판사 코드입니다.');
    }
    
    // 추가
    $sql = "INSERT INTO bt_publishers 
            (publisher_name, publisher_code, publisher_name_ko, 
             contact_name, contact_email, contact_phone, 
             commission_rate, description, website, status, 
             created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([
        $publisher_name,
        $publisher_code,
        $publisher_name_ko,
        $contact_name,
        $contact_email,
        $contact_phone,
        $commission_rate,
        $description,
        $website,
        $status
    ]);
    
    $publisher_id = $db->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'message' => '출판사가 추가되었습니다.',
        'publisher_id' => $publisher_id
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
