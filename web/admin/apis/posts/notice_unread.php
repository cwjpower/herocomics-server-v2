<?php
/*
 * Desc : API 읽지 않은 공지사항 갯수
 * 		내가 작성한 담벼락에 남긴 댓글 수, 안 읽은 댓글 개수.
 * 	method : GET
 */
require_once '../../wps-config.php';
require_once FUNC_PATH . '/functions-activity.php';

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

$last_notice_dt = wps_get_user_meta($user_id, $meta_key);

if (empty($last_notice_dt)) {
	$last_notice_dt = '2016-06-01 00:00:00';
}

$count = lps_get_unread_community_notice($book_id, $last_notice_dt);

$json = compact( 'code', 'msg', 'count' );
echo json_encode( $json );

?>