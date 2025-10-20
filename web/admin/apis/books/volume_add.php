<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../conf/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['code' => 1, 'msg' => 'POST method required']);
    exit;
}

// JSON body 읽기
$json_input = file_get_contents('php://input');
$data = json_decode($json_input, true);

if ($data === null) {
    $data = $_POST;
}

$series_id = $data['series_id'] ?? null;
$volume_title = $data['volume_title'] ?? $data['title'] ?? null;
$volume_number = $data['volume_number'] ?? null;
$price = $data['price'] ?? 0;
$discount_rate = $data['discount_rate'] ?? 0;
$total_pages = $data['page_count'] ?? $data['total_pages'] ?? 0;

if (!$series_id || !$volume_title || $volume_number === null) {
    echo json_encode([
        'code' => 1, 
        'msg' => 'Required fields missing'
    ]);
    exit;
}

try {
    $db = getDbConnection();
    
    // 시리즈 정보에서 publisher_id 가져오기
    $stmt = $db->prepare("SELECT publisher_id FROM bt_series WHERE series_id = ?");
    $stmt->execute([$series_id]);
    $series = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$series) {
        echo json_encode(['code' => 1, 'msg' => 'Series not found']);
        exit;
    }
    
    $publisher_id = $series['publisher_id'];
    
    // 권 추가
    $stmt = $db->prepare("
        INSERT INTO bt_volumes (
            series_id, 
            publisher_id,
            volume_title, 
            volume_number, 
            price, 
            discount_rate, 
            total_pages,
            created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    
    $stmt->execute([
        $series_id,
        $publisher_id,
        $volume_title,
        $volume_number,
        $price,
        $discount_rate,
        $total_pages
    ]);
    
    $volume_id = $db->lastInsertId();
    
    echo json_encode([
        'code' => 0,
        'msg' => 'Volume added successfully',
        'success' => true,
        'volume_id' => $volume_id,
        'data' => [
            'volume_id' => $volume_id
        ]
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['code' => 1, 'msg' => 'Database error: ' . $e->getMessage()]);
}
?>
