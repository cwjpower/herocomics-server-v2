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
    $publisher_name = trim($_POST['publisher_name'] ?? '');
    $publisher_code = trim($_POST['publisher_code'] ?? '');
    $publisher_name_ko = trim($_POST['publisher_name_ko'] ?? '');
    $contact_name = trim($_POST['contact_name'] ?? '');
    $contact_email = trim($_POST['contact_email'] ?? '');
    $contact_phone = trim($_POST['contact_phone'] ?? '');
    $commission_rate = isset($_POST['commission_rate']) ? (float)$_POST['commission_rate'] : null;
    $description = trim($_POST['description'] ?? '');
    $website = trim($_POST['website'] ?? '');
    $status = trim($_POST['status'] ?? '');
    
    // 필수 값 체크
    if ($publisher_id <= 0) {
        throw new Exception('출판사 ID가 필요합니다.');
    }
    
    if (empty($publisher_name)) {
        throw new Exception('출판사명은 필수입니다.');
    }
    
    if (empty($publisher_code)) {
        throw new Exception('출판사 코드는 필수입니다.');
    }
    
    // 존재 여부 체크
    $check_sql = "SELECT COUNT(*) as cnt FROM bt_publishers WHERE publisher_id = ?";
    $stmt = $db->prepare($check_sql);
    $stmt->execute([$publisher_id]);
    $exists = $stmt->fetch(PDO::FETCH_ASSOC)['cnt'];
    
    if ($exists == 0) {
        throw new Exception('존재하지 않는 출판사입니다.');
    }
    
    // 중복 체크 - 이름 (자기 자신 제외)
    $check_sql = "SELECT COUNT(*) as cnt FROM bt_publishers 
                  WHERE publisher_name = ? AND publisher_id != ?";
    $stmt = $db->prepare($check_sql);
    $stmt->execute([$publisher_name, $publisher_id]);
    $duplicate = $stmt->fetch(PDO::FETCH_ASSOC)['cnt'];
    
    if ($duplicate > 0) {
        throw new Exception('이미 존재하는 출판사명입니다.');
    }
    
    // 중복 체크 - 코드 (자기 자신 제외)
    $check_sql = "SELECT COUNT(*) as cnt FROM bt_publishers 
                  WHERE publisher_code = ? AND publisher_id != ?";
    $stmt = $db->prepare($check_sql);
    $stmt->execute([$publisher_code, $publisher_id]);
    $duplicate = $stmt->fetch(PDO::FETCH_ASSOC)['cnt'];
    
    if ($duplicate > 0) {
        throw new Exception('이미 존재하는 출판사 코드입니다.');
    }
    
    // 수정
    $sql = "UPDATE bt_publishers 
            SET publisher_name = ?,
                publisher_code = ?,
                publisher_name_ko = ?,
                contact_name = ?,
                contact_email = ?,
                contact_phone = ?,
                " . ($commission_rate !== null ? "commission_rate = ?," : "") . "
                description = ?,
                website = ?,
                " . (!empty($status) ? "status = ?," : "") . "
                updated_at = NOW()
            WHERE publisher_id = ?";
    
    // 파라미터 배열 구성
    $params = [
        $publisher_name,
        $publisher_code,
        $publisher_name_ko,
        $contact_name,
        $contact_email,
        $contact_phone,
    ];
    
    if ($commission_rate !== null) {
        $params[] = $commission_rate;
    }
    
    $params[] = $description;
    $params[] = $website;
    
    if (!empty($status)) {
        $params[] = $status;
    }
    
    $params[] = $publisher_id;
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    
    echo json_encode([
        'success' => true,
        'message' => '출판사 정보가 수정되었습니다.'
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
