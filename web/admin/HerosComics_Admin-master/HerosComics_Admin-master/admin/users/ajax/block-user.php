<?php
/*
 * 2016.07.30		softsyw
 * Desc : 회원 차단
 */
require_once '../../../wps-config.php';

$code = 0;
$msg = '';

if ( !wps_is_admin() ) {
	$code = 510;
	$msg = '관리자만 사용할 수 있습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['userID']) ) {
	$code = 410;
	$msg = '차단할 회원을 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['reason']) ) {
	$code = 411;
	$msg = '사유를 입력해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$ID = $_POST['userID'];
$reason = $_POST['reason'];
$user_status = '1';

$admin_id = wps_get_current_user_id();
$block_date = date('Y-m-d H:i:s');

if ( !wps_update_user_status( $ID, $user_status ) ) {
	$code = 501;
	$msg = '처리하지 못했습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$serialized = serialize(compact('reason', 'admin_id', 'block_date'));

wps_update_user_meta($ID, 'wps_user_block_log', $serialized);

$json = compact('code', 'msg', 'reason');
echo json_encode( $json );
?>