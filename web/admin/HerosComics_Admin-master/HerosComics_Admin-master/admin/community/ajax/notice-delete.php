<?php
/*
 * 2016.10.26		softsyw
 * Desc : 선택한 담벼락 공지 삭제
 */
require_once '../../../wps-config.php';

$code = 0;
$msg = '';

if ( !wps_is_agent() ) {
	$code = 510;
	$msg = '관리자만 사용할 수 있습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['post_list']) ) {
	$code = 410;
	$msg = '공지사항을 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$count = 0;

foreach ($_POST['post_list'] as $key => $val) {
	if(wps_delete_post($val)) {
		$count++;
	}
}

$json = compact('code', 'msg', 'count');
echo json_encode( $json );
?>