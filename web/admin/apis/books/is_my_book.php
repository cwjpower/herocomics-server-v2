<?php
/*
 * Desc : API 회원이 책을 구매했는지 여부
 * 	method : GET
 */
require_once '../../wps-config.php';
require_once FUNC_PATH . '/functions-book.php';

$code = 0;
$msg = '';

if ( empty($_GET['uid']) ) {
	$code = 401;
	$msg = '회원의 UID가 필요합니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_GET['book_id']) ) {
	$code = 402;
	$msg = '책 UID가 필요합니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$user_id = $_GET['uid'];
$book_id = $_GET['book_id'];

$result = lps_is_my_book( $user_id, $book_id );
$is_my_book = empty($result['item_id']) ? 'N' : 'Y';
$epub_url = empty($result['epub_url']) ? '' : $result['epub_url'];

// epub url
// $meta = lps_get_book_meta($book_id, 'lps_book_epub_file');
// $meta = unserialize($meta);
// $epub_url = @$meta['file_url'];

$json = compact( 'code', 'msg', 'is_my_book', 'epub_url' );
echo json_encode( $json );

?>