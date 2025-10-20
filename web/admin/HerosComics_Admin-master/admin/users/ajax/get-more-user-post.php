<?php
/*
 * 2016.12.27		softsyw
 * Desc : 메모 전체보기
 */
require_once '../../../wps-config.php';
require_once FUNC_PATH . '/functions-admin-logs.php';

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
$row_per_page = 15;	// 한 페이지 당 출력할 데이터 갯수

// Fetch post data
$post_array = lps_get_user_post_by_page($user_id, $page, $row_per_page);
$post_rows = $post_array['post_rows'];
$post_total_count = $post_array['total_row']['total_count'];

$body = '';

if (!empty($post_rows)) {
	foreach ($post_rows as $key => $val) {
		$list_no = $post_total_count - $key;
		$activity_id = $val['id'];
		$book_title = $val['book_title'];
		$post_title = htmlspecialchars($val['subject']);
		$post_date = $val['created_dt'];
		$post_date_label = substr($post_date, 0, 10);
		$count_like = $val['count_like'];
		
		$body .= '<tr>
					<td>' . $list_no . '</td>
					<td><a href="' . ADMIN_URL . '/community/activity_view.php?aid=' . $activity_id . '">' . $book_title . '</a></td>
					<td>' . $post_date_label . '</td>
					<td>' . $post_title . '</td>
					<td>' . number_format($count_like) . '</td>
				</tr>';
	}
}

// Pagination
$foot = lps_get_post_pagination_link($user_id, $page, $row_per_page);


$json = compact('code', 'msg', 'body', 'foot');
echo json_encode( $json );

?>