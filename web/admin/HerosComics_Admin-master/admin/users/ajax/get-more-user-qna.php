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

// Fetch qna data
$qna_array = lps_get_user_qna_by_page($user_id, $page, $row_per_page);
$qna_rows = $qna_array['qna_rows'];
$qna_total_count = $qna_array['total_row']['total_count'];

$body = '';

if (!empty($qna_rows)) {
	foreach ($qna_rows as $key => $val) {
		$list_no = $qna_total_count - (($page - 1) * $row_per_page) - $key;
		
		$post_title = htmlspecialchars($val['post_title']);
		$post_date = $val['post_date'];
		$post_date_label = substr($post_date, 0, 10);
		$post_status = $val['post_status'];
		$post_ans_user_id = $val['post_ans_user_id'];
		$post_ans_date = $val['post_ans_date'];
		
		$answer_row = wps_get_user($post_ans_user_id);
		$post_ans_user_name = @$answer_row['user_name'];
		
		$list_no = $qna_total_count - $key;
		
		if ( !strcmp($post_status, 'waiting') ) {
			$reply_icon = '대기중';
		} else {
			$reply_icon = '답변완료';
		}
		
		$body .= '<tr>
					<td>' . $list_no . '</td>
					<td>' . $post_date_label . '</td>
					<td>' . $post_title . '</td>
					<td>' . $reply_icon . '</td>
					<td>' . $post_ans_user_name . '</td>
					<td>' . $post_ans_date . '</td>
				</tr>';
	}
}

// Pagination
$foot = lps_get_qna_pagination_link($user_id, $page, $row_per_page);


$json = compact('code', 'msg', 'body', 'foot');
echo json_encode( $json );

?>