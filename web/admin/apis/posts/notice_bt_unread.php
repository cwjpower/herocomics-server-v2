<?php
/*
 * Desc : API 읽지 않은 공지사항 갯수 : 전체 공지
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

$user_id = $_GET['uid'];

$meta_key = 'lps_last_notice_access_dt';

$last_notice_dt = wps_get_user_meta($user_id, $meta_key);

if (empty($last_notice_dt)) {
	$last_notice_dt = '2016-06-01 00:00:00';
}

$count = lps_get_unread_notice($last_notice_dt);

wps_update_user_meta($user_id, $meta_key, date('Y-m-d H:i:s'));

$json = compact( 'code', 'msg', 'count' );
echo json_encode( $json );

?>