<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'code' => 1,
        'msg' => 'Method not allowed',
        'data' => null
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

$host = getenv('DB_HOST') ?: 'herocomics-mariadb';
$dbname = getenv('DB_NAME') ?: 'herocomics';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: 'rootpass';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // POST 데이터 받기
    $series_id = $_POST['series_id'] ?? 0;
    $publisher_id = $_POST['publisher_id'] ?? 1;
    $volume_number = $_POST['volume_number'] ?? 0;
    $volume_title = $_POST['volume_title'] ?? '';
    $cover_image = $_POST['cover_image'] ?? null;
    $price = $_POST['price'] ?? 0;
    $is_free = $_POST['is_free'] ?? 0;
    $total_pages = $_POST['total_pages'] ?? 0;
    $publish_date = $_POST['publish_date'] ?? null;
    $status = $_POST['status'] ?? 'draft';

    // 필수 필드 체크
    if (empty($series_id)) {
        http_response_code(400);
        echo json_encode([
            'code' => 1,
            'msg' => 'series_id is required',
            'data' => null
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }

    if (empty($volume_number)) {
        http_response_code(400);
        echo json_encode([
            'code' => 1,
            'msg' => 'volume_number is required',
            'data' => null
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }

    // 시리즈 존재 확인
    $stmt = $pdo->prepare("SELECT * FROM bt_series WHERE series_id = :series_id");
    $stmt->execute([':series_id' => $series_id]);
    $series = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$series) {
        http_response_code(404);
        echo json_encode([
            'code' => 1,
            'msg' => 'Series not found',
            'data' => null
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }

    // 중복 권수 체크
    $stmt = $pdo->prepare("SELECT * FROM bt_volumes WHERE series_id = :series_id AND volume_number = :volume_number");
    $stmt->execute([
        ':series_id' => $series_id,
        ':volume_number' => $volume_number
    ]);

    if ($stmt->fetch()) {
        http_response_code(400);
        echo json_encode([
            'code' => 1,
            'msg' => 'Volume number already exists in this series',
            'data' => null
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }

    // 상태 검증
    $valid_status = ['draft', 'published'];
    if (!in_array($status, $valid_status)) {
        http_response_code(400);
        echo json_encode([
            'code' => 1,
            'msg' => 'Invalid status. Must be one of: ' . implode(', ', $valid_status),
            'data' => null
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }

    // 권 추가
    $sql = "INSERT INTO bt_volumes 
            (series_id, publisher_id, volume_number, volume_title, cover_image, price, is_free, total_pages, publish_date, status)
            VALUES 
            (:series_id, :publisher_id, :volume_number, :volume_title, :cover_image, :price, :is_free, :total_pages, :publish_date, :status)";

    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        ':series_id' => $series_id,
        ':publisher_id' => $publisher_id,
        ':volume_number' => $volume_number,
        ':volume_title' => $volume_title,
        ':cover_image' => $cover_image,
        ':price' => $price,
        ':is_free' => $is_free,
        ':total_pages' => $total_pages,
        ':publish_date' => $publish_date,
        ':status' => $status
    ]);

    if ($result) {
        $volume_id = $pdo->lastInsertId();

        // 시리즈의 total_volumes 업데이트
        $stmt = $pdo->prepare("UPDATE bt_series SET total_volumes = (SELECT COUNT(*) FROM bt_volumes WHERE series_id = :series_id) WHERE series_id = :series_id");
        $stmt->execute([':series_id' => $series_id]);

        // 추가된 권 정보 조회
        $stmt = $pdo->prepare("SELECT v.*, s.series_name, s.category FROM bt_volumes v LEFT JOIN bt_series s ON v.series_id = s.series_id WHERE v.volume_id = :volume_id");
        $stmt->execute([':volume_id' => $volume_id]);
        $volume = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode([
            'code' => 0,
            'msg' => 'Volume created successfully',
            'data' => $volume
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    } else {
        throw new Exception('Failed to create volume');
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'code' => 1,
        'msg' => 'Database error: ' . $e->getMessage(),
        'data' => null
    ], JSON_UNESCAPED_UNICODE);
}
?>