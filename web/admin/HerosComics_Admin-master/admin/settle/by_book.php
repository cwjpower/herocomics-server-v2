<?php
require_once '../../wps-config.php';
require_once INC_PATH . '/classes/WpsPaginator.php';

$post_type = 'notice_new';

// page number
$page = empty($_GET['page']) ? 1 : $_GET['page'];

$period_from = empty($_GET['period_from']) ? '' : $_GET['period_from'];
$period_to = empty($_GET['period_to']) ? '' : $_GET['period_to'];

// search
$sts = empty($_GET['status']) ? '' : $_GET['status'];
$qa = empty($_GET['qa']) ? '' : trim($_GET['qa']);
$q = empty($_GET['q']) ? '' : trim($_GET['q']);
$sql = '';
$pph = '';
$sparam = [];

// Simple search
if ( empty($qa) ) {
	if ( !empty($q) ) {
		$sql = " AND ( p.post_content LIKE ? OR p.post_title LIKE ? OR p.post_name LIKE ? ) ";
		array_push( $sparam, '%'.$q.'%', '%'.$q.'%', '%'.$q.'%' );
	}
} else {
	if ( !empty($q) ) {
		if ( !strcmp($qa, 'isbn') ) {
			$sql = " AND $qa = ?";
			array_push( $sparam, $q );
		} else {
			$sql = " AND $qa LIKE ?";
			array_push( $sparam, '%'.$q.'%' );
		}
	}
}

if ($sts) {
	$sql .= " AND p.post_type_area LIKE ?";
	array_push( $sparam, '%'.$sts.'%' );
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
p.post_name,
p.post_date,
p.post_title,
p.post_parent,
p.post_status,
p.post_user_id,
p.post_email,
p.post_order,
p.post_type,
p.post_type_secondary,
p.post_type_area,
p.post_modified,
m.meta_value AS post_view_count
FROM
bt_posts AS p
LEFT JOIN
bt_posts_meta AS m
ON
p.ID = m.post_id AND
m.meta_key = 'post_view_count'
WHERE
p.post_type = '$post_type'
$sql
ORDER BY
p.post_order DESC,
p.post_modified DESC
";
$paginator = new WpsPaginator($wdb, $page);
$rows = $paginator->ls_init_pagination( $query, $sparam );
$total_count = $paginator->ls_get_total_rows();

if (wps_is_admin()) {
	require_once ADMIN_PATH . '/admin-header.php';
} else {
	require_once ADMIN_PATH . '/agent-header.php';
}

require_once './settle-lnb.php';

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
						도서별
					</h1>
				</div>

				<!-- Main content -->
				<div class="content body">
					<div class="box box-default">
						<div class="box-body">
							<div class="row">
								<div class="col-sm-12">
									<form id="search-form" class="form-horizontal">
										<table class="table table-bordered ls-table">
											<colgroup>
												<col style="width: 10%;">
												<col>
											</colgroup>
											<tbody>
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
														<div class="col-sm-4">
															<div class="input-group datepicker input-daterange">
																<input type="text" id="period_from" name="period_from" value="<?php echo $period_from ?>" class="form-control">
																<span class="input-group-addon">~</span>
																<input type="text" id="period_to" name="period_to" value="<?php echo $period_to ?>" class="form-control">
															</div>
														</div>
														<div class="col-sm-3">
															<button type="submit" class="btn btn-primary btn-flat">검색</button> &nbsp;
															<button type="button" id="reset-btn" class="btn btn-default btn-flat">초기화</button>
														</div>
		
													</td>
												</tr>
											</tbody>
										</table>
									</form>
								</div>
							</div><!-- /.row -->
						</div>
					</div><!-- /.box -->

					<div class="box box-primary">
						<div class="box-header">
							<i class="fa fa-circle-o text-yellow"></i> Total: <b><?php echo number_format($total_count) ?></b>
						</div>
						<div class="box-body">
							<table class="table table-striped table-hover">
								<thead>
									<tr>
										<th>날짜</th>
										<th>주문수</th>
										<th>책 권수</th>
										<th>주문금액</th>
										<th>할인가</th>
										<th>포인트</th>
										<th>캐시</th>
										<th>결제금액</th>
									</tr>
								</thead>
								<tbody>
					
								</tbody>
								<tfoot>
									<tr>
										<td>결제합계</td>
										<td></td>
									</tr>
									<tr>
										<td>환불합계</td>
										<td></td>
									</tr>
									<tr>
										<td>수수료</td>
										<td></td>
									</tr>
									<tr>
										<td>순매출</td>
										<td></td>
									</tr>
								</tfoot>
							</table>
						</div>
					</div><!-- /.box -->

				</div><!-- /.content -->
			</div><!-- /.content-wrapper -->

			
			<script>
			$(function() {
				$("#reset-btn").click(function() {
					$("#search-form :input").val("");
				});
				
				//Date picker
				$('.datepicker').datepicker({
			    	autoclose: true,
			    	language: 'kr',
			    	format: 'yyyy-mm-dd'
				});
				
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