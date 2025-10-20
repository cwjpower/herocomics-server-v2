<?php 
require_once '../../wps-config.php';
require_once INC_PATH . '/classes/WpsPaginator.php';

$user_id = wps_get_current_user_id();

// page number
$page = empty($_GET['page']) ? 1 : $_GET['page'];

// 구분
$pt = empty($_GET['pt']) ? 'A' : $_GET['pt'];

// search
$qa1 = empty($_GET['qa1']) ? '' : trim($_GET['qa1']);
$q1 = !isset($_GET['q1']) ? '' : trim($_GET['q1']);

$coupon_type = empty($_GET['coupon_type']) ? '' : $_GET['coupon_type'];
$discount_type = empty($_GET['discount_type']) ? '' : $_GET['discount_type'];
// $period_from = empty($_GET['period_from']) ? '' : $_GET['period_from'];
$period_to = empty($_GET['period_to']) ? '' : $_GET['period_to'];

$sql = '';
$pph = '';
$sparam = [];

// 검색어
if ( !empty($qa1) && !empty($q1) ) {
	$sql .= " AND $qa1 LIKE ?";
	array_push( $sparam, '%' . $q1 . '%' );
}

if ( !empty($coupon_type) ) {
	$sql .= " AND c.coupon_type = ?";
	array_push( $sparam, $coupon_type );
}

if ( !empty($discount_type) ) {
	$sql .= " AND c.discount_type = ?";
	array_push( $sparam, $discount_type );
}

// 사용 날짜
if ( !empty($period_to) ) {
	$sql .= " AND ? BETWEEN c.period_from AND c.period_to";
	array_push( $sparam, $period_to );
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
			c.ID,
			c.coupon_name,
			c.coupon_type,
			c.coupon_desc,
			c.period_from,
			c.period_to,
			c.discount_type,
			c.discount_rate,
			c.discount_amount,
			c.item_price_min,
			c.item_price_max,
			c.related_publisher,
			c.created_dt,
			u.display_name
		FROM
			bt_coupon AS c
		LEFT JOIN
			bt_users AS u
		ON
			u.ID = c.related_publisher
		WHERE 
			1
			$sql
		ORDER BY
			c.ID DESC
";
$paginator = new WpsPaginator($wdb, $page);
$rows = $paginator->ls_init_pagination( $query, $sparam );
$total_count = $paginator->ls_get_total_rows();

if (wps_is_admin()) {
	require_once ADMIN_PATH . '/admin-header.php';
} else {
	require_once ADMIN_PATH . '/agent-header.php';
}

require_once './promotion-lnb.php';
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
						쿠폰 발급 내역
						<a href="./coupon_new.php" class="btn btn-info btn-sm">쿠폰 등록</a>
					</h1>
					<ol class="breadcrumb">
						<li><a href="<?php echo ADMIN_URL ?>/admin.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li>프로모션</li>
						<li class="active"><b>쿠폰 발급 내역</b></li>
					</ol>
				</div>

				<!-- Main content -->
				<div class="content body">
					<div class="box box-info">
						
						<div class="box-body">
							<form id="search-form" class="form-horizontal">
								<table class="table table-bordered ls-table">
									<colgroup>
										<col style="width: 15%;">
										<col style="width: 35%;">
										<col style="width: 15%;">
										<col style="width: 35%;">
									</colgroup>
									<tbody>
										<tr>
											<td class="item-label">검색어</td>
											<td>
												<div class="col-sm-6">
													<select name="qa1" class="form-control">
														<optgroup label="키워드 검색">
															<option value="">-선택-</option>
															<option value="c.coupon_name" <?php echo strcmp($qa1, 'c.coupon_name') ? '' : 'selected'; ?>>쿠폰 이름</option>
															<option value="c.coupon_desc" <?php echo strcmp($qa1, 'c.coupon_desc') ? '' : 'selected'; ?>>쿠폰 설명</option>
															<option value="u.display_name" <?php echo strcmp($qa1, 'u.display_name') ? '' : 'selected'; ?>>출판사</option>
														</optgroup>
													</select>
												</div>
												<div class="col-sm-6">
													<input type="text" name="q1" value="<?php echo $q1 ?>" class="form-control">
												</div>
											</td>
											<td class="item-label">쿠폰 종류</td>
											<td>
												<div class="col-sm-12">
													<select name="coupon_type" class="form-control">
														<optgroup label="쿠폰 종류 검색">
															<option value="">-선택-</option>
															<option value="item" <?php echo strcmp($coupon_type, 'item') ? '' : 'selected'; ?>>개별 책</option>
															<option value="cart" <?php echo strcmp($coupon_type, 'cart') ? '' : 'selected'; ?>>장바구니</option>
														</optgroup>
													</select>
												</div>
											</td>
										</tr>
										<tr>
											<td class="item-label">할인 종류</td>
											<td>
												<div class="col-sm-12">
													<select name="discount_type" class="form-control">
														<optgroup label="할인 종류 검색">
															<option value="">-선택-</option>
															<option value="amount" <?php echo strcmp($discount_type, 'amount') ? '' : 'selected'; ?>>할인금액</option>
															<option value="rate" <?php echo strcmp($discount_type, 'rate') ? '' : 'selected'; ?>>할인율</option>
														</optgroup>
													</select>
												</div>
											</td>
											<td class="item-label">사용 날짜</td>
											<td>
												<div class="col-sm-6">
													<div class="input-group datepicker input-daterange">
														<input type="text" id="period_to" name="period_to" value="<?php echo $period_to ?>" class="form-control">
													</div>
												</div>

											</td>
										</tr>
										<tr>
											<td colspan="4">
												<div class="col-sm-12 text-center">
													<button type="submit" class="btn btn-primary btn-flat">검색</button> &nbsp;
													<button type="button" id="reset-btn" class="btn btn-default btn-flat">초기화</button>
												</div>
											</td>
										</tr>
									</tbody>
								</table>
							</form>
						</div><!-- /.box -->
					</div>

					<div class="box box-primary">
						<div class="box-header">
							<div>
								<i class="fa fa-circle-o text-yellow"></i> 결과: <b><?php echo number_format($total_count) ?></b>
							</div>
						</div>
						<div class="box-body">
							<table class="table table-striped table-hover">
								<thead>
									<tr>
										<th>쿠폰종류</th>
										<th>쿠폰정보</th>
										<th>할인정보</th>
										<th>유효기간</th>
										<th>쿠폰 적용 출판사</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
					<?php
					if ( !empty($rows) ) {
						$list_no = $page == 1 ? $total_count : $total_count - (($page - 1) * $paginator->rows_per_page);
						$ymd = date('Y-m-d');
						
						foreach ( $rows as $key => $val ) {
							$ID = $val['ID'];
							$coupon_name = $val['coupon_name'];
							
							$coupon_type = $val['coupon_type'];
							$coupon_desc = $val['coupon_desc'];
							$period_from = $val['period_from'];
							$period_to = $val['period_to'];
							$discount_type = $val['discount_type'];
							$discount_amount = $val['discount_amount'];
							$discount_rate = $val['discount_rate'];
							$item_price_min = $val['item_price_min'];
							$item_price_max = $val['item_price_max'];
							$display_name = $val['display_name'];
							$created_dt = $val['created_dt'];
							
							$period_label = !empty($period_to) && $ymd > $period_to ? '<span class="label label-default">기간만료</span>' : '';
							
					?>
									<tr>
										<td>
											<?php echo strcmp($coupon_type, 'cart') ? '개별 책' : '장바구니'; ?>
										</td>
										<td>
											<div class="text-danger"><?php echo $coupon_name ?></div>
											<div><?php echo nl2br($coupon_desc) ?></div>
											
										</td>
										<td>
											<span class="label label-success"><?php echo strcmp($discount_type, 'amount') ? '할인율' : '할인금액'; ?></span>
											<?php 
											if (!strcmp($discount_type, 'amount')) {
												echo number_format($discount_amount) . '원';
												echo '<div class="text-muted">' . number_format($item_price_min) . '원 이상 구매</div>';
												
											} else {
												echo $discount_rate . '%';
												echo '<div class="text-muted">' . number_format($item_price_min) . '원 이상 구매 | 최대 ' . number_format($item_price_max) . '원 할인</div>';
											}
											?>
										</td>
										<td>
											<?php echo $period_from ?> ~ <?php echo $period_to ?>
											<div><?php echo $period_label ?></div>
										</td>
										<td>
											<?php echo $display_name ?>
										</td>
										<td>
											<a href="coupon_edit.php?id=<?php echo $ID ?>" class="btn btn-info btn-sm">편집</a>
										</td>
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