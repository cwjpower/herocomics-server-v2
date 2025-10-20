<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../../conf/db.php';

$category = $_GET['category'] ?? null;
$page = intval($_GET['page'] ?? 1);
$limit = intval($_GET['limit'] ?? 20);
$offset = ($page - 1) * $limit;

try {
    $db = getDbConnection();
    
    $where = "";
    $params = [];
    
    if ($category) {
        $where = "WHERE s.category = ?";
        $params[] = $category;
    }
    
    // 시리즈 목록 조회
    $stmt = $db->prepare("
        SELECT 
            s.*,
            p.publisher_name,
            COUNT(v.volume_id) as volume_count
        FROM bt_series s
        LEFT JOIN bt_publishers p ON s.publisher_id = p.publisher_id
        LEFT JOIN bt_volumes v ON s.series_id = v.series_id
        $where
        GROUP BY s.series_id
        ORDER BY s.created_at DESC
        LIMIT ? OFFSET ?
    ");
    
    $params[] = $limit;
    $params[] = $offset;
    $stmt->execute($params);
    $series = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 전체 개수
    $count_params = $category ? [$category] : [];
    $stmt = $db->prepare("SELECT COUNT(*) FROM bt_series s $where");
    $stmt->execute($count_params);
    $total = $stmt->fetchColumn();
    
    echo json_encode([
        'code' => 0,
        'msg' => 'Success',
        'data' => [
            'series' => $series,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total
            ]
        ]
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['code' => 500, 'msg' => 'Database error: ' . $e->getMessage()]);
}
?>
