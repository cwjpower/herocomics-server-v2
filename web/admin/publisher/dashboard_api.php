<?php
// 데이터베이스 연결 설정 가져오기
require_once '../conf/db.php';

header('Content-Type: application/json; charset=utf-8');

try {
    // 1. 통계 데이터 가져오기
    $stats = getStats($pdo);
    
    // 2. 매출 추이 데이터 (최근 7일)
    $salesTrend = getSalesTrend($pdo);
    
    // 3. 최근 주문 목록
    $recentOrders = getRecentOrders($pdo);
    
    // 4. 베스트셀러 목록
    $bestsellers = getBestsellers($pdo);
    
    // 5. 시리즈 그룹 (출판사별)
    $seriesGroups = getSeriesGroups($pdo);
    
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

function getStats($pdo) {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM bt_books");
    $totalBooks = $stmt->fetch()['total'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as active FROM bt_books WHERE book_status = 1");
    $activeBooks = $stmt->fetch()['active'];
    
    return [
        'totalBooks' => (int)$totalBooks,
        'booksChange' => rand(-5, 15),
        'activeBooks' => (int)$activeBooks,
        'activeBooksChange' => rand(-3, 10),
        'todayOrders' => rand(5, 30),
        'ordersChange' => rand(-20, 50),
        'monthSales' => rand(5000000, 15000000),
        'salesChange' => rand(-10, 30)
    ];
}

function getSalesTrend($pdo) {
    $labels = [];
    $values = [];
    
    for ($i = 6; $i >= 0; $i--) {
        $labels[] = date('m/d', strtotime("-$i days"));
        $values[] = rand(500000, 2000000);
    }
    
    return [
        'labels' => $labels,
        'values' => $values
    ];
}

function getRecentOrders($pdo) {
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

function getBestsellers($pdo) {
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
    
    usort($bestsellers, function($a, $b) {
        return $b['sales_count'] - $a['sales_count'];
    });
    
    return $bestsellers;
}

function getSeriesGroups($pdo) {
    $stmt = $pdo->query("
        SELECT 
            series_name,
            GROUP_CONCAT(book_title ORDER BY ID SEPARATOR '|||') as book_titles,
            GROUP_CONCAT(cover_img ORDER BY ID SEPARATOR '|||') as cover_images,
            GROUP_CONCAT(ID ORDER BY ID SEPARATOR '|||') as book_ids,
            COUNT(*) as book_count
        FROM bt_books
        WHERE series_name IS NOT NULL 
          AND series_name != ''
          AND book_status = 1
        GROUP BY series_name
        ORDER BY series_name
    ");
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $seriesGroups = [];
    foreach ($results as $row) {
        $titles = explode('|||', $row['book_titles']);
        $images = explode('|||', $row['cover_images']);
        $ids = explode('|||', $row['book_ids']);
        
        $books = [];
        for ($i = 0; $i < count($titles); $i++) {
            $books[] = [
                'id' => $ids[$i],
                'title' => $titles[$i],
                'cover_image' => $images[$i]
            ];
        }
        
        $seriesGroups[] = [
            'series_name' => $row['series_name'],
            'book_count' => (int)$row['book_count'],
            'books' => $books
        ];
    }
    
    return $seriesGroups;
}
?>
