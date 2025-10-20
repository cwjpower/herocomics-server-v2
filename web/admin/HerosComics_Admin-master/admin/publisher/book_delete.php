<?php
require_once '../../wps-config.php';

if (!isset($_SESSION['login']['userid']) || $_SESSION['login']['user_level'] != 7) {
    header('Location: /admin/login.php');
    exit;
}

$publisher_id = $_SESSION['login']['publisher_id'];
$book_id = $_GET['id'] ?? 0;

if ($book_id <= 0) {
    die('잘못된 접근입니다.');
}

global $wdb;

// 본인 책인지 확인
$check_sql = "SELECT ID, epub_path, cover_img FROM bt_books WHERE ID = ? AND publisher_id = ?";
$stmt = $wdb->prepare($check_sql);
$stmt->bind_param("ii", $book_id, $publisher_id);
$stmt->execute();
$result = $stmt->get_result();
$book = $result->fetch_assoc();
$stmt->close();

if (!$book) {
    die('❌ 삭제 권한이 없거나 존재하지 않는 책입니다.');
}

// 파일 삭제
if ($book['epub_path'] && file_exists('/var/www/html' . $book['epub_path'])) {
    unlink('/var/www/html' . $book['epub_path']);
}
if ($book['cover_img'] && file_exists('/var/www/html' . $book['cover_img'])) {
    unlink('/var/www/html' . $book['cover_img']);
}

// DB에서 삭제
$delete_sql = "DELETE FROM bt_books WHERE ID = ? AND publisher_id = ?";
$stmt = $wdb->prepare($delete_sql);
$stmt->bind_param("ii", $book_id, $publisher_id);

if ($stmt->execute()) {
    $stmt->close();
    header('Location: book_list.php?msg=deleted');
    exit;
} else {
    $stmt->close();
    die('❌ 삭제 실패: ' . $wdb->error);
}
?>
