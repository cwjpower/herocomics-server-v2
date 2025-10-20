<?php
/*
 * 2016.09.07		softsyw
 * Desc : wps_postmeta 파일 삭제
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

if ( empty($_POST['pid']) ) {
	$code = 410;
	$msg = '파일을 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}
if ( !isset($_POST['key']) ) {
	$code = 411;
	$msg = '파일을 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$post_id = $_POST['pid'];
$key = $_POST['key'];

$attachment = unserialize(wps_get_post_meta( $post_id, 'wps-post-attachment' ));

$file_path = $attachment[$key]['file_path'];
$file_name = $attachment[$key]['file_name'];
// $file_size = $attachment[$key]['file_size'];

@unlink($file_path);

wps_update_post_meta($post_id, 'wps-post-attachment', '');

$json = compact('code', 'msg', 'file_name');
echo json_encode( $json );
?>