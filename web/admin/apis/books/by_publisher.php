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

// 파라미터
$publisher_id = isset($_GET['publisher_id']) ? intval($_GET['publisher_id']) : 0;
$publisher_code = isset($_GET['publisher_code']) ? trim($_GET['publisher_code']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'latest';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = isset($_GET['limit']) ? min(100, max(1, intval($_GET['limit']))) : 20;
$offset = ($page - 1) * $limit;

if ($publisher_id === 0 && empty($publisher_code)) {
    echo json_encode(['error' => 'publisher_id or publisher_code is required']);
    exit;
}

// SQL 쿼리
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
    b.stock_count,
    p.publisher_name,
    p.publisher_code,
    s.series_name,
    b.volume_number,
    GROUP_CONCAT(DISTINCT a.author_name ORDER BY ba.display_order SEPARATOR ', ') as authors,
    GROUP_CONCAT(DISTINCT g.genre_name ORDER BY bg.is_main DESC SEPARATOR ', ') as genres
FROM books b
INNER JOIN publishers p ON b.publisher_id = p.publisher_id
LEFT JOIN series s ON b.series_id = s.series_id
LEFT JOIN book_authors ba ON b.book_id = ba.book_id
LEFT JOIN authors a ON ba.author_id = a.author_id
LEFT JOIN book_genres bg ON b.book_id = bg.book_id
LEFT JOIN genres g ON bg.genre_id = g.genre_id
WHERE b.deleted_at IS NULL";

$params = [];

if ($publisher_id > 0) {
    $sql .= " AND b.publisher_id = :publisher_id";
    $params[':publisher_id'] = $publisher_id;
} else if (!empty($publisher_code)) {
    $sql .= " AND p.publisher_code = :publisher_code";
    $params[':publisher_code'] = $publisher_code;
}

$sql .= " GROUP BY b.book_id";

// 정렬
switch($sort) {
    case 'popular':
        $sql .= " ORDER BY b.purchase_count DESC, b.rating DESC";
        break;
    case 'rating':
        $sql .= " ORDER BY b.rating DESC";
        break;
    case 'price_asc':
        $sql .= " ORDER BY b.price ASC";
        break;
    case 'price_desc':
        $sql .= " ORDER BY b.price DESC";
        break;
    case 'latest':
    default:
        $sql .= " ORDER BY b.created_at DESC";
        break;
}

// 전체 개수
$count_sql = "SELECT COUNT(DISTINCT b.book_id) as total 
FROM books b
INNER JOIN publishers p ON b.publisher_id = p.publisher_id
WHERE b.deleted_at IS NULL";

if ($publisher_id > 0) {
    $count_sql .= " AND b.publisher_id = :publisher_id";
} else if (!empty($publisher_code)) {
    $count_sql .= " AND p.publisher_code = :publisher_code";
}

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

// 출판사 정보
$pub_sql = "SELECT * FROM publishers WHERE ";
if ($publisher_id > 0) {
    $pub_sql .= "publisher_id = :publisher_id";
} else {
    $pub_sql .= "publisher_code = :publisher_code";
}

$pub_stmt = $pdo->prepare($pub_sql);
foreach ($params as $key => $value) {
    $pub_stmt->bindValue($key, $value);
}
$pub_stmt->execute();
$publisher = $pub_stmt->fetch(PDO::FETCH_ASSOC);

// 응답
echo json_encode([
    'success' => true,
    'publisher' => $publisher,
    'total' => intval($total),
    'page' => $page,
    'limit' => $limit,
    'total_pages' => ceil($total / $limit),
    'books' => $books
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
