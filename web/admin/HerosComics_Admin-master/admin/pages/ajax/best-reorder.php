<?php
/*
 * 2016.10.13	softsyw
 * Desc : 베스트(랭킹) 정렬순서 변경
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
	$msg = '정렬할 책을 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$today_books = $_POST['book_id'];
$option_name = $_POST['option_name'];

$option_value = serialize($today_books);

$result = wps_update_option_value( $option_name, $option_value );

if ( empty($result) ) {
	$code = 501;
	$msg = '정렬순서를 변경하지 못했습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$json = compact('code', 'msg', 'result');
echo json_encode( $json );

?>