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
$stock = $_POST['stock'] ?? 0;

// 유효성 검사
if (!$book_id || $stock < 0) {
    echo json_encode(['success' => false, 'message' => '잘못된 요청입니다.']);
    exit;
}

try {
    // 해당 책이 자신의 책인지 확인
    $check_sql = "SELECT book_id, book_status FROM bt_books WHERE book_id = ? AND publisher_id = ?";
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->execute([$book_id, $publisher_id]);
    $book = $check_stmt->fetch();
    
    if (!$book) {
        echo json_encode(['success' => false, 'message' => '권한이 없거나 존재하지 않는 책입니다.']);
        exit;
    }
    
    // 재고 업데이트
    $sql = "UPDATE bt_books SET book_stock = ?, updated_at = NOW() WHERE book_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$stock, $book_id]);
    
    // 재고가 0이면 자동으로 품절 처리
    $new_status = null;
    if ($stock == 0 && $book['book_status'] == 'active') {
        $status_sql = "UPDATE bt_books SET book_status = 'soldout' WHERE book_id = ?";
        $status_stmt = $pdo->prepare($status_sql);
        $status_stmt->execute([$book_id]);
        $new_status = 'soldout';
    } 
    // 재고가 생기면 자동으로 판매중으로
    elseif ($stock > 0 && $book['book_status'] == 'soldout') {
        $status_sql = "UPDATE bt_books SET book_status = 'active' WHERE book_id = ?";
        $status_stmt = $pdo->prepare($status_sql);
        $status_stmt->execute([$book_id]);
        $new_status = 'active';
    }
    
    echo json_encode([
        'success' => true, 
        'message' => '재고가 업데이트되었습니다.',
        'new_stock' => $stock,
        'new_status' => $new_status
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'DB 오류: ' . $e->getMessage()]);
}
?>
