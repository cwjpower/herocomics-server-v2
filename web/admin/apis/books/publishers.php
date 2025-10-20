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

// 출판사별 책 개수 포함
$sql = "SELECT 
    p.*,
    COUNT(DISTINCT b.book_id) as book_count
FROM publishers p
LEFT JOIN books b ON p.publisher_id = b.publisher_id AND b.deleted_at IS NULL
WHERE p.is_active = 1
GROUP BY p.publisher_id
ORDER BY p.publisher_name ASC";

$stmt = $pdo->query($sql);
$publishers = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'success' => true,
    'publishers' => $publishers
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
