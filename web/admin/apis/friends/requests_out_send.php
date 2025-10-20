<?php
/*
 * Desc : API 친구 신청을 보낸다. 친구 요청
 * 	method : POST
 */
require_once '../../wps-config.php';
require_once FUNC_PATH . '/functions-friend.php';

$code = 0;
$msg = '';

// Logging
// $log_post = date('Y.m.d H:i:s') . ' POST : ' . print_r($_POST, true);
// lps_error_log( $log_post, UPLOAD_PATH . '/tmp/api_log.txt', 1);

// function lps_error_log ($message, $filename = null, $message_type = null) {
// 	file_put_contents($filename, $message, FILE_APPEND | LOCK_EX);
// }
// Logging

if ( empty($_POST['uid']) ) {
	$code = 401;
	$msg = '친구 요청할 회원의 UID가 필요합니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['friend_uid']) ) {
	$code = 402;
	$msg = '요청받을 친구의 UID가 필요합니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$user_id = $_POST['uid'];
$friend_uid = $_POST['friend_uid'];

$user_rows = wps_get_user($friend_uid);

if ( empty($user_rows) ) {
	$code = 503;
	$msg = '회원이 존재하지 않습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$ID = lps_is_friend_status($user_id, $friend_uid);

if ( !empty($ID) ) {
	$code = 502;
	$msg = '친구 신청 내역이 있습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

// 친구 요청 등록
$result = lps_request_friend( $user_id, $friend_uid );

if ( empty($result) ) {
	$code = 501;
	$msg = '신청하지 못했습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$json = compact( 'code', 'msg' );
echo json_encode( $json );

?>