<?php
/*
 * 2016.09.23		softsyw
 * Desc : 회원 META 정보 가져오기
 * 		serialized data 체크
 */
require_once '../../../wps-config.php';
require_once FUNC_PATH . '/functions-admin-logs.php';

$code = 0;
$msg = '';

if ( empty($_POST['user_id']) ) {
	$code = 410;
	$msg = '회원을 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$user_id = $_POST['user_id'];
$meta_key = empty($_POST['meta_key']) ? '' : $_POST['meta_key'];

$umeta = wps_get_user_meta($user_id, $meta_key);

$data = @unserialize($umeta);

$user_meta = $data !== false ? unserialize($umeta) : $umeta;

$json = compact('code', 'msg', 'user_meta');
echo json_encode( $json );

?>