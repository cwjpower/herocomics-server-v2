<?php
/*
 * 2016.10.13		softsyw
 * Desc : 출판사 검색
 */
require_once '../../../wps-config.php';
require_once FUNC_PATH . '/functions-user.php';

$code = 0;
$msg = '';

if ( empty($_POST['q']) ) {
	$code = 410;
	$msg = '출판사명을  입력해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$str = $_POST['q'];
$user_level = 7;

$publisher = lps_search_user( $str, $user_level );
$result = '';

if (!empty($publisher)) {
	foreach ($publisher as $key => $val) {
		$uid = $val['ID'];
		$user_name = $val['user_name'];
		$display_name = $val['display_name'];
		$result .= '<option value="' . $uid . '">' . $user_name . '(' . $display_name . ')' . '</option>';
	}
}

$json = compact('code', 'msg', 'result');
echo json_encode( $json );

?>