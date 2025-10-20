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
$author_id = isset($_GET['author_id']) ? intval($_GET['author_id']) : 0;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'latest';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = isset($_GET['limit']) ? min(100, max(1, intval($_GET['limit']))) : 20;
$offset = ($page - 1) * $limit;

if ($author_id === 0) {
    echo json_encode(['error' => 'author_id is required']);
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
INNER JOIN book_authors ba ON b.book_id = ba.book_id
INNER JOIN authors a ON ba.author_id = a.author_id
LEFT JOIN publishers p ON b.publisher_id = p.publisher_id
LEFT JOIN series s ON b.series_id = s.series_id
LEFT JOIN book_authors ba2 ON b.book_id = ba2.book_id
LEFT JOIN authors a2 ON ba2.author_id = a2.author_id
LEFT JOIN book_genres bg ON b.book_id = bg.book_id
LEFT JOIN genres g ON bg.genre_id = g.genre_id
WHERE b.deleted_at IS NULL
AND ba.author_id = :author_id
GROUP BY b.book_id";

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
INNER JOIN book_authors ba ON b.book_id = ba.book_id
WHERE b.deleted_at IS NULL AND ba.author_id = :author_id";

$count_stmt = $pdo->prepare($count_sql);
$count_stmt->bindValue(':author_id', $author_id, PDO::PARAM_INT);
$count_stmt->execute();
$total = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];

// 페이징
$sql .= " LIMIT :limit OFFSET :offset";

// 실행
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':author_id', $author_id, PDO::PARAM_INT);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 작가 정보
$author_sql = "SELECT * FROM authors WHERE author_id = :author_id";
$author_stmt = $pdo->prepare($author_sql);
$author_stmt->bindValue(':author_id', $author_id, PDO::PARAM_INT);
$author_stmt->execute();
$author = $author_stmt->fetch(PDO::FETCH_ASSOC);

// 응답
echo json_encode([
    'success' => true,
    'author' => $author,
    'total' => intval($total),
    'page' => $page,
    'limit' => $limit,
    'total_pages' => ceil($total / $limit),
    'books' => $books
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
