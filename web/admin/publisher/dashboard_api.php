<?php
// 데이터베이스 연결 설정 가져오기
require_once '../conf/db.php';

header('Content-Type: application/json; charset=utf-8');

// 출판사 ID (세션에서 가져오기)
session_start();
$publisher_id = isset($_SESSION['publisher_id']) ? $_SESSION['publisher_id'] : 1;

try {
    // 1. 통계 데이터 가져오기
    $stats = getStats($pdo, $publisher_id);
    
    // 2. 매출 추이 데이터 (최근 7일)
    $salesTrend = getSalesTrend($pdo, $publisher_id);
    
    // 3. 최근 주문 목록
    $recentOrders = getRecentOrders($pdo, $publisher_id);
    
    // 4. 베스트셀러 목록
    $bestsellers = getBestsellers($pdo, $publisher_id);
    
    // 5. 시리즈 그룹 (출판사별)
    $seriesGroups = getSeriesGroups($pdo, $publisher_id);
    
    // 성공 응답
    echo json_encode([
        'success' => true,
        'stats' => $stats,
        'salesTrend' => $salesTrend,
        'recentOrders' => $recentOrders,
        'bestsellers' => $bestsellers,
        'seriesGroups' => $seriesGroups
    ], JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => '데이터베이스 연결 실패: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

function getStats($pdo, $publisher_id) {
    // 총 책 권수
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM bt_books WHERE publisher_id = ?");
    $stmt->execute([$publisher_id]);
    $totalBooks = $stmt->fetch()['total'];
    
    // 판매 중인 책
    $stmt = $pdo->prepare("SELECT COUNT(*) as active FROM bt_books WHERE book_status = 1 AND publisher_id = ?");
    $stmt->execute([$publisher_id]);
    $activeBooks = $stmt->fetch()['active'];
    
    // 오늘 주문 (실제 데이터)
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as today_orders
        FROM bt_order o
        INNER JOIN bt_order_item i ON o.order_id = i.order_id
        INNER JOIN bt_books b ON i.book_id = b.ID
        WHERE b.publisher_id = ? AND DATE(o.created_dt) = CURDATE()
    ");
    $stmt->execute([$publisher_id]);
    $todayOrders = $stmt->fetch()['today_orders'] ?: 0;
    
    // 이번 달 매출 (실제 데이터)
    $stmt = $pdo->prepare("
        SELECT COALESCE(SUM(o.total_paid), 0) as month_sales
        FROM bt_order o
        INNER JOIN bt_order_item i ON o.order_id = i.order_id
        INNER JOIN bt_books b ON i.book_id = b.ID
        WHERE b.publisher_id = ? 
        AND YEAR(o.created_dt) = YEAR(CURDATE())
        AND MONTH(o.created_dt) = MONTH(CURDATE())
    ");
    $stmt->execute([$publisher_id]);
    $monthSales = (int)$stmt->fetch()['month_sales'];
    
    return [
        'totalBooks' => (int)$totalBooks,
        'booksChange' => 0,  // 변화율 계산은 나중에
        'activeBooks' => (int)$activeBooks,
        'activeBooksChange' => 0,
        'todayOrders' => (int)$todayOrders,
        'ordersChange' => 0,
        'monthSales' => $monthSales,
        'salesChange' => 0
    ];
}

function getSalesTrend($pdo, $publisher_id) {
    $labels = [];
    $values = [];
    
    // 최근 7일 실제 매출
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $labels[] = date('m/d', strtotime("-$i days"));
        
        $stmt = $pdo->prepare("
            SELECT COALESCE(SUM(o.total_paid), 0) as daily_sales
            FROM bt_order o
            INNER JOIN bt_order_item i ON o.order_id = i.order_id
            INNER JOIN bt_books b ON i.book_id = b.ID
            WHERE b.publisher_id = ? AND DATE(o.created_dt) = ?
        ");
        $stmt->execute([$publisher_id, $date]);
        $values[] = (int)$stmt->fetch()['daily_sales'];
    }
    
    return [
        'labels' => $labels,
        'values' => $values
    ];
}

function getRecentOrders($pdo, $publisher_id) {
    $stmt = $pdo->prepare("
        SELECT 
            o.order_id,
            i.book_title,
            o.created_dt as order_date,
            o.order_status
        FROM bt_order o
        INNER JOIN bt_order_item i ON o.order_id = i.order_id
        INNER JOIN bt_books b ON i.book_id = b.ID
        WHERE b.publisher_id = ?
        ORDER BY o.created_dt DESC
        LIMIT 5
    ");
    $stmt->execute([$publisher_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $status_map = [0 => 'pending', 1 => 'paid', 2 => 'cancelled', 3 => 'refunded'];
    
    foreach ($orders as &$order) {
        $order['status'] = $status_map[$order['order_status']] ?? 'unknown';
    }
    
    return $orders;
}

function getBestsellers($pdo, $publisher_id) {
    $stmt = $pdo->prepare("
        SELECT 
            i.book_id,
            i.book_title,
            COUNT(*) as sales_count,
            SUM(i.sale_price) as total_sales
        FROM bt_order_item i
        INNER JOIN bt_books b ON i.book_id = b.ID
        WHERE b.publisher_id = ?
        GROUP BY i.book_id, i.book_title
        ORDER BY sales_count DESC
        LIMIT 5
    ");
    $stmt->execute([$publisher_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getSeriesGroups($pdo, $publisher_id) {
    $stmt = $pdo->prepare("
        SELECT 
            series_name,
            COUNT(*) as volume_count,
            MIN(series_volume) as start_volume,
            MAX(series_volume) as end_volume
        FROM bt_books
        WHERE publisher_id = ? AND series_name IS NOT NULL
        GROUP BY series_name
        ORDER BY series_name
    ");
    $stmt->execute([$publisher_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>