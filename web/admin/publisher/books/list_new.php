<?php
$page_title = '책 관리 - HeroComics 출판사 CMS';
require_once '../layout/header.php';
require_once '../../conf/db.php';

$publisher_id = $_SESSION['publisher_id'] ?? 1;

// 필터 값 받기
$status = $_GET['status'] ?? 'all';
$category = $_GET['category'] ?? 'all';
$genre = $_GET['genre'] ?? 'all';
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'created_desc';

// WHERE 조건 생성
$where = ["b.publisher_id = ?"];
$params = [$publisher_id];

if ($status != 'all') {
    $where[] = "b.book_status = ?";
    $params[] = $status;
}

if ($category != 'all') {
    $where[] = "b.comics_brand = ?";
    $params[] = $category;
}

if ($genre != 'all') {
    $where[] = "EXISTS (SELECT 1 FROM bt_book_genres bg2 WHERE bg2.book_id = b.ID AND bg2.genre_id = ?)";
    $params[] = intval($genre);
}

if ($search) {
    $where[] = "(b.book_title LIKE ? OR b.author LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$where_sql = implode(' AND ', $where);

// 정렬 조건
$order_by = match($sort) {
    'created_desc' => 'b.created_dt DESC',
    'created_asc' => 'b.created_dt ASC',
    'price_desc' => 'b.sale_price DESC',
    'price_asc' => 'b.sale_price ASC',
    default => 'b.created_dt DESC'
};

// 페이지네이션
$page = $_GET['page'] ?? 1;
$per_page = $_GET['per_page'] ?? 20;
$offset = ($page - 1) * $per_page;

// 전체 개수
$count_sql = "SELECT COUNT(DISTINCT b.ID) FROM bt_books b 
              LEFT JOIN bt_book_genres bg ON b.ID = bg.book_id 
              WHERE $where_sql";
$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute($params);
$total_books = $count_stmt->fetchColumn();
$total_pages = ceil($total_books / $per_page);

// 책 목록 조회 (장르 포함)
$sql = "SELECT 
    b.ID as book_id,
    b.book_title,
    b.author,
    b.publisher,
    b.sale_price,
    b.cover_img,
    b.book_status,
    b.comics_brand,
    b.created_dt,
    b.isbn,
    GROUP_CONCAT(g.genre_name ORDER BY g.genre_order SEPARATOR ', ') as genre_names
FROM bt_books b
LEFT JOIN bt_book_genres bg ON b.ID = bg.book_id
LEFT JOIN bt_genres g ON bg.genre_id = g.genre_id
WHERE $where_sql
GROUP BY b.ID
ORDER BY $order_by
LIMIT ? OFFSET ?";

$params[] = (int)$per_page;
$params[] = (int)$offset;

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$books = $stmt->fetchAll();

// 통계 데이터
$stats_sql = "SELECT 
    COUNT(*) as total_books,
    SUM(CASE WHEN book_status = 1 THEN 1 ELSE 0 END) as active_books,
    SUM(CASE WHEN book_status = 2 THEN 1 ELSE 0 END) as soldout_books,
    SUM(CASE WHEN book_status = 0 THEN 1 ELSE 0 END) as inactive_books
FROM bt_books 
WHERE publisher_id = ?";

$stats_stmt = $pdo->prepare($stats_sql);
$stats_stmt->execute([$publisher_id]);
$stats = $stats_stmt->fetch();
?>
