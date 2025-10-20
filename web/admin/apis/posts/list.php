<?php
/*
 * Desc: 뉴스/공지사항 목록 API
 * Method: GET
 */

$code = 0;
$msg = '';
$posts = [];

// 카테고리 필터
$category = isset($_GET['category']) ? $_GET['category'] : '';

// DB 연결
$conn = new mysqli('herocomics-mariadb', 'root', 'rootpass', 'herocomics');

if ($conn->connect_error) {
    $code = 500;
    $msg = 'DB 연결 실패';
} else {
    // SQL 쿼리
    if (!empty($category)) {
        $stmt = $conn->prepare("SELECT * FROM posts WHERE category = ? ORDER BY created_at DESC");
        $stmt->bind_param('s', $category);
    } else {
        $stmt = $conn->prepare("SELECT * FROM posts ORDER BY created_at DESC");
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $posts[] = [
            'post_id' => $row['post_id'],
            'title' => $row['title'],
            'content' => $row['content'],
            'category' => $row['category'],
            'thumbnail' => $row['thumbnail'],
            'view_count' => $row['view_count'],
            'created_at' => $row['created_at']
        ];
    }
    
    $stmt->close();
    $conn->close();
}

header('Content-Type: application/json');
echo json_encode(compact('code', 'msg', 'posts'));
?>
