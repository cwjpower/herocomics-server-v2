<?php 
require_once '../../wps-config.php';
require_once INC_PATH . '/classes/WpsPaginator.php';

$user_id = wps_get_current_user_id();

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
		$sql = " AND ( publisher = ? OR book_title LIKE ? OR author LIKE ? OR isbn LIKE ? ) ";
		array_push( $sparam, $q, '%' . $q . '%', '%' . $q . '%', '%' . $q . '%' );
	}
} else {
	if ( $q != '' ) {
// 		if ( !strcmp($qa, 'isbn') ) {
// 			$sql = " AND $qa = ?";
// 			array_push( $sparam, $q );
// 		} else {
			$sql = " AND $qa LIKE ?";
			array_push( $sparam, '%' . $q . '%' );
// 		}
	}
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

$comics_brand = empty($_GET['comics_brand']) ? '' : $_GET['comics_brand'];

$fbstatus = empty($_GET['bstatus']) ? array(): $_GET['bstatus'];

if (!empty($price_from) && !empty($price_to)) {
	$sql .= " AND $price_which BETWEEN ? AND ? ";
	array_push( $sparam, $price_from, $price_to );
}

if (!empty($upload_which)) {
	$sql .= " AND upload_type = ? ";
	array_push( $sparam, $upload_which );
}

if (!empty($comics_brand)) {
	$sql .= " AND comics_brand = ? ";
	array_push( $sparam, $comics_brand );
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
						책 리스트
					</h1>
					<ol class="breadcrumb">
						<li><a href="<?php echo ADMIN_URL ?>/admin.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li><a href="<?php ECHO ADMIN_URL ?>/books/">책 관리</a></li>
						<li class="active"><b>책 리스트</b></li>
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
							
							<form id="adv-search-form" class="form-horizontal">
								<div class="row">
									<div class="col-sm-2"></div>
									<div class="col-sm-8">
										<div class="form-group">
											<div class="col-sm-2">
												<select name="price_which" class="form-control">
													<optgroup label="가격 검색">
														<option value="normal_price" <?php echo strcmp($qa, 'normal_price') ? '' : 'selected'; ?>>정가</option>
														<option value="sale_price" <?php echo strcmp($qa, 'sale_price') ? '' : 'selected'; ?>>판매가</option>
													</optgroup>
												</select>
											</div>
											<div class="col-sm-4">
												<div class="input-group">
													<input type="text" name="price_from" class="form-control numeric" maxlength="10" value="<?php echo $price_from ?>">
													<div class="input-group-addon">원</div>
													<div class="input-group-addon"> ~ </div>
													<input type="text" name="price_to" class="form-control numeric" maxlength="10" value="<?php echo $price_to ?>">
													<div class="input-group-addon">원</div>
												</div>
											</div>
											<div class="col-sm-2">
												<select name="upload_which" class="form-control">
													<optgroup label="등록형태">
														<option value="">-등록형태-</option>
														<?php
														foreach ($wps_upload_type as $key => $val ) {
															if (!empty($key)) {
																$selected = strcmp($upload_which, $key) ? '' : 'selected';
														?>
																		<option value="<?php echo $key ?>" <?php echo $selected ?>><?php echo $val ?></option>
														<?php
															}
														}
														?>
													</optgroup>
												</select>
											</div>
											<div class="col-sm-2">
												<select name="comics_brand" class="form-control">
													<optgroup label="코믹스 브랜드">
														<option value="">-코믹스 브랜드-</option>
														<?php
														foreach ($wps_comics_brand as $key => $val ) {
															if (!empty($key)) {
																$selected = strcmp($comics_brand, $key) ? '' : 'selected';
																?>
																<option value="<?php echo $key ?>" <?php echo $selected ?>><?php echo $val ?></option>
																<?php
															}
														}
														?>
													</optgroup>
												</select>
											</div>

											<div class="col-sm-4">
												<div class="input-group datepicker input-daterange">
													<input type="text" id="period_from" name="period_from" value="<?php echo $period_from ?>" class="form-control">
													<span class="input-group-addon">~</span>
													<input type="text" id="period_to" name="period_to" value="<?php echo $period_to ?>" class="form-control">
												</div>
											</div>
										</div>
										<div class="form-group">	
											<div class="col-sm-12">
									<?php 
									foreach ($wps_book_status as $key => $val ) {
										$checked = in_array($key, $fbstatus) ? 'checked' : '';
									?>
												<label><input type="checkbox" name="bstatus[]" value="<?php echo $key ?>" <?php echo $checked ?>> <?php echo $val ?></label> &nbsp;
									<?php 
									}
									?>
											</div>
										</div>
									</div>
									<div class="col-sm-2">
										<button type="submit" class="btn btn-warning btn-sm">검색</button> &nbsp; &nbsp;
										<button type="button" id="adv-reset-btn" class="btn btn-default btn-sm">초기화</button>
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
										<th>제목 / 저자</th>

										<th>ISBN</th>
										<th>정가/판매가</th>

										<th>출판사/브랜드</th>
										<th>상태</th>
										<th>미리보기</th>
										<th>액션뷰 정보</th>
									</tr>
								</thead>
								<tbody>
					<?php
					if ( !empty($rows) ) {
						$list_no = $page == 1 ? $total_count : $total_count - (($page - 1) * $paginator->rows_per_page);
						
						foreach ( $rows as $key => $val ) {
							$book_id = $val['ID'];
							$publisher = $val['publisher'];
							$comics_brand = $val['comics_brand']; // 마블, 디씨, 이미지 코믹스 구분
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
										<td><?= $book_id ?></td>
										<td><a href="book_detail.php?id=<?php echo $book_id ?>"><?php echo $pkg_label ?> <?php echo $book_title ?></a>
											<br> (<?php echo $author ?>)</td>

										<td><?php echo $isbn ?></td>
										<td><?php echo number_format($normal_price) ?><br> <?php echo number_format($sale_price) ?></td>

										<td><?php echo $publisher ?><br><?php echo $wps_comics_brand[$comics_brand] ?></td>
										<!--<td>--> <?php //echo $wps_upload_type[$upload_type] ?><!--</td>-->

										<td><?php echo $wps_book_status[$book_status] ?></td>
										<td><a href="/admin/books/book_preview_list.php?book_id=<?php echo $book_id; ?>" class="btn btn-default btn-sm">미리보기 관리</a></td>
										<td><a href="javascript:popupActionViewMgmt(<?=$book_id?>)" class="btn btn-default btn-sm">액션뷰관리</a></td>
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
			function popupActionViewMgmt(book_id){
				window.open('book_action_map.php?book_id='+book_id, 'ActionView Management', 'width='+screen.availWidth + ',height='+screen.availHeight+',scrollbars=yes, fullscreen=yes');
			}

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