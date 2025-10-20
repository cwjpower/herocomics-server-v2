<?php
/*
 * 2016.10.11	softsyw
 * Desc : 승인된 모든 책 검색, 큐레이팅/오늘의 신간 등의 메뉴에서 사용함.
 * 
 */
require_once '../../../wps-config.php';
require_once FUNC_PATH . '/functions-book.php';

$code = 0;
$msg = '';

if ( empty($_POST['q']) ) {
	$code = 410;
	$msg = '검색할 제목을  입력해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$str = $_POST['q'];

$book = lps_search_approved_books($str);
$result = '';

if (!empty($book)) {
	foreach ($book as $key => $val) {
		$bid = $val['ID'];
		$btitle = $val['book_title'];
		$author = $val['author'];
		$result .= '<option value="' . $bid . '">' . $btitle . '(' . $author . ')' . '</option>';
	}
}

$json = compact('code', 'msg', 'result');
echo json_encode( $json );

?>