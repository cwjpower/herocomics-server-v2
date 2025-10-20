<?php
/*
 * 2016.11.03		softsyw
 * Desc : 선택한 담벼락 게시글 삭제
 */
require_once '../../../wps-config.php';
require_once FUNC_PATH . '/functions-activity.php';

$code = 0;
$msg = '';

if ( !wps_is_agent() ) {
	$code = 510;
	$msg = '관리자만 사용할 수 있습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['activity_list']) ) {
	$code = 410;
	$msg = '게시글을 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$count = 0;

foreach ($_POST['activity_list'] as $key => $val) {
	if(lps_delete_status_activity($val)) {
		$count++;
	}
}

$json = compact('code', 'msg', 'count');
echo json_encode( $json );
?>