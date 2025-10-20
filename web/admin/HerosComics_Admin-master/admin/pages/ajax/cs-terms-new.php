<?php
/*
 * 2016.10.13		softsyw
 * Desc : 이용약관/개인정보취급방침 등록
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

if ( empty($_POST['post_type']) ) {
	$code = 410;
	$msg = '글 종류를 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}
if ( empty($_POST['post_content']) ) {
	$code = 413;
	$msg = '내용을 입력해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$post_type = $_POST['post_type'];

$post_title = date('YmdHis') . '-by-' . wps_get_current_user_id();
$_POST['post_title'] = $post_title;

if (!strcmp($post_type, 'terms_of_use')) {
	$_POST['post_type_secondary'] = '이용약관';
} else if (!strcmp($post_type, 'terms_of_privacy')) {
	$_POST['post_type_secondary'] = '개인정보취급방침';
}

$post_id = wps_add_post();

if ( empty($post_id) ) {
	$code = 501;
	$msg = '등록하지 못했습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$json = compact('code', 'msg');
echo json_encode( $json );
?>