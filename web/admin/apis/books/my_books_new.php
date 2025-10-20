<?php
/*
 * Desc : API 책 추가
 * 	method : POST
 */
require_once '../../wps-config.php';
require_once FUNC_PATH . '/functions-payment.php';
require_once FUNC_PATH . '/functions-book.php';
require_once FUNC_PATH . '/functions-coupon.php';
require_once INC_PATH . '/lib/pclzip.lib.php';

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
$book_ids = array($book_id);

if (!lps_is_free_book($book_id)) {
	$code = 502;
	$msg = '무료 책이 아닙니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if (lps_has_book_user($book_id, $user_id)) {
	$code = 503;
	$msg = '내 서재에 있는 책입니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

lps_add_book_cart( $book_ids, $user_id );
lps_add_book_pay( $book_ids, $user_id );

// 주문할 책이 있는 지 확인
$book_pay = wps_get_user_meta( $user_id, 'lps_user_book_pay' );
if (empty($book_pay)) {
	$code = 407;
	$msg = '주문할 책이 없습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

// 주문 완료
$result = lps_add_order( $user_id );

if (empty($result)) {
	$code = 501;
	$msg = '내 서재에 추가하지 못했습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$json = compact( 'code', 'msg' );
echo json_encode( $json );

?>