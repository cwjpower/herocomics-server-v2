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

// 장르별 책 개수 포함
$sql = "SELECT 
    g.*,
    COUNT(DISTINCT bg.book_id) as book_count
FROM genres g
LEFT JOIN book_genres bg ON g.genre_id = bg.genre_id
LEFT JOIN books b ON bg.book_id = b.book_id AND b.deleted_at IS NULL
WHERE g.is_active = 1
GROUP BY g.genre_id
ORDER BY g.display_order ASC, g.genre_name ASC";

$stmt = $pdo->query($sql);
$genres = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'success' => true,
    'genres' => $genres
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
