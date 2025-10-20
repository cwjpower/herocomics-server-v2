<?php
/*
 * 2016.12.27		softsyw
 * Desc : 구매내역 전체보기
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

// Fetch order data
$order_array = lps_get_user_order_by_page($user_id, $page, $row_per_page);
$order_rows = $order_array['order_rows'];
$order_total_count = $order_array['total_row']['total_count'];

$body = '';

if (!empty($order_rows)) {
	foreach ($order_rows as $key => $val) {
		$list_no = $order_total_count - (($page - 1) * $row_per_page) - $key;
		
		$order_id = $val['order_id'];
		$order_status = $val['order_status'];
		$book_title = $val['book_title'];
		$total_amount = $val['total_amount'];
		$cybercash_paid = $val['cybercash_paid'];
		$created_dt = $val['created_dt'];
		$updated_dt = $val['updated_dt'];
		$user_name = $val['user_name'];
		$count_order = $val['count_order'];
		if ($count_order > 1) {
			$book_title .= ' 외 ' . ($count_order - 1) . '권';
		}
		
		$body .= '<tr>
					<td>' . $list_no . '</td>
					<td>' . $order_id . '</td>
					<td>' . $book_title . '</td>
					<td>' . substr($created_dt, 0, 10) . '</td>
					<td>' . number_format($total_amount) . '</td>
					<td>' . number_format($cybercash_paid) . '</td>
					<td>' . $wps_order_status[$order_status] . '</td>
				</tr>';
	}
}

// Pagination
$foot = lps_get_order_pagination_link($user_id, $page, $row_per_page);


$json = compact('code', 'msg', 'body', 'foot');
echo json_encode( $json );

?>