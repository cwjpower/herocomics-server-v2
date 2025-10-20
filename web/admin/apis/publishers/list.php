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
    
    // 페이지네이션
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
    $offset = ($page - 1) * $limit;
    
    // 검색
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $where = '';
    $params = [];
    
    if (!empty($search)) {
        $where = "WHERE publisher_name LIKE ? OR publisher_code LIKE ? OR publisher_name_ko LIKE ?";
        $search_term = "%$search%";
        $params = [$search_term, $search_term, $search_term];
    }
    
    // 전체 개수
    $count_sql = "SELECT COUNT(*) as total FROM bt_publishers $where";
    $stmt = $db->prepare($count_sql);
    if (!empty($params)) {
        $stmt->execute($params);
    } else {
        $stmt->execute();
    }
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // 목록 조회
    $sql = "SELECT 
                publisher_id,
                publisher_name,
                publisher_code,
                publisher_name_ko,
                contact_email,
                contact_phone,
                commission_rate,
                status,
                logo_url,
                description,
                website,
                created_at,
                updated_at
            FROM bt_publishers 
            $where
            ORDER BY created_at DESC
            LIMIT ? OFFSET ?";
    
    $stmt = $db->prepare($sql);
    
    if (!empty($params)) {
        $stmt->execute(array_merge($params, [$limit, $offset]));
    } else {
        $stmt->execute([$limit, $offset]);
    }
    
    $publishers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $publishers,
        'pagination' => [
            'total' => (int)$total,
            'page' => $page,
            'limit' => $limit,
            'total_pages' => ceil($total / $limit)
        ]
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
