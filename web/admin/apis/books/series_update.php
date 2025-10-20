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

$json_input = file_get_contents('php://input');
$data = json_decode($json_input, true);

if ($data === null) {
    $data = $_POST;
}

$series_id = $data['series_id'] ?? null;

if (!$series_id) {
    echo json_encode(['code' => 1, 'msg' => 'series_id required']);
    exit;
}

try {
    $db = getDbConnection();
    
    $updates = [];
    $params = [];
    
    if (isset($data['series_name']) || isset($data['title'])) {
        $updates[] = "series_name = ?";
        $params[] = $data['series_name'] ?? $data['title'];
    }
    
    if (isset($data['category'])) {
        $updates[] = "category = ?";
        $params[] = $data['category'];
    }
    
    if (isset($data['description'])) {
        $updates[] = "description = ?";
        $params[] = $data['description'];
    }
    
    if (empty($updates)) {
        echo json_encode(['code' => 1, 'msg' => 'No fields to update']);
        exit;
    }
    
    $params[] = $series_id;
    
    $sql = "UPDATE bt_series SET " . implode(", ", $updates) . " WHERE series_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    
    echo json_encode([
        'code' => 0,
        'msg' => 'Series updated successfully',
        'success' => true
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['code' => 1, 'msg' => 'Database error: ' . $e->getMessage()]);
}
?>
