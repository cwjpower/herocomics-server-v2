<?php
/*
 * Desc : API SNS 연결관리 업데이트
 * 	method : POST
 */
require_once '../../wps-config.php';

$code = 0;
$msg = '';

if ( empty($_POST['uid']) ) {
	$code = 401;
	$msg = '회원의 UID가 필요합니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['on_off']) ) {
	$code = 411;
	$msg = 'ON / OFF 처리 방식을 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$user_id = $_POST['uid'];

$meta_key = 'wps_user_sns_facebook';
$on_off = !strcmp($_POST['on_off'], 'on') ? 'on' : 'off';

wps_update_user_meta( $user_id, $meta_key, $on_off );

$json = compact( 'code', 'msg', 'on_off' );
echo json_encode( $json );

?>