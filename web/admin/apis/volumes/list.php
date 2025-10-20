<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$host = getenv('DB_HOST') ?: 'herocomics-mariadb';
$dbname = getenv('DB_NAME') ?: 'herocomics';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: 'rootpass';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 파라미터 받기
    $series_id = $_GET['series_id'] ?? '';
    $status = $_GET['status'] ?? '';
    $is_free = $_GET['is_free'] ?? '';

    // 쿼리 작성
    $sql = "SELECT 
                v.volume_id,
                v.series_id,
                v.publisher_id,
                v.volume_number,
                v.volume_title,
                v.cover_image,
                v.price,
                v.is_free,
                v.total_pages,
                v.publish_date,
                v.status,
                v.created_at,
                v.updated_at,
                s.series_name,
                s.series_name_en,
                s.category
            FROM bt_volumes v
            LEFT JOIN bt_series s ON v.series_id = s.series_id
            WHERE 1=1";

    $params = [];

    // 시리즈 필터
    if ($series_id) {
        $sql .= " AND v.series_id = :series_id";
        $params[':series_id'] = $series_id;
    }

    // 상태 필터
    if ($status) {
        $sql .= " AND v.status = :status";
        $params[':status'] = $status;
    }

    // 무료 필터
    if ($is_free !== '') {
        $sql .= " AND v.is_free = :is_free";
        $params[':is_free'] = $is_free;
    }

    $sql .= " ORDER BY v.series_id ASC, v.volume_number ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $volumes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 응답
    echo json_encode([
        'code' => 0,
        'msg' => 'success',
        'data' => $volumes,
        'total' => count($volumes)
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'code' => 1,
        'msg' => 'Database error: ' . $e->getMessage(),
        'data' => null
    ], JSON_UNESCAPED_UNICODE);
}
?>