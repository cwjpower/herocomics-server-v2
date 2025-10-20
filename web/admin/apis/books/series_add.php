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

$publisher_id = $data['publisher_id'] ?? null;
$series_name = $data['series_name'] ?? $data['title'] ?? null;
$category = $data['category'] ?? null;
$description = $data['description'] ?? '';

if (!$publisher_id || !$series_name || !$category) {
    echo json_encode(['code' => 1, 'msg' => 'Required fields missing']);
    exit;
}

try {
    $db = getDbConnection();
    
    $stmt = $db->prepare("
        INSERT INTO bt_series (
            publisher_id,
            series_name,
            category,
            description,
            created_at
        ) VALUES (?, ?, ?, ?, NOW())
    ");
    
    $stmt->execute([
        $publisher_id,
        $series_name,
        $category,
        $description
    ]);
    
    $series_id = $db->lastInsertId();
    
    echo json_encode([
        'code' => 0,
        'msg' => 'Series added successfully',
        'success' => true,
        'series_id' => $series_id,
        'data' => [
            'series_id' => $series_id
        ]
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['code' => 1, 'msg' => 'Database error: ' . $e->getMessage()]);
}
?>
