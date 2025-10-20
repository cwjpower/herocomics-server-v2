<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// POST 요청만 허용
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
    $publisher_id = $_POST['publisher_id'] ?? 1;
    $series_name = $_POST['series_name'] ?? '';
    $series_name_en = $_POST['series_name_en'] ?? '';
    $author = $_POST['author'] ?? '';
    $category = $_POST['category'] ?? 'MARVEL';
    $description = $_POST['description'] ?? '';
    $cover_image = $_POST['cover_image'] ?? null;
    $status = $_POST['status'] ?? 'ongoing';

    // 필수 필드 체크
    if (empty($series_name)) {
        http_response_code(400);
        echo json_encode([
            'code' => 1,
            'msg' => 'series_name is required',
            'data' => null
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }

    // 카테고리 검증
    $valid_categories = ['MARVEL', 'DC', 'IMAGE', 'JAPANESE', 'KOREAN'];
    if (!in_array($category, $valid_categories)) {
        http_response_code(400);
        echo json_encode([
            'code' => 1,
            'msg' => 'Invalid category. Must be one of: ' . implode(', ', $valid_categories),
            'data' => null
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }

    // 상태 검증
    $valid_status = ['ongoing', 'completed'];
    if (!in_array($status, $valid_status)) {
        http_response_code(400);
        echo json_encode([
            'code' => 1,
            'msg' => 'Invalid status. Must be one of: ' . implode(', ', $valid_status),
            'data' => null
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }

    // 시리즈 추가
    $sql = "INSERT INTO bt_series 
            (publisher_id, series_name, series_name_en, author, category, description, cover_image, status)
            VALUES 
            (:publisher_id, :series_name, :series_name_en, :author, :category, :description, :cover_image, :status)";

    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        ':publisher_id' => $publisher_id,
        ':series_name' => $series_name,
        ':series_name_en' => $series_name_en,
        ':author' => $author,
        ':category' => $category,
        ':description' => $description,
        ':cover_image' => $cover_image,
        ':status' => $status
    ]);

    if ($result) {
        $series_id = $pdo->lastInsertId();

        // 추가된 시리즈 정보 조회
        $stmt = $pdo->prepare("SELECT * FROM bt_series WHERE series_id = :series_id");
        $stmt->execute([':series_id' => $series_id]);
        $series = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode([
            'code' => 0,
            'msg' => 'Series created successfully',
            'data' => $series
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    } else {
        throw new Exception('Failed to create series');
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