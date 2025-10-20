<?php
/*
 * 2016.08.22		softsyw
 * Desc : 책 등록
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


//if ( empty($_POST['preview-img'][0]) ) {
//	$code = 415;
//	$msg = '미리보기 파일을 선택해 주십시오.';
//	$json = compact('code', 'msg');
//	exit( json_encode($json) );
//}



$result = lps_add_book_preview();

if (empty($result)) {
    $code = 503;
    $msg = '미리보기 파일을 등록하지 못했습니다.';
    $json = compact('code', 'msg');
    exit( json_encode($json) );
}

$json = compact('code', 'msg', 'result');
echo json_encode( $json );

