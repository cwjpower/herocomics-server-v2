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
			<script src="<?php echo ADMIN_URL ?>/js/jquery.flot.js"></script>
			<script src="<?php echo ADMIN_URL ?>/js/jquery.flot.pie.js"></script>
			
			<!-- Content Wrapper. Contains page content -->
			<div class="content-wrapper">
				<!-- Content Header (Page header) -->
				<div class="content-header">
					<h1>
						도서별
					</h1>
					<ol class="breadcrumb">
						<li><a href="<?php echo ADMIN_URL ?>/admin.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li>통계</li>
						<li class="active"><b>도서별</b></li>
					</ol>
				</div>

				<!-- Main content -->
				<div class="content body">
					<div class="row">
						<div class="col-md-4">
							<div class="box box-primary">
								<div class="box-header">
									<h4>그룹별 현황</h4>
								</div>
								<div class="box-body">
									<div id="chart-gender" style="height: 200px;"></div>
								</div>
							</div><!-- /.box -->
						</div>
						<div class="col-md-8">
							<div class="box box-success">
								<div class="box-header">
									<h4>그룹별 현황</h4>
								</div>
								<div class="box-body">
									<div id="chart-age" style="height: 200px;"></div>
								</div>
							</div><!-- /.box -->
						</div>
					</div><!-- /.row -->
					
					<div class="row">
						<div class="col-md-6">
							<div class="box box-warning">
								<div class="box-header">
									<h4>지역</h4>
								</div>
								<div class="box-body">
									<div id="chart-area" style="height: 300px;"></div>
								</div>
							</div><!-- /.box -->
						</div>
						<div class="col-md-6">
							<div class="box box-warning">
								<div class="box-header">
									<h4>학력</h4>
								</div>
								<div class="box-body">
									<div id="chart-school" style="height: 300px;"></div>
								</div>
							</div><!-- /.box -->
						</div>
					</div>
					
				</div><!-- /.content -->
			</div><!-- /.content-wrapper -->

			<!-- InputMask -->
			<script src="<?php echo ADMIN_URL ?>/js/jquery/input-mask/jquery.inputmask.js"></script>
			<script src="<?php echo ADMIN_URL ?>/js/jquery/input-mask/jquery.inputmask.date.extensions.js"></script>
			<script src="<?php echo ADMIN_URL ?>/js/jquery/input-mask/jquery.inputmask.extensions.js"></script>
			
			<script>
			// 도넛 
			Morris.Donut({
				element: 'chart-gender',
				data: [
					{value: 61, label: '남자'},
					{value: 39, label: '여자'}
				],
				colors: [
			 		'#3030ff',
			 		'#ff0000'
			 	],
				formatter: function (x) { return x + "%"}
			});


			xbarColor = ["#ff4646", "#20f806", "#ff30ff", "#ff6c6c", "#5757ff", "#f8df07"];
			// Use Morris.Bar
			Morris.Bar({
				element: 'chart-age',
				data: [
					{x: '19세 이하', y: 10},
					{x: '20대', y: 20},
					{x: '30대', y: 30},
					{x: '40대', y: 20},
					{x: '50대', y: 10},
					{x: '60세 이상', y: 5}
				],
				xkey: 'x',
				ykeys: ['y'],
				labels: ['비율'],
				barColors: function (row, series, type) {
					return xbarColor[row.x];
				}
			});
			
			</script>
			
			<script>
			$(function() {
				// flotcharts.org, 지역
				var data = [];

				var chartArea = $("#chart-area");

				var dataArea = ["서울", "광주", "부산", "대구", "경기"];
				var dataAreaRate = [32, 7, 12, 9, 40];
				
				for (var i = 0; i < dataArea.length; i++) {
					data[i] = {
						label: dataArea[i],
						data: dataAreaRate[i]
					}
				}
	
				$.plot(chartArea, data, {
					series: {
						pie: { 
							show: true
						}
					}
				});
				
				// flotcharts.org, 학력
				var data2 = [];

				var chartSchool = $("#chart-school");

				var dataSchool = ["대학교", "전문대학", "고등학교", "대학원", "중학교"];
				var dataSchoolRate = [28, 11, 20, 6, 35];
				
				for (var i = 0; i < dataSchool.length; i++) {
					data2[i] = {
						label: dataSchool[i],
						data: dataSchoolRate[i]
					}
				}
	
				$.plot(chartSchool, data2, {
					series: {
						pie: { 
							show: true
						}
					}
				});

			});
			
			</script>

<?php 
require_once ADMIN_PATH . '/admin-footer.php';
?>