<?php
/*
 * Desc : API 최근 읽은 책
 * 	method : GET
 */
require_once '../../wps-config.php';
require_once FUNC_PATH . '/functions-book.php';
require_once FUNC_PATH . '/functions-payment.php';

$code = 0;
$msg = '';

if ( empty($_GET['uid']) ) {
	$code = 401;
	$msg = '회원의 UID가 필요합니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$user_id = $_GET['uid'];

$read_row = lps_get_last_read_book($user_id);

if (!empty($read_row)) {
	$book_id = $read_row['book_id'];
	$page_read = intval($read_row['read_page_to']);
	$epub_index = $read_row['epub_index'];
}

if ( empty($book_id) ) {
	$code = 501;
	$msg = '읽은 책이 존재하지 않습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$book_row = lps_get_book($book_id);

$book_title = $book_row['book_title'];
$author = $book_row['author'];
$cover_img = $book_row['cover_img'];

$meta = lps_get_book_meta($book_id);

$total_page = intval($meta['lps_book_total_page']);
$epub_file = unserialize($meta['lps_book_epub_file']);
$epub_url = lps_get_order_epub_url( $user_id, $book_id );
$epub_name = $epub_file['file_name'];
$chat_url = @$meta['lps_sendbird_chat_url'];

$json = compact( 'code', 'msg', 'book_id', 'book_title', 'author', 'cover_img', 'total_page', 'page_read', 'epub_url', 'epub_name', 'chat_url', 'epub_index' );
echo json_encode( $json );

?>