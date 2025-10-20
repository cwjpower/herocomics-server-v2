<?php
/*
 * 2016.08.29		softsyw
 * Desc : 수정/삭제 요청 내역 전체보기
 */
require_once '../../../wps-config.php';
require_once FUNC_PATH . '/functions-book.php';
require_once FUNC_PATH . '/functions-term.php';

$code = 0;
$msg = '';

$page = empty($_POST['page']) ? 1 : $_POST['page'];
$row_per_page = 15;	// 한 페이지 당 출력할 데이터 갯수

// Fetch memo data
$book_array = lps_get_req_book_by_page($page, $row_per_page);
$book_rows = $book_array['book_rows'];
$book_total_count = $book_array['total_row']['total_count'];

$body = '';

if (!empty($book_rows)) {
	foreach ($book_rows as $key => $val) {
		$bk_id = $val['ID'];
		$bk_title = $val['book_title'];
		$bk_req_edit_dt = $val['req_edit_dt'];
		$bk_req_del_dt = $val['req_del_dt'];
		$bk_status = $val['book_status'];
		$bk_cate_first = $val['category_first'];
		$bk_cate_second = $val['category_second'];
		$bk_cate_third = $val['category_third'];
		$is_pkg = $val['is_pkg'];
		
		$bk_ask_dt = '';
		$admin = '';
		$bk_res_dt = '';
		
		if ($bk_status == '2001') {		// 수정 요청
			$bk_ask_dt = $bk_req_edit_dt;
		} else if ($bk_status == '2101') {	// 삭제 요청
			$bk_ask_dt = $bk_req_del_dt;
		} else if ($bk_status == '4001') {	// 수정 거절
			$bk_ask_dt = $bk_req_edit_dt;
			$bk_meta = unserialize(lps_get_book_meta($bk_id, 'lps_res_reject_edit'));
			$admin_id = $bk_meta['admin_id'];
			$usr_row = wps_get_user($admin_id);
			$admin = $usr_row['user_name'];
			$bk_res_dt = date('y.m.d H:i', $bk_meta['reject_dt']);
		} else if ($bk_status == '4101') {	// 삭제 거절
			$bk_ask_dt = $bk_req_del_dt;
			$bk_meta = unserialize(lps_get_book_meta($bk_id, 'lps_res_reject_delete'));
			$admin_id = $bk_meta['admin_id'];
			$usr_row = wps_get_user($admin_id);
			$admin = $usr_row['user_name'];
			$bk_res_dt = date('y.m.d H:i', $bk_meta['reject_dt']);
		}
		
		// 카테고리
		$bk_category = wps_get_term_name($bk_cate_first) . ' > ' 
					.  wps_get_term_name($bk_cate_second) . ' > '
					.  wps_get_term_name($bk_cate_third);
		
		$pkg_label = !strcmp($is_pkg, 'N') ? '<span class="label label-default">단품</span>' : '<span class="label label-success">세트</span>';
		
		if ( $bk_status != '2001' && $bk_status != '2101' ) {
			$body .= '<tr>
						<td>' . $bk_category . '</td>
						<td>' . $pkg_label . ' <a href="book_detail.php?id=' . $bk_id . '">' . $bk_title . '</a></td>
						<td>' . $bk_ask_dt . '</td>
						<td>' . $wps_book_status[$bk_status] .'</td>
						<td>' . $admin . '</td>
						<td>' . $bk_res_dt . '</td>
					</tr>';
		} else {
			$body .= '<tr>
						<td>' . $bk_category . '</td>
						<td>' . $pkg_label . ' <a href="book_detail.php?id=' . $bk_id . '">' . $bk_title . '</a></td>
						<td>' . $bk_ask_dt . '</td>
						<td>' .
								$wps_book_status[$bk_status] .
								'&nbsp; <a class="btn bg-purple btn-xs req-cancel" id="bk-' . $bk_id .'">요청취소</a>'
						. '</td>
						<td>' . $admin . '</td>
						<td>' . $bk_res_dt . '</td>
					</tr>';
		}
	}
}

// Pagination
$foot = lps_get_req_book_pagination_link($page, $row_per_page);


$json = compact('code', 'msg', 'body', 'foot');
echo json_encode( $json );

?>