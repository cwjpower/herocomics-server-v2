<?php
session_start();
require_once '../../conf/db.php';

header('Content-Type: application/json');

// 로그인 체크
if (!isset($_SESSION['user_id']) || $_SESSION['user_level'] != 9) {
    echo json_encode(['success' => false, 'message' => '권한이 없습니다.']);
    exit;
}

$publisher_id = $_SESSION['publisher_id'] ?? 1;

// POST 데이터 받기
$book_id = $_POST['book_id'] ?? 0;
$status = $_POST['status'] ?? '';

// 유효성 검사
if (!$book_id || !in_array($status, ['active', 'soldout', 'inactive'])) {
    echo json_encode(['success' => false, 'message' => '잘못된 요청입니다.']);
    exit;
}

try {
    // 해당 책이 자신의 책인지 확인
    $check_sql = "SELECT book_id FROM bt_books WHERE book_id = ? AND publisher_id = ?";
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->execute([$book_id, $publisher_id]);
    
    if (!$check_stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => '권한이 없거나 존재하지 않는 책입니다.']);
        exit;
    }
    
    // 상태 업데이트
    $sql = "UPDATE bt_books SET book_status = ?, updated_at = NOW() WHERE book_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$status, $book_id]);
    
    echo json_encode([
        'success' => true, 
        'message' => '상태가 변경되었습니다.',
        'new_status' => $status
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'DB 오류: ' . $e->getMessage()]);
}
?>
