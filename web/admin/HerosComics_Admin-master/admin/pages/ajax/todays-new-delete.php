<?php
/*
 * 2016.10.11	softsyw
 * Desc : 오늘의 신간 삭제
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

if ( empty($_POST['book_id']) ) {
	$code = 410;
	$msg = '오늘의 신간에서 제외할 책을 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$book_id = $_POST['book_id'];

$option_name = 'lps_todays_new_book';
$lps_todays_new_book = wps_get_option($option_name);

$unserial = unserialize($lps_todays_new_book);

foreach ($unserial as $key => $val) {
	if (in_array($val, $book_id)) {
		unset($unserial[$key]);
	}
}
$option_value = serialize($unserial);

$result = wps_update_option_value( $option_name, $option_value );

if ( empty($result) ) {
	$code = 501;
	$msg = '삭제하지 못했습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$json = compact('code', 'msg', 'result');
echo json_encode( $json );

?>