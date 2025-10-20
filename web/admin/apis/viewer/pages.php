<?php
// ~/herocomics-server/web/admin/apis/viewer/pages.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// DB 연결
$conn = new mysqli('herocomics-mariadb', 'root', 'rootpass', 'herocomics');

if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]));
}

$conn->set_charset("utf8mb4");

// 파라미터
$book_id = isset($_GET['book_id']) ? intval($_GET['book_id']) : 1;
$episode = isset($_GET['episode']) ? intval($_GET['episode']) : 1;

// 실제 DB에서 데이터 가져오기
$query = "SELECT * FROM bt_books_pages 
          WHERE book_id = ? AND episode_num = ? 
          ORDER BY page_num ASC";

if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("ii", $book_id, $episode);
    $stmt->execute();
    $result = $stmt->get_result();

    $pages = [];
    while($row = $result->fetch_assoc()) {
        $pages[] = $row;
    }

    echo json_encode([
        'success' => true,
        'pages' => $pages,
        'total' => count($pages)
    ]);

    $stmt->close();
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Query failed',
        'message' => $conn->error
    ]);
}

$conn->close();
?>