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

$volume_id = $data['volume_id'] ?? null;

if (!$volume_id) {
    echo json_encode(['code' => 1, 'msg' => 'volume_id required']);
    exit;
}

try {
    $db = getDbConnection();
    $stmt = $db->prepare("DELETE FROM bt_volumes WHERE volume_id = ?");
    $stmt->execute([$volume_id]);
    
    echo json_encode([
        'code' => 0,
        'msg' => 'Volume deleted successfully',
        'success' => true
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['code' => 1, 'msg' => 'Database error: ' . $e->getMessage()]);
}
?>
