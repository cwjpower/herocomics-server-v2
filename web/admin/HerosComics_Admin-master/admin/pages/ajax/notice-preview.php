<?php
/*
 * 2016.09.06		softsyw
 * Desc : 메모 전체보기
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

if ( empty($_POST['id']) ) {
	$code = 410;
	$msg = '게시글을 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$post_id = $_POST['id'];

$posts = wps_get_post($post_id);
$posts_meta = wps_get_post_meta($post_id, 'wps-post-attachment');
if (!empty($posts_meta)) {
	$posts_meta = unserialize($posts_meta);
	$attachment = $posts_meta[0]['file_name'];
}

$json = compact('code', 'msg', 'posts', 'attachment');
echo json_encode( $json );

?>