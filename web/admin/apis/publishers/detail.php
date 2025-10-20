<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$db_host = 'herocomics-mariadb';
$db_name = 'herocomics';
$db_user = 'root';
$db_pass = 'rootpass';

try {
    $pdo = new PDO(
        "mysql:host={$db_host};dbname={$db_name};charset=utf8mb4",
        $db_user,
        $db_pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $publisher_id = $_GET['publisher_id'] ?? 0;

    if (empty($publisher_id)) {
        echo json_encode(['code' => 1, 'msg' => 'publisher_id가 필요합니다.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // 출판사 기본 정보
    $stmt = $pdo->prepare("
        SELECT 
            publisher_id,
            publisher_name,
            publisher_code,
            publisher_name_ko,
            contact_name,
            contact_email,
            contact_phone,
            commission_rate,
            bank_name,
            bank_account,
            account_holder,
            status,
            logo_url,
            description,
            website,
            approval_date,
            created_at,
            updated_at
        FROM bt_publishers
        WHERE publisher_id = ?
    ");
    $stmt->execute([$publisher_id]);
    $publisher = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$publisher) {
        echo json_encode(['code' => 1, 'msg' => '출판사를 찾을 수 없습니다.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // 시리즈 목록
    $series_stmt = $pdo->prepare("
        SELECT 
            s.series_id,
            s.series_name,
            s.series_name_en,
            s.author,
            s.category,
            s.cover_image,
            s.status,
            s.created_at,
            COUNT(DISTINCT v.volume_id) as volume_count,
            COALESCE(SUM(v.total_pages), 0) as total_pages
        FROM bt_series s
        LEFT JOIN bt_volumes v ON s.series_id = v.series_id
        WHERE s.publisher_id = ?
        GROUP BY s.series_id
        ORDER BY s.created_at DESC
    ");
    $series_stmt->execute([$publisher_id]);
    $series_list = $series_stmt->fetchAll(PDO::FETCH_ASSOC);

    // 통계 정보
    $stats = [
        'total_series' => count($series_list),
        'total_volumes' => 0,
        'total_pages' => 0,
    ];

    foreach ($series_list as $series) {
        $stats['total_volumes'] += $series['volume_count'];
        $stats['total_pages'] += $series['total_pages'];
    }

    // 매출 정보 (주문 테이블이 있다면)
    // 나중에 구현할 예정이므로 일단 0으로
    $sales = [
        'total_sales' => 0,
        'publisher_revenue' => 0,
        'platform_commission' => 0,
        'pending_settlement' => 0,
    ];

    echo json_encode([
        'code' => 0,
        'msg' => '성공',
        'data' => [
            'publisher' => $publisher,
            'series' => $series_list,
            'stats' => $stats,
            'sales' => $sales,
        ]
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    echo json_encode([
        'code' => 1,
        'msg' => 'DB 오류: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    echo json_encode([
        'code' => 1,
        'msg' => '오류: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
