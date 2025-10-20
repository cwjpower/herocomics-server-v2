<?php
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

// DB 연결
$host = 'herocomics-mariadb';
$user = 'root';
$pass = 'rootpass';
$db = 'herocomics';

$conn = mysqli_connect($host, $user, $pass, $db);
mysqli_set_charset($conn, "utf8");

if (!$conn) {
    die(json_encode(['error' => 'DB 연결 실패']));
}

// 파라미터 받기
$category = isset($_GET['category']) ? $_GET['category'] : '';
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 20;
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
$search = isset($_GET['search']) ? $_GET['search'] : '';

// 기본 쿼리
$query = "SELECT 
    ID as id,
    book_title as title,
    author,
    publisher,
    normal_price,
    discount_rate,
    sale_price as price,
    cover_img as cover_image,
    isbn,
    is_free,
    created_dt
FROM bt_books 
WHERE book_status = 1";

// 카테고리 필터
if ($category) {
    if ($category == 'marvel') {
        $query .= " AND publisher LIKE '%Marvel%'";
    } else if ($category == 'dc') {
        $query .= " AND publisher LIKE '%DC%'";
    }
}

// 검색 필터
if ($search) {
    $search = mysqli_real_escape_string($conn, $search);
    $query .= " AND (book_title LIKE '%$search%' OR author LIKE '%$search%')";
}

// 정렬 및 페이징
$query .= " ORDER BY ID DESC LIMIT $limit OFFSET $offset";

// 실행
$result = mysqli_query($conn, $query);

if (!$result) {
    die(json_encode(['error' => mysqli_error($conn)]));
}

$books = [];
while($row = mysqli_fetch_assoc($result)) {
    // 할인율 계산
    $discount_percent = 0;
    if ($row['normal_price'] > 0 && $row['price'] < $row['normal_price']) {
        $discount_percent = round((($row['normal_price'] - $row['price']) / $row['normal_price']) * 100);
    }
    
    $books[] = [
        'id' => intval($row['id']),
        'title' => $row['title'],
        'author' => $row['author'],
        'publisher' => $row['publisher'],
        'price' => intval($row['price']),
        'original_price' => intval($row['normal_price']),
        'discount_percent' => $discount_percent,
        'cover_image' => $row['cover_image'] ?: 'https://via.placeholder.com/150x200',
        'is_free' => $row['is_free'] == 'Y',
        'created_at' => $row['created_dt']
    ];
}

// 전체 개수 가져오기
$count_query = "SELECT COUNT(*) as total FROM bt_books WHERE book_status = 1";
if ($category) {
    if ($category == 'marvel') {
        $count_query .= " AND publisher LIKE '%Marvel%'";
    } else if ($category == 'dc') {
        $count_query .= " AND publisher LIKE '%DC%'";
    }
}
if ($search) {
    $count_query .= " AND (book_title LIKE '%$search%' OR author LIKE '%$search%')";
}

$count_result = mysqli_query($conn, $count_query);
$total_count = mysqli_fetch_assoc($count_result)['total'];

// 결과 반환
$response = [
    'success' => true,
    'books' => $books,
    'total' => intval($total_count),
    'limit' => $limit,
    'offset' => $offset
];

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

mysqli_close($conn);
?>
