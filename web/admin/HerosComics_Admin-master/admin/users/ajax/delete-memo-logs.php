<?php
/*
 * 2016.08.06		softsyw
 * Desc : 메모 삭제
 */
require_once '../../../wps-config.php';
require_once FUNC_PATH . '/functions-admin-logs.php';

$code = 0;
$msg = '';

if ( !wps_is_admin() ) {
	$code = 510;
	$msg = '관리자만 사용할 수 있습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['user_id']) ) {
	$code = 410;
	$msg = '회원을 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['memo_id']) ) {
	$code = 411;
	$msg = '메모를 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$user_id = $_POST['user_id'];
$memo_id = $_POST['memo_id'];
$affected_rows = lps_delete_memo_logs($user_id, $memo_id);

$json = compact('code', 'msg', 'affected_rows');
echo json_encode( $json );
?>