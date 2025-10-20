<?php
/*
 * Desc : API 특정 책을 구입한 독자들
 * 	method : GET
 */
require_once '../../wps-config.php';
require_once FUNC_PATH . '/functions-book.php';
require_once FUNC_PATH . '/functions-friend.php';

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

$LIST = lps_get_user_lists_by_book( $book_id, $user_id );

$json = compact( 'code', 'msg', 'LIST' );
echo json_encode( $json );

?>