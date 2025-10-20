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

    // 업데이트할 필드만 수집
    $updates = [];
    $params = [':series_id' => $series_id];

    if (isset($_POST['publisher_id'])) {
        $updates[] = "publisher_id = :publisher_id";
        $params[':publisher_id'] = $_POST['publisher_id'];
    }
    if (isset($_POST['series_name'])) {
        $updates[] = "series_name = :series_name";
        $params[':series_name'] = $_POST['series_name'];
    }
    if (isset($_POST['series_name_en'])) {
        $updates[] = "series_name_en = :series_name_en";
        $params[':series_name_en'] = $_POST['series_name_en'];
    }
    if (isset($_POST['author'])) {
        $updates[] = "author = :author";
        $params[':author'] = $_POST['author'];
    }
    if (isset($_POST['category'])) {
        $valid_categories = ['MARVEL', 'DC', 'IMAGE', 'JAPANESE', 'KOREAN'];
        if (!in_array($_POST['category'], $valid_categories)) {
            http_response_code(400);
            echo json_encode([
                'code' => 1,
                'msg' => 'Invalid category',
                'data' => null
            ], JSON_UNESCAPED_UNICODE);
            exit();
        }
        $updates[] = "category = :category";
        $params[':category'] = $_POST['category'];
    }
    if (isset($_POST['description'])) {
        $updates[] = "description = :description";
        $params[':description'] = $_POST['description'];
    }
    if (isset($_POST['cover_image'])) {
        $updates[] = "cover_image = :cover_image";
        $params[':cover_image'] = $_POST['cover_image'];
    }
    if (isset($_POST['status'])) {
        $valid_status = ['ongoing', 'completed'];
        if (!in_array($_POST['status'], $valid_status)) {
            http_response_code(400);
            echo json_encode([
                'code' => 1,
                'msg' => 'Invalid status',
                'data' => null
            ], JSON_UNESCAPED_UNICODE);
            exit();
        }
        $updates[] = "status = :status";
        $params[':status'] = $_POST['status'];
    }
    if (isset($_POST['total_volumes'])) {
        $updates[] = "total_volumes = :total_volumes";
        $params[':total_volumes'] = $_POST['total_volumes'];
    }

    // 업데이트할 내용이 없으면 에러
    if (empty($updates)) {
        http_response_code(400);
        echo json_encode([
            'code' => 1,
            'msg' => 'No fields to update',
            'data' => null
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }

    // 업데이트 실행
    $sql = "UPDATE bt_series SET " . implode(', ', $updates) . " WHERE series_id = :series_id";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute($params);

    if ($result) {
        // 업데이트된 시리즈 정보 조회
        $stmt = $pdo->prepare("SELECT * FROM bt_series WHERE series_id = :series_id");
        $stmt->execute([':series_id' => $series_id]);
        $updated_series = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode([
            'code' => 0,
            'msg' => 'Series updated successfully',
            'data' => $updated_series
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    } else {
        throw new Exception('Failed to update series');
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