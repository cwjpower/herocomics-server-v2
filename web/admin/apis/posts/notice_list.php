<?php
/*
 * Desc : API 담벼락 > 공지사항
 * 	method : GET
 */
require_once '../../wps-config.php';

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
	$msg = '책에 대한 정보가 필요합니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$user_id = $_GET['uid'];
$book_id = $_GET['book_id'];

$meta_key = 'lps_last_community_notice_' . $book_id;

// 마지막 접속 날짜 기록
wps_update_user_meta($user_id, $meta_key, date('Y-m-d H:i:s'));

// community notice
$LIST = lps_get_all_community_notice($book_id);

$json = compact( 'code', 'msg', 'LIST' );
echo json_encode( $json );

?>