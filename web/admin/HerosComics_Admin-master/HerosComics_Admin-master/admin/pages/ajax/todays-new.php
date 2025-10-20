<?php
/*
 * 2016.10.11	softsyw
 * Desc : 오늘의 신간 등록
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

if ( empty($_POST['today_books']) ) {
	$code = 410;
	$msg = '오늘의 신간을 입력해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$today_books = $_POST['today_books'];
$option_name = 'lps_todays_new_book';
$option_value = serialize($today_books);

$lps_todays_new_book = wps_get_option($option_name);

if (!empty($lps_todays_new_book)) {
	$unserial = unserialize($lps_todays_new_book);
	
	if (!empty($unserial)) {
		foreach ($today_books as $key => $val) {
			array_unshift($unserial, $val);
// 			array_push($unserial, $today_books);	append			
		}
		$option_value = serialize($unserial);
	} else {
		$option_value = serialize($today_books);
	}
	
}

$result = wps_update_option_value( $option_name, $option_value );

if ( empty($result) ) {
	$code = 501;
	$msg = '등록하지 못했습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$json = compact('code', 'msg', 'result');
echo json_encode( $json );

?>