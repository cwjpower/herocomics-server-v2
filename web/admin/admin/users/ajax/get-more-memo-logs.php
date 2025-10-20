<?php
/*
 * 2016.08.04		softsyw
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

// Fetch memo data
$memo_array = lps_get_memo_logs_by_page($user_id, $page, $row_per_page);
$memo_rows = $memo_array['memo_rows'];
$memo_total_count = $memo_array['total_row']['total_count'];

$body = '';

if (!empty($memo_rows)) {
	foreach ($memo_rows as $key => $val) {
		$list_no = $memo_total_count - (($page - 1) * $row_per_page) - $key;
		$ID = $val['ID'];
		$memo = $val['memo'];
		$created_by = $val['created_by'];
		$created_dt = $val['created_dt'];
		
		$body .= '<tr>
					<td><input type="checkbox" name="memo_id[]" value="'. $ID . '"></td>
					<td>' . $list_no . '</td>
					<td>' . $created_dt . '</td>
					<td>' . $created_by . '</td>
					<td>' . nl2br($memo) . '</td>
				</tr>';
	}
}

// Pagination
$foot = lps_get_memo_pagination_link($user_id, $page, $row_per_page);


$json = compact('code', 'msg', 'body', 'foot');
echo json_encode( $json );

?>