<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// DB 연결
$conn = new mysqli('herocomics-mariadb', 'root', 'rootpass', 'herocomics');

if ($conn->connect_error) {
    die(json_encode(['error' => 'DB Connection failed: ' . $conn->connect_error]));
}

// 간단한 쿼리
$result = $conn->query("SELECT * FROM books LIMIT 5");

$books = [];
while ($row = $result->fetch_assoc()) {
    $books[] = $row;
}

echo json_encode([
    'code' => 0,
    'data' => $books
]);

$conn->close();
?>
