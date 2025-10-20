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
// $q2 = !isset($_GET['q2']) ? '' : trim($_GET['q2']);

$period_from = empty($_GET['period_from']) ? '' : $_GET['period_from'];
$period_to = empty($_GET['period_to']) ? '' : $_GET['period_to'];

$sql = '';
$pph = '';
$sparam = [];

// 검색어
if ( !empty($qa1) && !empty($q1) ) {
	$sql .= " AND $qa1 LIKE ?";
	array_push( $sparam, '%' . $q1 . '%' );
}

// 기간
if ( !empty($period_from) && !empty($period_to) ) {
	$sql .= " AND p.payment_dt BETWEEN ? AND ?";
	array_push( $sparam, $period_from, $period_to );
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
			p.ID,
			p.payment_amount,
			p.payment_method,
			p.payment_state,
			p.point_amount,
			p.created_dt,
			p.payment_dt,
			p.meta_value,
			u.user_login,
			u.user_name,
			u.user_email,
			u.display_name,
			m.meta_value AS total_point
		FROM
			bt_user_payment_list AS p
		LEFT JOIN
			bt_users AS u
		ON
			u.ID = p.user_id
		LEFT JOIN
			bt_users_meta AS m
		ON
			u.ID = m.user_id AND
			m.meta_key = 'lps_user_total_point'
		WHERE 
			p.point_amount > 0
			$sql
		ORDER BY
			p.ID DESC
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
						포인트 적립내역
					</h1>
					<ol class="breadcrumb">
						<li><a href="<?php echo ADMIN_URL ?>/admin.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li>프로모션</li>
						<li class="active"><b>포인트 적립내역</b></li>
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
										<col>
									</colgroup>
									<tbody>
										<tr class="hide">
											<td class="item-label">구분</td>
											<td>
												<div class="col-sm-12">
													<label style="margin-right: 20px;">
														<input type="radio" name="pt" value="A" <?php echo strcmp($pt, 'A') ? '' : 'checked'; ?>>전체
													</label>
													<label style="margin-right: 20px;">
														<input type="radio" name="pt" value="G" <?php echo strcmp($pt, 'G') ? '' : 'checked'; ?>>지급
													</label>
													<label>
														<input type="radio" name="pt" value="S" <?php echo strcmp($pt, 'S') ? '' : 'checked'; ?>>사용
													</label>
												</div>
											</td>
										</tr>
										<tr>
											<td class="item-label">검색어</td>
											<td>
												<div class="col-sm-3">
													<select name="qa1" class="form-control">
														<optgroup label="키워드 검색">
															<option value="">-선택-</option>
															<option value="u.user_name" <?php echo strcmp($qa1, 'u.user_name') ? '' : 'selected'; ?>>이름</option>
															<option value="u.display_name" <?php echo strcmp($qa1, 'u.display_name') ? '' : 'selected'; ?>>닉네임</option>
															<option value="u.user_login" <?php echo strcmp($qa1, 'u.user_login') ? '' : 'selected'; ?>>계정(Email)</option>
															<option value="u.user_email" <?php echo strcmp($qa1, 'u.user_email') ? '' : 'selected'; ?>>이메일 주소</option>
															<option value="u.mobile" <?php echo strcmp($qa1, 'u.mobile') ? '' : 'selected'; ?>>연락처</option>
														</optgroup>
													</select>
												</div>
												<div class="col-sm-3">
													<input type="text" name="q1" value="<?php echo $q1 ?>" class="form-control">
												</div>
											</td>
										</tr>
										<tr>
											<td class="item-label">기간</td>
											<td>
												<div class="col-sm-5">
													<a class="btn btn-info btn-xs wps-date" title="0">오늘</a>
													<a class="btn btn-info btn-xs wps-date" title="1">어제</a>
													<a class="btn btn-info btn-xs wps-date" title="7">최근 7일</a>
													<a class="btn btn-info btn-xs wps-date" title="15">최근 15일</a>
													<a class="btn btn-info btn-xs wps-date" title="30">최근 30일</a>
													<a class="btn btn-info btn-xs wps-date" title="60">최근 60일</a>
													<a class="btn btn-info btn-xs wps-date" title="90">최근 90일</a>
												</div>
												<div class="col-sm-7">
													<div class="input-group datepicker input-daterange">
														<input type="text" id="period_from" name="period_from" value="<?php echo $period_from ?>" class="form-control">
														<span class="input-group-addon">~</span>
														<input type="text" id="period_to" name="period_to" value="<?php echo $period_to ?>" class="form-control">
													</div>
												</div>

											</td>
										</tr>
										<tr>
											<td colspan="2">
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
										<th>이름</th>
										<th>E-mail</th>
										<th>닉네임</th>
										<th>사유</th>
										<th>구분</th>
										<th>적립 포인트</th>
										<th>적립날짜</th>
										<th>만료예정</th>
										<th>잔여 포인트</th>
									</tr>
								</thead>
								<tbody>
					<?php
					if ( !empty($rows) ) {
						$list_no = $page == 1 ? $total_count : $total_count - (($page - 1) * $paginator->rows_per_page);
						
						foreach ( $rows as $key => $val ) {
							$point_amount = $val['point_amount'];
							
							$pay_amount = $val['payment_amount'];
							$pay_method = $val['payment_method'];
							$pay_state = $val['payment_state'];
							$created_dt = $val['created_dt'];
							$payment_dt = $val['payment_dt'];
							$total_point = $val['total_point'];
							
							$user_name = $val['user_name'];
							$user_email = $val['user_email'];
							$display_name = $val['display_name'];
					?>
									<tr>
										<td><?php echo $user_name ?></td>
										<td><?php echo $user_email ?></td>
										<td><?php echo $display_name ?></td>
										<td>
											<?php echo $wps_payment_method[$pay_method] ?><br>
											<?php echo number_format($pay_amount) ?>
										</td>
										<td><?php echo $wps_payment_state[$pay_state] ?></td>
										<td class="text-red"><?php echo number_format($point_amount) ?></td>
										<td><?php echo $payment_dt ?></td>
										<td>???</td>
										<td><?php echo number_format($total_point) ?></td>
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

				// 단축아이콘
			    $(".wps-date").click(function(e) {
				    e.preventDefault();

				    var period = $(this).attr("title");
				    var dt = new Date();
				    var from, to, fmonth, fdate, tmonth, tdate = "";

				    // 오늘
				    tmonth = dt.getMonth() + 1;
				    tdate =  dt.getDate();

				    if (tmonth < 10) {
					    tmonth = "0" + tmonth;
				    }
				    if (tdate < 10) {
					    tdate = "0" + tdate;
				    }
				    to = dt.getFullYear() + "-" + tmonth + "-" + tdate;

				    // period 전
				    dt.setDate(dt.getDate() - period);

				    fmonth = dt.getMonth() + 1;
				    fdate =  dt.getDate();

				    if (fmonth < 10) {
					    fmonth = "0" + fmonth;
				    }
				    if (fdate < 10) {
					    fdate = "0" + fdate;
				    }
				    
				    from = dt.getFullYear() + "-" + fmonth + "-" + fdate;

				    $("#period_from").val(from);
				    $("#period_to").val(to);

// 				    $("#search-form").submit();
				    
				});
			});
			</script>

<?php 
require_once ADMIN_PATH . '/admin-footer.php';
?>