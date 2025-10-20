<?php
/*
 * 2017.01.06		softsyw
 * Desc : 찜목록
 */
require_once '../../../wps-config.php';
require_once FUNC_PATH . '/functions-admin-logs.php';
require_once FUNC_PATH . '/functions-book.php';

$code = 0;
$msg = '';

if ( empty($_POST['user_id']) ) {
	$code = 410;
	$msg = '회원을 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$user_id = $_POST['user_id'];
$page = empty($_POST['page']) ? 1 : $_POST['page'];
$row_per_page = 1000;	// 한 페이지 당 출력할 데이터 갯수, pagination 처리 불가하니 모든 리스트 

// Fetch user wish data
$wish_array = lps_get_user_wish_by_page($user_id, $page, $row_per_page);
$wish_rows = $wish_array['wish_rows'];
$wish_total_count = $wish_array['total_row'];

$body = '';

if (!empty($wish_rows)) {
	foreach ($wish_rows as $key => $val) {
		$list_no = $wish_total_count - $key;
		$book_rows = lps_get_book($val);
		$book_id = $book_rows['ID'];
		$book_title = $book_rows['book_title'];
		// 								$cover_img = $book_rows['cover_img'];
		$author = $book_rows['author'];
		$publisher = $book_rows['publisher'];
		
		$body .= '<tr>
					<td>' . $list_no . '</td>
					<td>' . $book_title . '</td>
					<td>' . $author . '</td>
					<td>' . $publisher . '</td>
				</tr>';
	}
}

// Pagination
// $foot = lps_get_post_pagination_link($user_id, $page, $row_per_page);


$json = compact('code', 'msg', 'body', 'foot');
echo json_encode( $json );

?>