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
$book_ids = $_POST['book_ids'] ?? [];
$action = $_POST['action'] ?? '';

// 유효성 검사
if (empty($book_ids) || !is_array($book_ids)) {
    echo json_encode(['success' => false, 'message' => '선택한 책이 없습니다.']);
    exit;
}

if (!in_array($action, ['status', 'delete', 'category'])) {
    echo json_encode(['success' => false, 'message' => '잘못된 작업입니다.']);
    exit;
}

try {
    // 선택한 책들이 모두 자신의 책인지 확인
    $placeholders = str_repeat('?,', count($book_ids) - 1) . '?';
    $check_sql = "SELECT COUNT(*) FROM bt_books WHERE book_id IN ($placeholders) AND publisher_id = ?";
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->execute([...$book_ids, $publisher_id]);
    $count = $check_stmt->fetchColumn();
    
    if ($count != count($book_ids)) {
        echo json_encode(['success' => false, 'message' => '권한이 없는 책이 포함되어 있습니다.']);
        exit;
    }
    
    // 작업 수행
    $pdo->beginTransaction();
    
    switch ($action) {
        case 'status':
            // 상태 일괄 변경
            $status = $_POST['status'] ?? '';
            if (!in_array($status, ['active', 'soldout', 'inactive'])) {
                throw new Exception('잘못된 상태입니다.');
            }
            
            $sql = "UPDATE bt_books SET book_status = ?, updated_at = NOW() WHERE book_id IN ($placeholders)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$status, ...$book_ids]);
            
            $message = count($book_ids) . '권의 책 상태가 변경되었습니다.';
            break;
            
        case 'delete':
            // 일괄 삭제 (실제로는 soft delete 권장)
            // 주문이 있는 책은 삭제하지 않고 판매중단 처리
            $order_check_sql = "SELECT DISTINCT book_id FROM bt_order WHERE book_id IN ($placeholders)";
            $order_check_stmt = $pdo->prepare($order_check_sql);
            $order_check_stmt->execute($book_ids);
            $books_with_orders = $order_check_stmt->fetchAll(PDO::FETCH_COLUMN);
            
            if (!empty($books_with_orders)) {
                // 주문이 있는 책은 판매중단
                $placeholders_orders = str_repeat('?,', count($books_with_orders) - 1) . '?';
                $inactive_sql = "UPDATE bt_books SET book_status = 'inactive', updated_at = NOW() WHERE book_id IN ($placeholders_orders)";
                $inactive_stmt = $pdo->prepare($inactive_sql);
                $inactive_stmt->execute($books_with_orders);
                
                // 주문이 없는 책만 삭제
                $books_to_delete = array_diff($book_ids, $books_with_orders);
                if (!empty($books_to_delete)) {
                    $placeholders_delete = str_repeat('?,', count($books_to_delete) - 1) . '?';
                    $delete_sql = "DELETE FROM bt_books WHERE book_id IN ($placeholders_delete)";
                    $delete_stmt = $pdo->prepare($delete_sql);
                    $delete_stmt->execute($books_to_delete);
                }
                
                $message = count($books_with_orders) . '권은 주문 이력이 있어 판매중단 처리되었고, ' . 
                          count($books_to_delete) . '권이 삭제되었습니다.';
            } else {
                // 모두 삭제
                $sql = "DELETE FROM bt_books WHERE book_id IN ($placeholders)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($book_ids);
                
                $message = count($book_ids) . '권의 책이 삭제되었습니다.';
            }
            break;
            
        case 'category':
            // 카테고리 일괄 변경
            $category = $_POST['category'] ?? '';
            if (empty($category)) {
                throw new Exception('카테고리를 선택해주세요.');
            }
            
            $sql = "UPDATE bt_books SET book_category = ?, updated_at = NOW() WHERE book_id IN ($placeholders)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$category, ...$book_ids]);
            
            $message = count($book_ids) . '권의 책 카테고리가 변경되었습니다.';
            break;
    }
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true, 
        'message' => $message,
        'affected_count' => count($book_ids)
    ]);
    
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'DB 오류: ' . $e->getMessage()]);
}
?>
