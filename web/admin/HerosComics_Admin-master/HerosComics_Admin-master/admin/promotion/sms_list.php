<?php 
require_once '../../wps-config.php';
require_once INC_PATH . '/classes/WpsPaginator.php';

$user_id = wps_get_current_user_id();

// page number
$page = empty($_GET['page']) ? 1 : $_GET['page'];

// search
$qa1 = empty($_GET['qa1']) ? '' : trim($_GET['qa1']);
$q1 = !isset($_GET['q1']) ? '' : trim($_GET['q1']);

$coupon_type = empty($_GET['coupon_type']) ? '' : $_GET['coupon_type'];
$discount_type = empty($_GET['discount_type']) ? '' : $_GET['discount_type'];
$period_from = empty($_GET['period_from']) ? '' : $_GET['period_from'];
$period_to = empty($_GET['period_to']) ? '' : $_GET['period_to'];

$period_from_hms = $period_from . ' 00:00:00';
$period_to_hms = $period_to . ' 23:59:59';

$sql = '';
$pph = '';
$sparam = [];

// 검색어
if ( !empty($qa1) && !empty($q1) ) {
	$sql .= " AND $qa1 LIKE ?";
	array_push( $sparam, '%' . $q1 . '%' );
}

// 전송 기간 검색
if ( !empty($period_from) && !empty($period_to) ) {
	$sql .= " AND created_dt BETWEEN ? AND ?";
	array_push( $sparam, $period_from_hms, $period_to_hms );
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
			*
		FROM
			bt_promotion
		WHERE 
			prom_type = 'SMS' 
			$sql
		ORDER BY
			ID DESC
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
						SMS 전송 내역
						<a href="./sms_new.php" class="btn btn-info btn-sm">SMS 작성</a>
					</h1>
					<ol class="breadcrumb">
						<li><a href="<?php echo ADMIN_URL ?>/admin.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li>프로모션</li>
						<li class="active"><b>SMS 전송 내역</b></li>
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
										<tr>
											<td class="item-label">검색어</td>
											<td>
												<div class="col-sm-6">
													<select name="qa1" class="form-control">
														<optgroup label="키워드 검색">
															<option value="">-선택-</option>
															<option value="prom_title" <?php echo strcmp($qa1, 'prom_title') ? '' : 'selected'; ?>>관리용 제목</option>
															<option value="prom_content" <?php echo strcmp($qa1, 'prom_content') ? '' : 'selected'; ?>>메시지 내용</option>
														</optgroup>
													</select>
												</div>
												<div class="col-sm-6">
													<input type="text" name="q1" value="<?php echo $q1 ?>" class="form-control">
												</div>
											</td>
										</tr>
										<tr>
											<td class="item-label">전송 기간</td>
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
												<div class="col-sm-4">
													<div class="input-group datepicker input-daterange">
														<input type="text" id="period_from" name="period_from" value="<?php echo $period_from ?>" class="form-control">
														<span class="input-group-addon">~</span>
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
										<th>#</th>
										<th>제목</th>
										<th>내용</th>
										<th>전송 회원 수</th>
										<th>전송날짜</th>
									</tr>
								</thead>
								<tbody>
					<?php
					if ( !empty($rows) ) {
						$list_no = $page == 1 ? $total_count : $total_count - (($page - 1) * $paginator->rows_per_page);
						$ymd = date('Y-m-d');
						
						foreach ( $rows as $key => $val ) {
							$ID = $val['ID'];
							
							$prom_title = $val['prom_title'];
							$prom_content = $val['prom_content'];
							$user_count = $val['user_count'];
							$created_dt = $val['created_dt'];
					?>
									<tr>
										<td><?php echo $list_no ?></td>
										<td><?php echo $prom_title ?></td>
										<td><?php echo $prom_content ?></td>
										<td><?php echo $user_count ?></td>
										<td><?php echo $created_dt ?></td>
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