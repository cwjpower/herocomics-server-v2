<?php
/*
 * 2016.09.22		softsyw
 * Desc : 책 ISBN 중복 체크
 */
require_once '../../../wps-config.php';
require_once FUNC_PATH . '/functions-book.php';

$code = 0;
$msg = '';

if ( !wps_is_agent() ) {
	$code = 510;
	$msg = '사용권한이 없습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['isbn']) ) {
	$code = 404;
	$msg = 'ISBN을 입력해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}


$isbn = preg_replace('/\D/', '', $_POST['isbn']);

$ID = lps_find_isbn($isbn);

if (!empty($ID)) {
	$code = 501;
	$msg = '이미 사용중인 ISBN입니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$json = compact('code', 'msg');
echo json_encode( $json );

?>