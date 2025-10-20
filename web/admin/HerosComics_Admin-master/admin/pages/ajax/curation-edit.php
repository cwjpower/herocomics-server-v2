<?php
require_once '../../../wps-config.php';
require_once FUNC_PATH . '/functions-page.php';

$code = 0;
$msg = '';

if ( !wps_is_admin() ) {
	$code = 510;
	$msg = '관리자만 사용할 수 있습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['curation_id']) ) {
	$code = 422;
	$msg = '큐레이션을  선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['pkg_books']) ) {
	$code = 401;
	$msg = '큐레이션에 추가할 책을  선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['curation_title']) ) {
	$code = 410;
	$msg = '큐레이팅 제목을 입력해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['file_path']) ) {
	$code = 412;
	$msg = '표지 이미지를 등록해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$result = lps_edit_curation();

if ( empty($result) ) {
	$code = 501;
	$msg = '등록하지 못했습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$json = compact('code', 'msg', 'result');
echo json_encode( $json );

?>