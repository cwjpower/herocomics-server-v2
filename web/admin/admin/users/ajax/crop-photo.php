<?php
/*
 * 2016.09.01		softsyw
 * Desc : 프로필 사진 업데이트
 */
require_once '../../../wps-config.php';
require_once INC_PATH . '/classes/WpsImage.php';

$code = 0;
$msg = '';

// var_dump($_POST);

if ( !wps_is_agent() ) {
	$code = 510;
	$msg = '관리자만 사용할 수 있습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( !isset($_POST['xc']) ) {
	$code = 401;
	$msg = '이미지를 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}
if ( !isset($_POST['yc']) ) {
	$code = 402;
	$msg = '이미지를 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}
if ( !isset($_POST['wc']) ) {
	$code = 403;
	$msg = '이미지를 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}
if ( !isset($_POST['hc']) ) {
	$code = 404;
	$msg = '이미지를 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}
if ( empty($_POST['user_id']) ) {
	$code = 410;
	$msg = '회원을 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}
if ( empty($_POST['avatar_photo_path']) ) {
	$code = 412;
	$msg = '프로필 포토 파일을 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$source_img = $_POST['avatar_photo_path'];
$dir = 'user_avatar/' . date('Ym');	// 포토 디렉토리

$wps_img = new WpsImage($source_img);

$user_id = $_POST['user_id'];
$src_x = $_POST['xc'];
$src_y = $_POST['yc'];
$src_w = $_POST['wc'];
$src_h = $_POST['hc'];

$result = $wps_img->crop_image($src_x, $src_y, $src_w, $src_h, $dir);

if ($result) {
	wps_update_user_meta($user_id, 'wps_user_avatar', $result);
}

$json = compact('code', 'msg', 'result');
echo json_encode( $json );
?>