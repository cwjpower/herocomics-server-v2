<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// DB 연결
$host = 'herocomics-mariadb';
$dbname = 'herocomics';
$username = 'root';
$password = 'rootpass';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// 검색 파라미터
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$title = isset($_GET['title']) ? trim($_GET['title']) : '';
$author_id = isset($_GET['author_id']) ? intval($_GET['author_id']) : 0;
$publisher_id = isset($_GET['publisher_id']) ? intval($_GET['publisher_id']) : 0;
$genre_id = isset($_GET['genre_id']) ? intval($_GET['genre_id']) : 0;
$series_id = isset($_GET['series_id']) ? intval($_GET['series_id']) : 0;
$price_min = isset($_GET['price_min']) ? intval($_GET['price_min']) : 0;
$price_max = isset($_GET['price_max']) ? intval($_GET['price_max']) : 0;
$rating_min = isset($_GET['rating_min']) ? floatval($_GET['rating_min']) : 0;
$is_new = isset($_GET['is_new']) ? intval($_GET['is_new']) : -1;
$is_bestseller = isset($_GET['is_bestseller']) ? intval($_GET['is_bestseller']) : -1;
$is_free = isset($_GET['is_free']) ? intval($_GET['is_free']) : -1;

// 정렬
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'latest';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = isset($_GET['limit']) ? min(100, max(1, intval($_GET['limit']))) : 20;
$offset = ($page - 1) * $limit;

// SQL 쿼리 구성
$sql = "SELECT DISTINCT
    b.book_id,
    b.title,
    b.subtitle,
    b.description,
    b.price,
    b.original_price,
    b.discount_rate,
    b.rating,
    b.review_count,
    b.cover_image,
    b.page_count,
    b.publish_date,
    b.is_new,
    b.is_bestseller,
    b.is_recommended,
    b.is_free,
    b.stock_count,
    b.age_rating,
    p.publisher_name,
    p.publisher_code,
    s.series_name,
    b.volume_number,
    GROUP_CONCAT(DISTINCT a.author_name ORDER BY ba.display_order SEPARATOR ', ') as authors,
    GROUP_CONCAT(DISTINCT g.genre_name ORDER BY bg.is_main DESC SEPARATOR ', ') as genres
FROM books b
LEFT JOIN publishers p ON b.publisher_id = p.publisher_id
LEFT JOIN series s ON b.series_id = s.series_id
LEFT JOIN book_authors ba ON b.book_id = ba.book_id
LEFT JOIN authors a ON ba.author_id = a.author_id
LEFT JOIN book_genres bg ON b.book_id = bg.book_id
LEFT JOIN genres g ON bg.genre_id = g.genre_id
WHERE b.deleted_at IS NULL";

$params = [];

// 통합 키워드 검색 (제목 + 작가명)
if (!empty($keyword)) {
    $sql .= " AND (b.title LIKE :keyword OR a.author_name LIKE :keyword2)";
    $params[':keyword'] = "%$keyword%";
    $params[':keyword2'] = "%$keyword%";
}

// 제목 검색
if (!empty($title)) {
    $sql .= " AND b.title LIKE :title";
    $params[':title'] = "%$title%";
}

// 작가 검색
if ($author_id > 0) {
    $sql .= " AND ba.author_id = :author_id";
    $params[':author_id'] = $author_id;
}

// 출판사 검색
if ($publisher_id > 0) {
    $sql .= " AND b.publisher_id = :publisher_id";
    $params[':publisher_id'] = $publisher_id;
}

// 장르 검색
if ($genre_id > 0) {
    $sql .= " AND bg.genre_id = :genre_id";
    $params[':genre_id'] = $genre_id;
}

// 시리즈 검색
if ($series_id > 0) {
    $sql .= " AND b.series_id = :series_id";
    $params[':series_id'] = $series_id;
}

// 가격 범위
if ($price_min > 0) {
    $sql .= " AND b.price >= :price_min";
    $params[':price_min'] = $price_min;
}
if ($price_max > 0) {
    $sql .= " AND b.price <= :price_max";
    $params[':price_max'] = $price_max;
}

// 평점
if ($rating_min > 0) {
    $sql .= " AND b.rating >= :rating_min";
    $params[':rating_min'] = $rating_min;
}

// 상태 필터
if ($is_new >= 0) {
    $sql .= " AND b.is_new = :is_new";
    $params[':is_new'] = $is_new;
}
if ($is_bestseller >= 0) {
    $sql .= " AND b.is_bestseller = :is_bestseller";
    $params[':is_bestseller'] = $is_bestseller;
}
if ($is_free >= 0) {
    $sql .= " AND b.is_free = :is_free";
    $params[':is_free'] = $is_free;
}

// GROUP BY
$sql .= " GROUP BY b.book_id";

// 정렬
switch($sort) {
    case 'popular':
        $sql .= " ORDER BY b.purchase_count DESC, b.rating DESC";
        break;
    case 'rating':
        $sql .= " ORDER BY b.rating DESC, b.review_count DESC";
        break;
    case 'price_asc':
        $sql .= " ORDER BY b.price ASC";
        break;
    case 'price_desc':
        $sql .= " ORDER BY b.price DESC";
        break;
    case 'title':
        $sql .= " ORDER BY b.title ASC";
        break;
    case 'latest':
    default:
        $sql .= " ORDER BY b.created_at DESC";
        break;
}

// 전체 개수 조회
$count_sql = "SELECT COUNT(DISTINCT b.book_id) as total FROM books b
LEFT JOIN book_authors ba ON b.book_id = ba.book_id
LEFT JOIN authors a ON ba.author_id = a.author_id
LEFT JOIN book_genres bg ON b.book_id = bg.book_id
WHERE b.deleted_at IS NULL";

// 같은 WHERE 조건 추가
if (!empty($keyword)) {
    $count_sql .= " AND (b.title LIKE :keyword OR a.author_name LIKE :keyword2)";
}
if (!empty($title)) {
    $count_sql .= " AND b.title LIKE :title";
}
if ($author_id > 0) {
    $count_sql .= " AND ba.author_id = :author_id";
}
if ($publisher_id > 0) {
    $count_sql .= " AND b.publisher_id = :publisher_id";
}
if ($genre_id > 0) {
    $count_sql .= " AND bg.genre_id = :genre_id";
}
if ($series_id > 0) {
    $count_sql .= " AND b.series_id = :series_id";
}
if ($price_min > 0) {
    $count_sql .= " AND b.price >= :price_min";
}
if ($price_max > 0) {
    $count_sql .= " AND b.price <= :price_max";
}
if ($rating_min > 0) {
    $count_sql .= " AND b.rating >= :rating_min";
}
if ($is_new >= 0) {
    $count_sql .= " AND b.is_new = :is_new";
}
if ($is_bestseller >= 0) {
    $count_sql .= " AND b.is_bestseller = :is_bestseller";
}
if ($is_free >= 0) {
    $count_sql .= " AND b.is_free = :is_free";
}

// 전체 개수
$count_stmt = $pdo->prepare($count_sql);
foreach ($params as $key => $value) {
    $count_stmt->bindValue($key, $value);
}
$count_stmt->execute();
$total = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];

// 페이징
$sql .= " LIMIT :limit OFFSET :offset";

// 실행
$stmt = $pdo->prepare($sql);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 응답
echo json_encode([
    'success' => true,
    'total' => intval($total),
    'page' => $page,
    'limit' => $limit,
    'total_pages' => ceil($total / $limit),
    'books' => $books
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
