<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../../conf/db.php';

try {
    $db = db_connect();
    
    // 전체 목록 조회 (순서대로)
    $sql = "SELECT 
                ID as banner_id,
                bnr_title as title,
                bnr_url as link_url,
                bnr_target as target,
                hide_or_show as status,
                bnr_file_url as image_url,
                bnr_order as display_order,
                bnr_from as start_date,
                bnr_to as end_date,
                bnr_created as created_at,
                user_id
            FROM bt_banner
            ORDER BY bnr_order ASC, ID DESC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute();
    
    $banners = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // status를 is_active로 변환
    foreach ($banners as &$banner) {
        $banner['is_active'] = ($banner['status'] === 'show') ? 1 : 0;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $banners,
        'total' => count($banners)
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
