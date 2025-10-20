<?php 
require_once '../../wps-config.php';
require_once FUNC_PATH . '/functions-book.php';
require_once FUNC_PATH . '/functions-term.php';
require_once INC_PATH . '/classes/WpsPaginator.php';

$user_id = wps_get_current_user_id();

// page number
$page = empty($_GET['page']) ? 1 : $_GET['page'];

// search
$qa = empty($_GET['qa']) ? '' : trim($_GET['qa']);
$q = empty($_GET['q']) ? '' : trim($_GET['q']);
$sql = '';
$pph = '';
$sparam = [];

// Simple search
if ( empty($qa) ) {
	if ( !empty($q) ) {
		$sql = " AND ( publisher = ? OR book_title LIKE ? OR author LIKE ? OR isbn LIKE ? ) ";
		array_push( $sparam, $q, '%' . $q . '%', '%' . $q . '%', '%' . $q . '%' );
	}
} else {
	if ( !empty($q) ) {
		if ( !strcmp($qa, 'isbn') ) {
			$sql = " AND $qa = ?";
			array_push( $sparam, $q );
		} else {
			$sql = " AND $qa LIKE ?";
			array_push( $sparam, '%' . $q . '%' );
		}
	}
}


// Positional placeholder ?
if ( !empty($sql) ) {
	$pph_count = substr_count($sql, '?');
	for ( $i = 0; $i < $pph_count; $i++ ) {
		$pph .= 's';
	}
}

if (!empty($pph)) {
	array_unshift($sparam, $pph);
}

if (wps_is_admin()) {
	$query = "
		SELECT
			*
		FROM
			bt_books
		WHERE
			book_status IN ('3000', '4000', '4001', '4101')
			$sql
		ORDER BY
			ID DESC
	";
} else {
	$query = "
		SELECT
			*
		FROM
			bt_books
		WHERE
			book_status IN ('3000', '4000', '4001', '4101') AND
			user_id = '$user_id'
			$sql
		ORDER BY
			ID DESC
	";
}

$paginator = new WpsPaginator($wdb, $page);
$rows = $paginator->ls_init_pagination( $query, $sparam );
$total_count = $paginator->ls_get_total_rows();
$total_records = $paginator->ls_get_total_records();

if (wps_is_admin()) {
	require_once ADMIN_PATH . '/admin-header.php';
} else {
	require_once ADMIN_PATH . '/agent-header.php';
}

/*
 * Desc : 수정/삭제 요청 내역
 */
$book_array = lps_get_req_book_by_page(1, 5);
$book_rows = $book_array['book_rows'];
$book_total_count = $book_array['total_row']['total_count'];

require_once './books-lnb.php';
?>
			<!-- bootstrap datepicker -->
			<link rel="stylesheet" href="<?php echo ADMIN_URL ?>/css/datepicker3.css">
			
			<!-- bootstrap datepicker -->
			<script src="<?php echo ADMIN_URL ?>/js/bootstrap-datepicker.js"></script>
			<script src="<?php echo ADMIN_URL ?>/js/locales/bootstrap-datepicker.kr.js"></script>

			<!-- Content Wrapper. Contains page content -->
			<div class="content-wrapper">
				<!-- Content Header (Page header) -->
				<div class="content-header">
					<h1>
						수정/삭제 요청 내역
					</h1>
					<ol class="breadcrumb">
						<li><a href="<?php echo ADMIN_URL ?>/admin.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li><a href="<?php ECHO ADMIN_URL ?>/books/">책 관리</a></li>
						<li class="active"><b>수정/삭제 요청</b></li>
					</ol>
				</div>

				<!-- Main content -->
				<div class="content body">
				
					<div class="box box-danger ls-user-logs">
						<div class="box-header">
							<div class="text-right">
								<button type="button" id="more-req-books" class="btn btn-info btn-sm">전체보기</button>
							</div>
						</div>
						<div class="box-body">
							<table id="list-req-books" class="table table-striped table-hover">
								<colgroup>
									<col style="width: 30%;">
									<col>
									<col style="width: 10%;">
									<col style="width: 10%;">
									<col style="width: 7%;">
									<col style="width: 10%;">
								</colgroup>
								<thead>
									<tr>
										<th>분류</th>
										<th>책제목</th>
										<th>요청일</th>
										<th>처리상태</th>
										<th>담당자</th>
										<th>처리일</th>
									</tr>
								</thead>
								<tbody>
						<?php 
						/*
						 * Desc : 전체보기는 ./ajax/get-more-req-books.php
						 */
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
// 								$list_no = $book_total_count - $key;
						?>
									<tr>
										<td><?php echo $bk_category ?></td>
										<td><?php echo $pkg_label ?> <a href="book_detail.php?id=<?php echo $bk_id ?>"><?php echo $bk_title ?></a></td>
										<td><?php echo $bk_ask_dt ?></td>
										<td>
											<?php echo $wps_book_status[$bk_status] ?>
								<?php 
								if ( $bk_status == '2001' || $bk_status == '2101' ) {
								?>
											&nbsp; 
											<a class="btn bg-purple btn-xs req-cancel" id="bk-<?php echo $bk_id ?>">요청취소</a>
								<?php 
								}
								?>
										</td>
										<td><?php echo $admin ?></td>
										<td><?php echo $bk_res_dt ?></td>
									</tr>
						<?php 
							}
						}
						?>
								</tbody>
								<tfoot>
									<tr>
										<td colspan="6" class="text-center"></td>
									</tr>
								</tfoot>
							</table>
						</div>
					</div><!-- /.box -->
				
					<div class="box box-primary">
						<div class="box-header">
							<form id="search-form" class="form-horizontal">
								<div class="row">
									<div class="col-sm-2"></div>
									<div class="col-sm-8">
										<div class="form-group">
											<div class="col-sm-3">
												<select name="qa" class="form-control">
													<optgroup label="키워드 검색">
														<option value="">-전체-</option>
														<option value="book_title" <?php echo strcmp($qa, 'book_title') ? '' : 'selected'; ?>>책 제목</option>
														<option value="author" <?php echo strcmp($qa, 'author') ? '' : 'selected'; ?>>저자</option>
														<option value="publisher" <?php echo strcmp($qa, 'publisher') ? '' : 'selected'; ?>>출판사</option>
														<option value="isbn" <?php echo strcmp($qa, 'isbn') ? '' : 'selected'; ?>>ISBN</option>
													</optgroup>
												</select>
											</div>
											<div class="col-sm-4">
												<div class="input-group input-group-sm">
													<input type="text" name="q" value="<?php echo $q ?>" class="form-control">
													<span class="input-group-btn">
														<button type="submit" class="btn btn-primary btn-flat">검색</button>
													</span>
												</div>
											</div>
											<div class="col-sm-5">
												<button type="button" id="reset-btn" class="btn btn-default btn-sm">초기화</button>
											</div>
										</div>
									</div>
									<div class="col-sm-2"></div>
								</div><!-- /.row -->
							</form>
							
							<div>
					<?php 
					if (wps_is_admin()) {
					?>
								<i class="fa fa-circle-o text-red"></i> Total: <b><?php echo number_format($total_records) ?></b>  &nbsp;
					<?php 
					}
					?>
								<i class="fa fa-circle-o text-yellow"></i> 검색: <b><?php echo number_format($total_count) ?></b>
							</div>
						</div>
						<div class="box-body">
							<table class="table table-striped table-hover">
								<thead>
									<tr>
										<th>#</th>
										<th>제목</th>
										<th>저자</th>
										<th>ISBN</th>
										<th>판매가</th>
										<th>등록형태</th>
										<th>상태</th>
										<th>수정</th>
										<th>삭제</th>
									</tr>
								</thead>
								<tbody>
					<?php
					if ( !empty($rows) ) {
						$list_no = $page == 1 ? $total_count : $total_count - (($page - 1) * $paginator->rows_per_page);
	
						foreach ( $rows as $key => $val ) {
							$book_id = $val['ID'];
							$publisher = $val['publisher'];
							$book_title = $val['book_title'];
							$author = $val['author'];
							$isbn = $val['isbn'];
							$normal_price = $val['normal_price'];
							$sale_price = $val['sale_price'];
							$upload_type = $val['upload_type'];
							$book_status = $val['book_status'];
							$is_pkg = $val['is_pkg'];
							
							$pkg_label = !strcmp($is_pkg, 'N') ? '<span class="label label-default">단품</span>' : '<span class="label label-success">세트</span>';
					?>
									<tr id="book-<?php echo $book_id ?>">
										<td><?php echo $list_no ?></td>
										<td><?php echo $pkg_label ?> <a href="book_detail.php?id=<?php echo $book_id ?>"><?php echo $book_title ?></a></td>
										<td><?php echo $author ?></td>
										<td><?php echo $isbn ?></td>
										<td><?php echo number_format($sale_price) ?></td>
										<td><?php echo $wps_upload_type[$upload_type] ?></td>
										<td><?php echo $wps_book_status[$book_status] ?></td>
										<td><a href="book_req_edit.php?id=<?php echo $book_id ?>" class="btn btn-warning btn-xs">수정</a></td>
										<td><a href="book_req_del.php?id=<?php echo $book_id ?>" class="btn btn-danger btn-xs">삭제</a></td>
									</tr>
					<?php
							$list_no--;
						}
					}
					?>
								</tbody>
							</table>
						</div>
						<div class="box-footer text-center">
							<?php echo $paginator->ls_bootstrap_pagination_link(); ?>
						</div>
					</div><!-- /.box -->

				</div><!-- /.content -->
			</div><!-- /.content-wrapper -->
			
			<!-- InputMask -->
			<script src="<?php echo ADMIN_URL ?>/js/jquery/input-mask/jquery.inputmask.js"></script>
			<script src="<?php echo ADMIN_URL ?>/js/jquery/input-mask/jquery.inputmask.date.extensions.js"></script>
			<script src="<?php echo ADMIN_URL ?>/js/jquery/input-mask/jquery.inputmask.extensions.js"></script>
			<!-- Numeric -->
			<script src="<?php echo INC_URL ?>/js/jquery/jquery.number.min.js"></script>
			
			<script>
			$(function() {
				// 수정/삭제 요청내역 전체보기
				$(document).on("click", "#more-req-books", function() {
					$.ajax({
						type : "POST",
						url : "./ajax/get-more-req-books.php",
						data : {},
						dataType : "json",
						success : function(res) {
							if ( res.code == "0" ) {
								$("#list-req-books tbody").html( res.body );
								$("#list-req-books tfoot td").html( res.foot );
								$("#more-req-books").text("전체보기 닫기");
								$("#more-req-books").attr("id", "close-req-books");
							} else {
								alert( res.msg );
							}
						}
					});
				});

				// 수정/삭제 요청내역 전체보기 닫기
				$(document).on("click", "#close-req-books", function() {
					$("#list-req-books tbody tr:gt(4)").remove();
					$("#list-req-books tfoot td").html("");

					$("#close-req-books").text("전체보기");
					$("#close-req-books").attr("id", "more-req-books");
				});

				// 수정/삭제 요청 내역 Pagination Link
				$(document).on("click", "ul.lps-pager li a", function(e) {
// 					e.preventDefault();

					$(".lps-pager li").removeClass("active");
					$(this).closest("li").addClass("active");
					var page = $(this).attr("title");

					$.ajax({
						type : "POST",
						url : "./ajax/get-more-req-books.php",
						data : {
							"page" : page 
						},
						dataType : "json",
						success : function(res) {
							if ( res.code == "0" ) {
								$("#list-req-books tbody").html( res.body );
								$("#list-req-books tfoot td").html( res.foot );
							} else {
								alert( res.msg );
							}
						}
					});
				});

				// 요청취소
				$(document).on("click", ".req-cancel", function(e) {
					var id = $(this).attr("id").replace(/\D/g, "");
					$.ajax({
						type : "POST",
						url : "./ajax/book-req-cancel.php",
						data : {
							"id" : id
						},
						dataType : "json",
						success : function(res) {
							if ( res.code == "0" ) {
								$("#bk-" + id).closest("tr").fadeOut();
							} else {
								alert( res.msg );
							}
						}
					});
				});

				$("#reset-btn").click(function() {
					$("#search-form :input").val("");
				});
			});
			</script>

<?php 
require_once ADMIN_PATH . '/admin-footer.php';
?>