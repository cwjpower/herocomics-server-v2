<?php
/*
 * 2016.08.22		softsyw
 * Desc : 책 등록(세트)
 */
require_once '../../../wps-config.php';
require_once FUNC_PATH . '/functions-book.php';

$code = 0;
$msg = '';

if ( !wps_is_agent() ) {
	$code = 510;
	$msg = '관리자만 사용할 수 있습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( !wps_is_publisher() ) {
	$code = 512;
	$msg = '출판사와 1인 작가만 사용하실 수 있습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['pkg_books']) ) {
	$code = 401;
	$msg = '단품 책을  선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}
if ( empty($_POST['book_title']) ) {
	$code = 401;
	$msg = '세트 이름을 입력해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}
if ( empty($_POST['author']) ) {
	$code = 402;
	$msg = '저자를 입력해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}
if ( empty($_POST['publisher']) ) {
	$code = 403;
	$msg = '출판사를 입력해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}
// if ( empty($_POST['isbn']) ) {
// 	$code = 404;
// 	$msg = 'ISBN을 입력해 주십시오.';
// 	$json = compact('code', 'msg');
// 	exit( json_encode($json) );
// }
if ( empty($_POST['normal_price']) ) {
	$code = 405;
	$msg = '정가를 입력해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}
if ( empty($_POST['sale_price']) ) {
	$code = 406;
	$msg = '판매가를 입력해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}
if ( empty($_POST['upload_type']) ) {
	$code = 407;
	$msg = '등록형태를 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}
if ( empty($_POST['introduction_book']) ) {
	$code = 408;
	$msg = '책 소개를 입력해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}
if ( empty($_POST['introduction_author']) ) {
	$code = 409;
	$msg = '저자 소개를 입력해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}
if ( empty($_POST['publisher_review']) ) {
	$code = 410;
	$msg = '출판사 서평을 입력해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}
if ( empty($_POST['category_first']) ) {
	$code = 412;
	$msg = '대분류를 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}
if ( empty($_POST['category_second']) ) {
	$code = 413;
	$msg = '중분류를 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}
if ( empty($_POST['category_third']) ) {
	$code = 414;
	$msg = '소분류를 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}
if ( empty($_POST['file_path_cover'][0]) ) {
	$code = 416;
	$msg = '대표 이미지를 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

// 정가 > 판매가
$normal_price = preg_replace('/\D/', '', $_POST['normal_price']);
$sale_price = preg_replace('/\D/', '', $_POST['sale_price']);

$result = lps_add_book();

$json = compact('code', 'msg', 'result');
echo json_encode( $json );

?>