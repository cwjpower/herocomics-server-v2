<?php
/*
 * 2016.08.17		softsyw
 * Desc : 책 검색
 */
require_once '../../../wps-config.php';
require_once FUNC_PATH . '/functions-book.php';

$code = 0;
$msg = '';

// if ( empty($_POST['q']) ) {
// 	$code = 410;
// 	$msg = '책 제목을  입력해 주십시오.';
// 	$json = compact('code', 'msg');
// 	exit( json_encode($json) );
// }

$user_id = wps_get_current_user_id();
$str = $_POST['q'];

$book = lps_search_books_by_user($user_id, $str);
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