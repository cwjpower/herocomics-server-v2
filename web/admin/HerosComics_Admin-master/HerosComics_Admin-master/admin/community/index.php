<?php 
require_once '../../wps-config.php';
require_once INC_PATH . '/classes/WpsPaginator.php';

$user_id = wps_get_current_user_id();

// page number
$page = empty($_GET['page']) ? 1 : $_GET['page'];

// search
// $qa = empty($_GET['qa']) ? '' : trim($_GET['qa']);
$q = !isset($_GET['q']) ? '' : trim($_GET['q']);
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
// 	if ( !empty($q) ) {
// 		$sql = " AND $qa LIKE ?";
// 		array_push( $sparam, '%' . $q . '%' );
// 	}
}

// Advanced Search
// price_which 가격
$price_which = empty($_GET['price_which']) ? '' : $_GET['price_which'];
$price_from = empty($_GET['price_from']) ? '' : preg_replace('/\D/', '', $_GET['price_from']); 
$price_to = empty($_GET['price_to']) ? '' : preg_replace('/\D/', '', $_GET['price_to']); 
// upload_which 등록형태
$upload_which = empty($_GET['upload_which']) ? '' : $_GET['upload_which'];
$period_from = empty($_GET['period_from']) ? '' : $_GET['period_from'];
$period_to = empty($_GET['period_to']) ? '' : $_GET['period_to'];

$fbstatus = empty($_GET['bstatus']) ? array(): $_GET['bstatus'];

if (!empty($price_from) && !empty($price_to)) {
	$sql .= " AND $price_which BETWEEN ? AND ? ";
	array_push( $sparam, $price_from, $price_to );
}

if (!empty($upload_which)) {
	$sql .= " AND upload_type = ? ";
	array_push( $sparam, $upload_which );
}

if ( !empty($period_from) && !empty($period_to) ) {
	$sql .= " AND ? BETWEEN period_from AND period_to AND ? BETWEEN period_from AND period_to ";
	array_push( $sparam, $period_from, $period_to );
}

if ( !empty($fbstatus) ) {
	$impsql = '';

	foreach ( $fbstatus as $key => $val ) {
		$impsql .= "OR book_status = ? ";
		array_push($sparam, $val);
	}
	$sql .= ' AND (' . substr($impsql, 3) . ')';
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
			1
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
			user_id = '$user_id' AND
			book_status = '3000'
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

require_once './community-lnb.php';
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
						담벼락
					</h1>
					<ol class="breadcrumb">
						<li><a href="<?php echo ADMIN_URL ?>/admin.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li><a href="<?php ECHO ADMIN_URL ?>/community/">커뮤니티 관리</a></li>
						<li class="active"><b>담벼락</b></li>
					</ol>
				</div>

				<!-- Main content -->
				<div class="content body">
					<div class="box box-info">
						
						<div class="box-body">
							<form id="search-form" class="form-horizontal">
								<div class="row">
									<div class="col-sm-6">
										<div class="form-group">
											<div class="col-sm-4">
												<div class="input-group input-group-sm">
													<input type="text" name="q" value="<?php echo $q ?>" class="form-control">
													<span class="input-group-btn">
														<button type="submit" class="btn btn-primary btn-flat">검색</button>
													</span>
												</div>
											</div>
										</div>
									</div>
									<div class="col-sm-6 text-right">
										<a href="notice_list.php" class="btn btn-info">전체 공지 관리</a>
									</div>
								</div><!-- /.row -->
							</form>
						</div><!-- /.box -->
					</div>

					<div class="box box-primary">
						<div class="box-header">
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
										<th>책 제목</th>
										<th>담벼락 바로가기</th>
										<th></th>
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
									<tr>
										<td><?php echo $list_no ?></td>
										<td><a href="../books/book_detail.php?id=<?php echo $book_id ?>"><?php echo $pkg_label ?> <?php echo $book_title ?></a></td>
										<td><a href="post_list.php?id=<?php echo $book_id ?>">바로가기</a></td>
										<td><?php echo $wps_book_status[$book_status] ?></td>
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
				//Date picker
				$('.datepicker').datepicker({
			    	autoclose: true,
			    	language: 'kr',
			    	format: 'yyyy-mm-dd'
				});
	
				//Datemask yyyy-mm/dd
			    $("[data-mask]").inputmask();
	
				$("#reset-btn").click(function() {
					$("#search-form :input").val("");
				});
				$("#adv-reset-btn").click(function() {
					$("#adv-search-form :input").val("");
					$('select[name="price_which"] option:eq(0)').attr("selected", "selected");
				});

				$(".numeric").number( true, 0 );
			});
			</script>

<?php 
require_once ADMIN_PATH . '/admin-footer.php';
?>