<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// DB 연결
$host = 'herocomics-mariadb';
$user = 'root';
$pass = 'rootpass';
$db = 'herocomics';
$conn = mysqli_connect($host, $user, $pass, $db);
mysqli_set_charset($conn, "utf8");

if (!$conn) {
    die(json_encode([
        'code' => 1,
        'msg' => 'DB Connection Error: ' . mysqli_connect_error()
    ]));
}

try {
    // 파라미터
    $search = $_GET['search'] ?? '';
    $publisher_id = $_GET['publisher_id'] ?? '';
    $category = $_GET['category'] ?? '';
    $status = $_GET['status'] ?? '';
    
    // SQL 쿼리 (publisher_id로 수정)
    $sql = "
        SELECT 
            s.series_id,
            s.series_name as series_title,
            s.series_name_en as series_code,
            s.category,
            s.status,
            s.cover_image as cover_url,
            s.created_at,
            p.publisher_name,
            p.publisher_id,
            COUNT(DISTINCT v.volume_id) as volume_count,
            COALESCE(SUM(v.total_pages), 0) as total_pages
        FROM bt_series s
        LEFT JOIN bt_publishers p ON s.publisher_id = p.publisher_id
        LEFT JOIN bt_volumes v ON s.series_id = v.series_id
        WHERE 1=1
    ";
    
    // 검색
    if ($search) {
        $search_param = '%' . mysqli_real_escape_string($conn, $search) . '%';
        $sql .= " AND (s.series_name LIKE '$search_param' OR s.series_name_en LIKE '$search_param' OR p.publisher_name LIKE '$search_param')";
    }
    
    // 출판사 필터
    if ($publisher_id) {
        $publisher_id = mysqli_real_escape_string($conn, $publisher_id);
        $sql .= " AND s.publisher_id = '$publisher_id'";
    }
    
    // 카테고리 필터
    if ($category) {
        $category = mysqli_real_escape_string($conn, $category);
        $sql .= " AND s.category = '$category'";
    }
    
    // 상태 필터 (ongoing/completed)
    if ($status) {
        $status = mysqli_real_escape_string($conn, $status);
        $sql .= " AND s.status = '$status'";
    }
    
    $sql .= " GROUP BY s.series_id ORDER BY s.created_at DESC";
    
    $result = mysqli_query($conn, $sql);
    $series_list = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $series_list[] = $row;
    }
    
    // 통계
    $stats_sql = "
        SELECT 
            COUNT(DISTINCT s.series_id) as total_series,
            COUNT(DISTINCT v.volume_id) as total_volumes,
            COUNT(DISTINCT p.publisher_id) as total_publishers
        FROM bt_series s
        LEFT JOIN bt_volumes v ON s.series_id = v.series_id
        LEFT JOIN bt_publishers p ON s.publisher_id = p.publisher_id
    ";
    $stats_result = mysqli_query($conn, $stats_sql);
    $stats = mysqli_fetch_assoc($stats_result);
    
    echo json_encode([
        'code' => 0,
        'msg' => 'success',
        'data' => $series_list,
        'stats' => $stats
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    echo json_encode([
        'code' => 1,
        'msg' => 'Error: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

mysqli_close($conn);
?>
