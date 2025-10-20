<?php
/*
 * 2016.12.21		softsyw
 * Desc : 1:1 문의 답변 등록
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

if ( empty($_POST['post_id']) ) {
	$code = 410;
	$msg = '문의를 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}
if ( empty($_POST['post_title']) ) {
	$code = 411;
	$msg = '제목을 입력해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}
if ( empty($_POST['post_content']) ) {
	$code = 412;
	$msg = '내용을 입력해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$post_id = wps_add_post_answer();

if ( empty($post_id) ) {
	$code = 501;
	$msg = '등록하지 못했습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$json = compact('code', 'msg');
echo json_encode( $json );

?>