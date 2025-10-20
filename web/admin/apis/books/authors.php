<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$host = 'herocomics-mariadb';
$dbname = 'herocomics';
$username = 'root';
$password = 'rootpass';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// 작가별 책 개수 포함
$sql = "SELECT 
    a.*,
    COUNT(DISTINCT ba.book_id) as book_count
FROM authors a
LEFT JOIN book_authors ba ON a.author_id = ba.author_id
LEFT JOIN books b ON ba.book_id = b.book_id AND b.deleted_at IS NULL
WHERE a.is_active = 1
GROUP BY a.author_id
ORDER BY a.author_name ASC";

$stmt = $pdo->query($sql);
$authors = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'success' => true,
    'authors' => $authors
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
