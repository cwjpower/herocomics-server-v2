<?php
/*
 * Desc : API 읽을거에요 책 추가
 * 	method : POST
 */
require_once '../../wps-config.php';
require_once FUNC_PATH . '/functions-book.php';

$code = 0;
$msg = '';

if ( empty($_POST['uid']) ) {
	$code = 401;
	$msg = '회원의 UID가 필요합니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['book_id']) ) {
	$code = 402;
	$msg = '책 UID가 필요합니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$user_id = $_POST['uid'];
$book_id = $_POST['book_id'];

$result = lps_is_my_book( $user_id, $book_id );
if ( !empty($result) ) {
	$code = 405;
	$msg = '이미 구매하신 책입니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$result = lps_add_book_wishlist($book_id, $user_id);

if (empty($result)) {
	$code = 501;
	$msg = '읽을 책으로 추가하지 못했습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if (!strcmp($result, 'exists')) {
	$code = 502;
	$msg = '이미 존재하는 책입니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$json = compact( 'code', 'msg' );
echo json_encode( $json );

?>