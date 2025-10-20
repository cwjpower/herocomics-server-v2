<?php
/*
 * Desc : API 읽을거에요 책 삭제
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

$meta_key = 'lps_user_wishlist';

$wishlist = wps_get_user_meta($user_id, $meta_key);
$wishlist_unserial = unserialize($wishlist);

foreach ($wishlist_unserial as $key => $val) {
	if ($val == $book_id) {
		unset($wishlist_unserial[$key]);
	}
}
$serialized = serialize($wishlist_unserial);

$updated = wps_update_user_meta($user_id, $meta_key, $serialized);

if (empty($updated)) {
	$code = 501;
	$msg = '삭제하지 못했습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$json = compact( 'code', 'msg' );
echo json_encode( $json );

?>