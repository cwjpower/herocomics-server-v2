<?php
/*
 * Desc : API 친구 즐겨찾기 추가 -> is_favorite 1
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

if ( empty($_POST['favorite_uid']) ) {
	$code = 402;
	$msg = '즐겨찾기할 회원의 UID가 필요합니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$user_id = $_POST['uid'];
$favorite_uid = $_POST['favorite_uid'];

$result = lps_add_favorite_friend($user_id, $favorite_uid);

if ( empty($result) ) {
	$code = 501;
	$msg = '즐겨찾기에 추가하지 못했습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$json = compact( 'code', 'msg' );
echo json_encode( $json );

?>