<?php
/*
 * Desc : API 친구 요청 거절 -> 삭제
 * 	 initiator_user_id가 타인,  friend_user_id 본인  
 * 	method : POST
 */
require_once '../../wps-config.php';
require_once FUNC_PATH . '/functions-friend.php';

$code = 0;
$msg = '';

if ( empty($_POST['uid']) ) {
	$code = 401;
	$msg = '회원의 UID가 필요합니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['friend_uid']) ) {
	$code = 402;
	$msg = '친구 신청을 취소할 회원의 UID가 필요합니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$user_id = $_POST['uid'];
$friend_uid = $_POST['friend_uid'];

$result = lps_delete_friends($friend_uid, $user_id);

if ( empty($result) ) {
	$code = 501;
	$msg = '삭제하지 못했습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$json = compact( 'code', 'msg' );
echo json_encode( $json );

?>