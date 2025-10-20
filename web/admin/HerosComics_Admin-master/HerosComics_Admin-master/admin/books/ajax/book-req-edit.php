<?php
/*
 * 2016.08.25		softsyw
 * Desc : 책 수정 요청 > 출판사
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

if ( !wps_is_publisher() ) {
	$code = 512;
	$msg = '출판사와 1인 작가만 사용하실 수 있습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$is_pkg = $_POST['is_pkg'];

if ( empty($_POST['book_id']) ) {
	$code = 400;
	$msg = '책을 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}
if (!strcmp($is_pkg, 'Y')) {
	if ( empty($_POST['pkg_books']) ) {
		$code = 421;
		$msg = '세트에 추가할 책을 선택해 주십시오.';
		$json = compact('code', 'msg');
		exit( json_encode($json) );
	}
}
if ( empty($_POST['book_title']) ) {
	$code = 401;
	$msg = strcmp($is_pkg, 'Y') ? '책 제목을 입력해 주십시오.' : '세트 이름을 입력해 주십시오';
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
if (!strcmp($is_pkg, 'N')) {
	if ( empty($_POST['isbn']) ) {
		$code = 404;
		$msg = 'ISBN을 입력해 주십시오.';
		$json = compact('code', 'msg');
		exit( json_encode($json) );
	}
}

if (empty($_POST['is_free'])) {		// 유료, 무료는 'Y'
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

if (!strcmp($is_pkg, 'N')) {
	if ( empty($_POST['book_title']) ) {
		$code = 411;
		$msg = '목차를 입력해 주십시오.';
		$json = compact('code', 'msg');
		exit( json_encode($json) );
	}
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
if (!strcmp($is_pkg, 'N')) {
	if ( empty($_POST['file_path_epub'][0]) ) {
		$code = 415;
		$msg = 'EPUB 파일을 선택해 주십시오.';
		$json = compact('code', 'msg');
		exit( json_encode($json) );
	}
}
if ( empty($_POST['file_path_cover'][0]) ) {
	$code = 416;
	$msg = strcmp($is_pkg, 'Y') ? '표지이미지 파일을 선택해 주십시오.' : '대표이미지 파일을 선택해 주십시오';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}
if ( empty($_POST['req_reason']) ) {
	$code = 421;
	$msg = '변경 요청에 대한 사유를 입력해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$result = lps_req_edit_book();

$json = compact('code', 'msg', 'result');
echo json_encode( $json );

?>