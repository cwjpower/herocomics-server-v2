<?php
require_once '../../wps-config.php';

$period_from = empty($_GET['period_from']) ? date('Y-m-d', time() - 84600 * 7) : $_GET['period_from'];
$period_to = empty($_GET['period_to']) ? date('Y-m-d') : $_GET['period_to'];

$period_from_hms = $period_from . ' 00:00:00';
$period_to_hms = $period_to . ' 23:59:59';

$query = "
		SELECT
			SUBSTRING(o.created_dt, 1, 10) AS order_dt,
			COUNT(*) AS count
		FROM
			bt_order AS o
		INNER JOIN
			bt_order_item AS i
		WHERE
			o.order_id = i.order_id AND
			o.created_dt BETWEEN ? AND ?
		GROUP BY
			SUBSTRING(o.created_dt, 1, 10)
		ORDER BY
			order_dt ASC
";
$stmt = $wdb->prepare( $query );
$stmt->bind_param('ss', $period_from_hms, $period_to_hms);
$stmt->execute();
$result = $wdb->get_results($stmt);

if (wps_is_admin()) {
	require_once ADMIN_PATH . '/admin-header.php';
} else {
	require_once ADMIN_PATH . '/agent-header.php';
}

require_once './statistics-lnb.php';

?>

			<!-- bootstrap datepicker -->
			<link rel="stylesheet" href="<?php echo ADMIN_URL ?>/css/datepicker3.css">
			<link rel="stylesheet" href="<?php echo ADMIN_URL ?>/css/morris.css">
			
			<!-- bootstrap datepicker -->
			<script src="<?php echo ADMIN_URL ?>/js/bootstrap-datepicker.js"></script>
			<script src="<?php echo ADMIN_URL ?>/js/locales/bootstrap-datepicker.kr.js"></script>
			
			<script src="<?php echo ADMIN_URL ?>/js/raphael-min.js"></script>
			<script src="<?php echo ADMIN_URL ?>/js/morris.min.js"></script>

			<!-- Content Wrapper. Contains page content -->
			<div class="content-wrapper">
				<!-- Content Header (Page header) -->
				<div class="content-header">
					<h1>
						기간별
					</h1>
					<ol class="breadcrumb">
						<li><a href="<?php echo ADMIN_URL ?>/admin.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li>통계</li>
						<li class="active"><b>기간별</b></li>
					</ol>
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
															<!-- a class="btn btn-info btn-xs wps-date" title="0">오늘</a>
															<a class="btn btn-info btn-xs wps-date" title="1">어제</a> -->
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
							<?php 
							if (!empty($result)) {
								$stat_arr = [];
								foreach ($result as $key => $val) {
									$dt = $val['order_dt'];
									$subt = $val['count'];
									array_push($stat_arr, "{ period: '$dt', value: $subt }");
								}
								$stat_data = implode(',', $stat_arr);
							}
							?>
						</div>
						<div class="box-body">
							<div id="morrisChart" style="height: 250px;"></div>
							<!-- div id="chart" style="width: 100%; height: 200px"></div> -->
						</div>
					</div><!-- /.box -->
					

				</div><!-- /.content -->
			</div><!-- /.content-wrapper -->

			<!-- InputMask -->
			<script src="<?php echo ADMIN_URL ?>/js/jquery/input-mask/jquery.inputmask.js"></script>
			<script src="<?php echo ADMIN_URL ?>/js/jquery/input-mask/jquery.inputmask.date.extensions.js"></script>
			<script src="<?php echo ADMIN_URL ?>/js/jquery/input-mask/jquery.inputmask.extensions.js"></script>
			
			<script>

// 			var statData = [
// 					{ year: '2008', value: 20 },
// 					{ year: '2009', value: 10 },
// 					{ year: '2010', value: 5 },
// 					{ year: '2011', value: 5 },
// 					{ year: '2012', value: 20 }
// 			];

			var statData = [ <?php echo $stat_data ?> ];

			new Morris.Line({
				element: 'morrisChart',
				data: statData,
				xkey: 'period',
				ykeys: ['value'],
				labels: ['판매부수']
			});
			
			</script>
			
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