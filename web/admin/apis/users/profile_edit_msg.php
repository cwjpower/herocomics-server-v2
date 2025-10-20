<?php
/*
 * Desc : 프로필 메시지 저장
 */
require_once '../../wps-config.php';

$code = 0;
$msg = '';

if ( empty($_POST['uid']) ) {
	$code = 401;
	$msg = '회원 아이디가 필요합니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['profile_msg']) ) {
	$code = 402;
	$msg = '프로필 메시지가 필요합니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$uid = $_POST['uid'];
$profile_msg = $_POST['profile_msg'];

$result = wps_update_user_meta( $uid, 'wps_user_profile_msg', $profile_msg );

if ( empty($result) ) {
	$code = 502;
	$msg = '프로필 메시지를 변경하지 못했습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$json = compact('code', 'msg');
echo json_encode( $json );
?>