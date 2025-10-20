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
$genre_id = isset($_GET['genre_id']) ? intval($_GET['genre_id']) : 0;
$genre_code = isset($_GET['genre_code']) ? trim($_GET['genre_code']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'latest';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = isset($_GET['limit']) ? min(100, max(1, intval($_GET['limit']))) : 20;
$offset = ($page - 1) * $limit;

if ($genre_id === 0 && empty($genre_code)) {
    echo json_encode(['error' => 'genre_id or genre_code is required']);
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
    GROUP_CONCAT(DISTINCT g2.genre_name ORDER BY bg2.is_main DESC SEPARATOR ', ') as genres
FROM books b
INNER JOIN book_genres bg ON b.book_id = bg.book_id
INNER JOIN genres g ON bg.genre_id = g.genre_id
LEFT JOIN publishers p ON b.publisher_id = p.publisher_id
LEFT JOIN series s ON b.series_id = s.series_id
LEFT JOIN book_authors ba ON b.book_id = ba.book_id
LEFT JOIN authors a ON ba.author_id = a.author_id
LEFT JOIN book_genres bg2 ON b.book_id = bg2.book_id
LEFT JOIN genres g2 ON bg2.genre_id = g2.genre_id
WHERE b.deleted_at IS NULL";

$params = [];

if ($genre_id > 0) {
    $sql .= " AND bg.genre_id = :genre_id";
    $params[':genre_id'] = $genre_id;
} else if (!empty($genre_code)) {
    $sql .= " AND g.genre_code = :genre_code";
    $params[':genre_code'] = $genre_code;
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
INNER JOIN book_genres bg ON b.book_id = bg.book_id
INNER JOIN genres g ON bg.genre_id = g.genre_id
WHERE b.deleted_at IS NULL";

if ($genre_id > 0) {
    $count_sql .= " AND bg.genre_id = :genre_id";
} else if (!empty($genre_code)) {
    $count_sql .= " AND g.genre_code = :genre_code";
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

// 장르 정보
$genre_sql = "SELECT * FROM genres WHERE ";
if ($genre_id > 0) {
    $genre_sql .= "genre_id = :genre_id";
} else {
    $genre_sql .= "genre_code = :genre_code";
}

$genre_stmt = $pdo->prepare($genre_sql);
foreach ($params as $key => $value) {
    $genre_stmt->bindValue($key, $value);
}
$genre_stmt->execute();
$genre = $genre_stmt->fetch(PDO::FETCH_ASSOC);

// 응답
echo json_encode([
    'success' => true,
    'genre' => $genre,
    'total' => intval($total),
    'page' => $page,
    'limit' => $limit,
    'total_pages' => ceil($total / $limit),
    'books' => $books
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
