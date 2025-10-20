<?php
/*
 * 2016.08.25	softsyw
 * Desc : 관리자 > 수정(2001)/삭제(2101) 요청 상태의 책 리스트
 * 
 */
require_once '../../wps-config.php';
require_once INC_PATH . '/classes/WpsPaginator.php';

// page number
$page = empty($_GET['page']) ? 1 : $_GET['page'];

// search
$qa = empty($_GET['qa']) ? '' : trim($_GET['qa']);
$q = !isset($_GET['q']) ? '' : trim($_GET['q']);
$sql = '';
$pph = '';
$sparam = [];

// Simple search
if ( empty($qa) ) {
	if ( !empty($q) ) {
		$sql = " AND ( b.publisher LIKE ? OR b.book_title LIKE ? OR b.author LIKE ? OR b.isbn = ? OR u.user_name LIKE ? ) ";
		array_push( $sparam, '%' . $q . '%', '%' . $q . '%', '%' . $q . '%', $q, '%' . $q . '%' );
	}
} else {
	if ( $q != '' ) {
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

$query = "
	SELECT
		b.ID,
		b.book_title,
		b.publisher,
		b.upload_type,
		b.book_status,
		b.req_edit_dt,
		b.req_del_dt,
		b.is_pkg,
		u.user_login,
		u.user_name
	FROM
		bt_books AS b
	INNER JOIN
		bt_users AS u
	WHERE
		b.user_id = u.ID AND
		b.book_status > '2000' AND
		b.book_status < '2200'
		$sql
	ORDER BY
		req_edit_dt DESC,
		req_del_dt DESC
";
$paginator = new WpsPaginator($wdb, $page);
$rows = $paginator->ls_init_pagination( $query, $sparam );
$total_count = $paginator->ls_get_total_rows();

require_once ADMIN_PATH . '/admin-header.php';

require_once './books-lnb.php';
?>

			<!-- Content Wrapper. Contains page content -->
			<div class="content-wrapper">
				<!-- Content Header (Page header) -->
				<div class="content-header">
					<h1>
						수정/삭제 요청 확인
					</h1>
					<ol class="breadcrumb">
						<li><a href="<?php echo ADMIN_URL ?>/admin.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li><a href="<?php ECHO ADMIN_URL ?>/books/">책 관리</a></li>
						<li class="active"><b>수정/삭제 요청 확인</b></li>
					</ol>
				</div>

				<!-- Main content -->
				<div class="content body">
					<div class="box box-info">
						
						<div class="box-body">
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
														<option value="user_name" <?php echo strcmp($qa, 'user_name') ? '' : 'selected'; ?>>회원이름(요청자)</option>
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
											<div class="col-sm-3">
												<button type="button" id="reset-btn" class="btn btn-default btn-sm">초기화</button>
											</div>
										</div>
									</div>
									<div class="col-sm-2"></div>
								</div><!-- /.row -->
							</form>
						</div><!-- /.box -->
					</div>

					<div class="box box-primary">
						<div class="box-header">
							<i class="fa fa-circle-o text-yellow"></i> Total: <b><?php echo number_format($total_count) ?></b>
						</div>
						<div class="box-body">
							<table class="table table-striped table-hover">
								<thead>
									<tr>
										<th>책 제목</th>
										<th>출판사</th>
										<th>요청분류</th>
										<th>요청일</th>
										<th>요청자</th>
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
							$upload_type = $val['upload_type'];
							$book_status = $val['book_status'];
							$req_edit_dt = date('y.m.d H:i', strtotime($val['req_edit_dt']));
							$req_del_dt = date('y.m.d H:i', strtotime($val['req_del_dt']));
							$user_login = $val['user_login'];
							$user_name = $val['user_name'];
							$is_pkg = $val['is_pkg'];
							
							$pkg_label = !strcmp($is_pkg, 'N') ? '<span class="label label-default">단품</span>' : '<span class="label label-success">세트</span>';
					?>
									<tr>
										<td>
											<?php echo $pkg_label ?>
								<?php 
								if ($book_status == '2101') {	// 삭제 요청
								?>
											<a href="book_res_del.php?id=<?php echo $book_id ?>"><?php echo $book_title ?></a>
								<?php 
								} else {	// 2001 수정 요청
								?>
											<a href="book_res_edit.php?id=<?php echo $book_id ?>"><?php echo $book_title ?></a>
								<?php 
								}
								?>
										</td>
										<td><?php echo $publisher ?></td>
										<td><?php echo $wps_book_status[$book_status] ?></td>
										<td><?php echo $book_status == '2101' ? $req_del_dt : $req_edit_dt ?></td>
										<td><?php echo $user_name ?> (<?php echo $user_login ?>)</td>
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
			
			<script>
			$(function() {
				$("#reset-btn").click(function() {
					$("#search-form :input").val("");
				});
			});
			</script>

<?php 
require_once ADMIN_PATH . '/admin-footer.php';
?>