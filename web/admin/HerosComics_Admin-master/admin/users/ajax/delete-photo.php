<?php
/*
 * 2016.09.01		softsyw
 * Desc : 프로필 사진 삭제
 */
require_once '../../../wps-config.php';
require_once INC_PATH . '/classes/WpsImage.php';

$code = 0;
$msg = '';

if ( !wps_is_agent() ) {
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

$user_id = $_POST['user_id'];
$dir = 'user_avatar';	// 포토 디렉토리

$avatar = wps_get_user_meta($user_id, 'wps_user_avatar');

$filename = UPLOAD_PATH . '/' . $dir . '/' . basename($avatar);
@unlink($filename);

wps_update_user_meta($user_id, 'wps_user_avatar', "");

$altimg = IMG_URL . '/common/photo-default.png';

$json = compact('code', 'msg', 'altimg');
echo json_encode( $json );
?>