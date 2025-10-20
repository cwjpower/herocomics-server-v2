<?php
/*
 * 2016.09.07		softsyw
 * Desc : 공지사항 삭제
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

$result = wps_delete_post($post_id);

$json = compact('code', 'msg', 'result');
echo json_encode( $json );

?>