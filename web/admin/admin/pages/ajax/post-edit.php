<?php
/*
 * 2016.09.07		softsyw
 * Desc : 공지사항 편집
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
if ( empty($_POST['ID']) ) {
	$code = 401;
	$msg = '게시글을 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}
if ( empty($_POST['post_type']) ) {
	$code = 410;
	$msg = '글 종류를 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}
if ( empty($_POST['post_title']) ) {
	$code = 411;
	$msg = '제목을 입력해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}
if ( empty($_POST['post_type_area']) ) {
	$code = 412;
	$msg = '등록범위를 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}
if ( empty($_POST['post_content']) ) {
	$code = 413;
	$msg = '내용을 입력해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

/*
 * Desc : serializeObject() bug ?
 * 	checkbox가 중복 (post_type_area[])
 */
$_POST['post_type_area'] = array_unique($_POST['post_type_area']);

$result = wps_update_post();

if ( empty($result) ) {
	$code = 501;
	$msg = '저장하지 못했습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$json = compact('code', 'msg', 'user_id', 'result');
echo json_encode( $json );
?>