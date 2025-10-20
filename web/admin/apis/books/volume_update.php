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

$volume_id = $data['volume_id'] ?? null;

if (!$volume_id) {
    echo json_encode(['code' => 1, 'msg' => 'volume_id required']);
    exit;
}

try {
    $db = getDbConnection();
    
    $updates = [];
    $params = [];
    
    if (isset($data['volume_title']) || isset($data['title'])) {
        $updates[] = "volume_title = ?";
        $params[] = $data['volume_title'] ?? $data['title'];
    }
    
    if (isset($data['volume_number'])) {
        $updates[] = "volume_number = ?";
        $params[] = $data['volume_number'];
    }
    
    if (isset($data['price'])) {
        $updates[] = "price = ?";
        $params[] = $data['price'];
    }
    
    if (isset($data['discount_rate'])) {
        $updates[] = "discount_rate = ?";
        $params[] = $data['discount_rate'];
    }
    
    if (isset($data['page_count']) || isset($data['total_pages'])) {
        $updates[] = "total_pages = ?";
        $params[] = $data['page_count'] ?? $data['total_pages'];
    }
    
    if (empty($updates)) {
        echo json_encode(['code' => 1, 'msg' => 'No fields to update']);
        exit;
    }
    
    $params[] = $volume_id;
    
    $sql = "UPDATE bt_volumes SET " . implode(", ", $updates) . " WHERE volume_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    
    echo json_encode([
        'code' => 0,
        'msg' => 'Volume updated successfully',
        'success' => true
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['code' => 1, 'msg' => 'Database error: ' . $e->getMessage()]);
}
?>
