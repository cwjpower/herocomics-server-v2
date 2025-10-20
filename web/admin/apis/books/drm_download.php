<?php
/*
 * Desc : 개인 DRM 책 다운로드 URL
 * 	method : GET
 * 	사용하지 않음.
 */
require_once '../../wps-config.php';
require_once FUNC_PATH . '/functions-book.php';
require_once FUNC_PATH . '/functions-payment.php';
require_once INC_PATH . '/lib/pclzip.lib.php';

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

if ( empty($_GET['device_id']) ) {
	$code = 403;
	$msg = '디바이스 아이디가 필요합니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$user_id = $_GET['uid'];
$book_id = $_GET['book_id'];
$device_id = $_GET['device_id'];

$drm_url = lps_api_drm($user_id, $book_id, $device_id);

if ( empty($drm_url) ) {
	$code = 505;
	$msg = 'DRM URL을 생성할 수 없습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$json = compact( 'code', 'msg', 'drm_url' );
echo json_encode( $json );

?>