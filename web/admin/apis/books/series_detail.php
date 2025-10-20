<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../../conf/db.php';

$series_id = $_GET['series_id'] ?? null;

if (!$series_id) {
    echo json_encode(['code' => 400, 'msg' => 'series_id required']);
    exit;
}

try {
    $db = getDbConnection();
    
    // 시리즈 정보 조회
    $stmt = $db->prepare("
        SELECT 
            s.*,
            p.publisher_name
        FROM bt_series s
        LEFT JOIN bt_publishers p ON s.publisher_id = p.publisher_id
        WHERE s.series_id = ?
    ");
    $stmt->execute([$series_id]);
    $series = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$series) {
        echo json_encode(['code' => 400, 'msg' => 'Series not found']);
        exit;
    }
    
    // 해당 시리즈의 모든 권 조회
    $stmt = $db->prepare("
        SELECT 
            volume_id,
            series_id,
            volume_number,
            volume_title as title,
            cover_image,
            price,
            discount_rate,
            is_free,
            total_pages as page_count,
            status,
            created_at
        FROM bt_volumes
        WHERE series_id = ?
        ORDER BY volume_number ASC
    ");
    $stmt->execute([$series_id]);
    $volumes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'code' => 0,
        'msg' => 'Success',
        'data' => [
            'series' => $series,
            'volumes' => $volumes
        ]
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['code' => 500, 'msg' => 'Database error: ' . $e->getMessage()]);
}
?>
