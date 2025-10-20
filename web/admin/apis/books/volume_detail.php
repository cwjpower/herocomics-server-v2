<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

try {
    $pdo = new PDO(
        "mysql:host=herocomics-mariadb;dbname=herocomics;charset=utf8mb4",
        "root",
        "rootpass",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    $volumeId = isset($_GET['volume_id']) ? (int)$_GET['volume_id'] : 0;
    
    if ($volumeId <= 0) {
        throw new Exception('volume_id is required');
    }
    
    $sql = "
        SELECT 
            v.volume_id, v.volume_number, v.volume_title, v.cover_image,
            v.normal_price, v.price, v.discount_rate, v.is_free,
            v.total_pages, v.publish_date, v.status, v.created_at,
            s.series_id, s.series_name, s.series_name_en, s.author,
            s.category, s.comics_brand, s.description, s.total_volumes,
            p.publisher_name
        FROM bt_volumes v
        INNER JOIN bt_series s ON v.series_id = s.series_id
        LEFT JOIN bt_publishers p ON v.publisher_id = p.publisher_id
        WHERE v.volume_id = :volume_id
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':volume_id' => $volumeId]);
    $volume = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$volume) {
        throw new Exception('Volume not found');
    }
    
    echo json_encode([
        'code' => 0,
        'msg' => 'success',
        'data' => ['volume' => $volume]
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode(['code' => 400, 'msg' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>
