<?php
/*
 * Desc : API 담벼락 > 댓글 등록
 * 	method : POST
 */
require_once '../../wps-config.php';
require_once FUNC_PATH . '/functions-activity.php';

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
	$code = 400;
	$msg = '로그인 후 이용해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['post_id']) ) {
	$code = 402;
	$msg = '책을 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['comment']) ) {
	$code = 403;
	$msg = '댓글을 입력해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$activity_id = $_POST['post_id'];
$user_id = $_POST['uid'];

$user_rows = wps_get_user($user_id);
$user_name = $user_rows['display_name'];
$user_level = $user_rows['user_level'];

$_POST['activity_id'] = $activity_id;
$_POST['user_id'] = $user_id;
$_POST['user_level'] = $user_level;
$_POST['comment_author'] = $user_name;



// 게시글 등록
$comment_id = lps_add_activity_comment();

if ( empty($comment_id) ) {
	$code = 501;
	$msg = '등록하지 못했습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$json = compact( 'code', 'msg', 'comment_id' );
echo json_encode( $json );

?>