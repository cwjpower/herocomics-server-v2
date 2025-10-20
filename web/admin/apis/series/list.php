<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');  // ← 이거 추가!

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

    $category = $_GET['category'] ?? '';
    $status = $_GET['status'] ?? '';
    $search = $_GET['search'] ?? '';

    $sql = "SELECT 
                s.series_id,
                s.publisher_id,
                s.series_name,
                s.series_name_en,
                s.author,
                s.category,
                s.description,
                s.cover_image,
                s.status,
                s.total_volumes,
                s.created_at,
                s.updated_at,
                COUNT(v.volume_id) as actual_volumes
            FROM bt_series s
            LEFT JOIN bt_volumes v ON s.series_id = v.series_id
            WHERE 1=1";

    $params = [];

    if ($category) {
        $sql .= " AND s.category = :category";
        $params[':category'] = $category;
    }

    if ($status) {
        $sql .= " AND s.status = :status";
        $params[':status'] = $status;
    }

    if ($search) {
        $sql .= " AND (s.series_name LIKE :search OR s.author LIKE :search)";
        $params[':search'] = "%$search%";
    }

    $sql .= " GROUP BY s.series_id ORDER BY s.created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $series = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'code' => 0,
        'msg' => 'success',
        'data' => $series,
        'total' => count($series)
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