<?php
/*
 * Desc : API 공지사항 삭제
 * 	method : POST
 */
require_once '../../wps-config.php';
require_once FUNC_PATH . '/functions-activity.php';

$code = 0;
$msg = '';

if ( empty($_POST['uid']) ) {
	$code = 400;
	$msg = '로그인 후 이용해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['post_id']) ) {
	$code = 402;
	$msg = '공지사항을 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$notice_id =$_POST['post_id'];
$user_id = $_POST['uid'];

$post_user_id = wps_get_post_field( $notice_id, 'post_user_id' );

if ( $post_user_id != $user_id ) {
	$code = 403;
	$msg = '삭제할 권한이 없습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

// 공지사항 삭제
$result = wps_delete_post( $notice_id );

if ( empty($result) ) {
	$code = 501;
	$msg = '삭제하지 못했습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$json = compact('code', 'msg');
echo json_encode( $json );
?>