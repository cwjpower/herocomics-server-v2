<?php
// 데이터베이스 연결 설정 가져오기
require_once '../conf/db.php';

header('Content-Type: application/json; charset=utf-8');

try {
    // db.php에서 이미 $pdo 연결이 생성되어 있음
    
    // 1. 통계 데이터 가져오기
    $stats = getStats($pdo);
    
    // 2. 매출 추이 데이터 (최근 7일)
    $salesTrend = getSalesTrend($pdo);
    
    // 3. 최근 주문 목록
    $recentOrders = getRecentOrders($pdo);
    
    // 4. 베스트셀러 목록
    $bestsellers = getBestsellers($pdo);
    
    // 성공 응답
    echo json_encode([
        'success' => true,
        'stats' => $stats,
        'salesTrend' => $salesTrend,
        'recentOrders' => $recentOrders,
        'bestsellers' => $bestsellers
    ], JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    // 에러 응답
    echo json_encode([
        'success' => false,
        'message' => '데이터베이스 연결 실패: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

/**
 * 통계 데이터 가져오기
 */
function getStats($pdo) {
    // 총 책 권수
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM bt_books");
    $totalBooks = $stmt->fetch()['total'];
    
    // 판매 중인 책 (book_status = 1)
    $stmt = $pdo->query("SELECT COUNT(*) as active FROM bt_books WHERE book_status = 1");
    $activeBooks = $stmt->fetch()['active'];
    
    // 어제 대비 변화율 계산 (Mock 데이터)
    $booksChange = rand(-5, 15);
    $activeBooksChange = rand(-3, 10);
    
    // 오늘 주문 (주문 테이블이 없으므로 Mock 데이터)
    $todayOrders = rand(5, 30);
    $ordersChange = rand(-20, 50);
    
    // 이번 달 매출 (Mock 데이터)
    $monthSales = rand(5000000, 15000000);
    $salesChange = rand(-10, 30);
    
    return [
        'totalBooks' => (int)$totalBooks,
        'booksChange' => $booksChange,
        'activeBooks' => (int)$activeBooks,
        'activeBooksChange' => $activeBooksChange,
        'todayOrders' => $todayOrders,
        'ordersChange' => $ordersChange,
        'monthSales' => $monthSales,
        'salesChange' => $salesChange
    ];
}

/**
 * 최근 7일 매출 추이 가져오기
 */
function getSalesTrend($pdo) {
    // 주문 테이블이 없으므로 Mock 데이터 생성
    
    $labels = [];
    $values = [];
    
    // 최근 7일 날짜 생성
    for ($i = 6; $i >= 0; $i--) {
        $date = date('m/d', strtotime("-$i days"));
        $labels[] = $date;
        
        // 랜덤 매출 생성 (50만원 ~ 200만원)
        $values[] = rand(500000, 2000000);
    }
    
    return [
        'labels' => $labels,
        'values' => $values
    ];
}

/**
 * 최근 주문 목록 가져오기
 */
function getRecentOrders($pdo) {
    // 주문 테이블이 없으므로 Mock 데이터 생성
    
    // 실제 책 데이터 가져오기
    $stmt = $pdo->query("
        SELECT ID, book_title 
        FROM bt_books 
        WHERE book_status = 1
        ORDER BY ID DESC 
        LIMIT 5
    ");
    $books = $stmt->fetchAll();
    
    $orders = [];
    $statuses = ['pending', 'paid', 'shipping', 'completed'];
    
    foreach ($books as $index => $book) {
        $orders[] = [
            'order_id' => 1000 + $index,
            'book_title' => $book['book_title'],
            'order_date' => date('Y-m-d H:i', strtotime("-" . rand(0, 48) . " hours")),
            'status' => $statuses[array_rand($statuses)]
        ];
    }
    
    return $orders;
}

/**
 * 베스트셀러 목록 가져오기
 */
function getBestsellers($pdo) {
    // 주문 테이블이 없으므로 실제 책에 Mock 판매량 추가
    
    $stmt = $pdo->query("
        SELECT 
            ID,
            book_title,
            cover_img
        FROM bt_books 
        WHERE book_status = 1
        ORDER BY ID DESC
        LIMIT 5
    ");
    $books = $stmt->fetchAll();
    
    $bestsellers = [];
    foreach ($books as $book) {
        $bestsellers[] = [
            'book_id' => $book['ID'],
            'title' => $book['book_title'],
            'cover_image' => $book['cover_img'],
            'sales_count' => rand(50, 500)
        ];
    }
    
    // 판매량 기준 내림차순 정렬
    usort($bestsellers, function($a, $b) {
        return $b['sales_count'] - $a['sales_count'];
    });
    
    return $bestsellers;
}
?>
