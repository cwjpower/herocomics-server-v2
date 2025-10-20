<?php
require_once '../wps-config.php';

require_once FUNC_PATH . '/functions-book.php';
require_once FUNC_PATH . '/functions-payment.php';
require_once FUNC_PATH . '/functions-activity.php';


// 누적 판매 부수
$total_sale_book = lps_get_total_sale_book();

// 오늘 판매 부수
$today_sale_book = lps_get_today_sale_book();
// SNS 공유 수
$sns_share_count = lps_get_share_count();
// 오늘 가입한 회원
$today_user_count = lps_get_today_join_user();


// 베스트 장르
$best_genre_book = lps_get_best_genre_book();
$best_genre_title = @$best_genre_book['name'];

// 베스트 단품
$best_selling_book = lps_get_best_selling_book();
$best_book_title = @$best_selling_book['book_title'];

// 베스트 세트
$best_selling_set = lps_get_best_selling_set();
$best_set_title = @$best_selling_set['book_title'];

// 커뮤니티 랭킹 1위
$best_ranking_book = lps_get_best_ranking_book();
$best_ranking_title = @$best_ranking_book['book_title'];

// 승인 완료된 책
$total_accepted_book = lps_get_total_accepted_book();
// 등록 요청 중인 책
$total_waiting_new_book = lps_get_total_waiting_new_book();
// 수정, 삭제 요청 중인 책
$total_waiting_update_book = lps_get_total_waiting_update_book();
// 대기중 1:1 문의
$total_waiting_qna = lps_get_total_waiting_qna();

// 매출 7일 간
$period_from = empty($_GET['period_from']) ? date('Y-m-d', time() - 84600 * 7) : $_GET['period_from'];
$period_to = empty($_GET['period_to']) ? date('Y-m-d') : $_GET['period_to'];

$period_from_hms = $period_from . ' 00:00:00';
$period_to_hms = $period_to . ' 23:59:59';

// 결제 통계
$query = "
		SELECT
			SUBSTRING(created_dt, 1, 10) AS payment_dt,
			COUNT(*) AS count,
			SUM(total_amount) AS sub_sum
		FROM
			bt_order
		WHERE
			order_status = '9' AND
		    created_dt BETWEEN ? AND ?
		GROUP BY
			1
		ORDER BY
			1 ASC
";

$stmt = $wdb->prepare( $query );
$stmt->bind_param('ss', $period_from_hms, $period_to_hms);
$stmt->execute();
$result = $wdb->get_results($stmt);

if (!empty($result)) {
	$stat_arr = [];
	foreach ($result as $key => $val) {
		$dt = $val['payment_dt'];
		$subc = $val['count'];
		$subs = $val['sub_sum'];
		array_push($stat_arr, "{ period: '$dt', value: $subs }");
	}
	$stat_data = implode(',', $stat_arr);
}


require_once ADMIN_PATH . '/admin-header.php';

?>

			<link rel="stylesheet" href="<?php echo ADMIN_URL ?>/css/morris.css">
			<script src="<?php echo ADMIN_URL ?>/js/raphael-min.js"></script>
			<script src="<?php echo ADMIN_URL ?>/js/morris.min.js"></script>
			
			<!-- Left side column. contains the logo and sidebar -->
			<aside class="main-sidebar">
				<!-- sidebar: style can be found in sidebar.less -->
				<section class="sidebar" style="height: auto;" id="scrollspy">
					<!-- Sidebar user panel -->
					<div class="user-panel">
						<div class="pull-left image">
							<img src="<?php echo IMG_URL ?>/common/photo-default.png" class="img-circle" alt="User Image">
						</div>
						<div class="pull-left info" style="width: 70%;">
							<div class="pull-right"><a href="<?php echo ADMIN_URL ?>/users/profile.php"><i class="fa fa-gear"></i></a></div>
							<div class="pull-left"><?php echo wps_get_current_user_name() ?></div>
							<div style="clear: both; padding-top: 10px;"><a href="<?php echo ADMIN_URL ?>/logout.php" class="label label-warning">로그아웃</a></div>
						</div>
					</div>
				</section>
				<!-- /.sidebar -->
			</aside>
			<!-- /.main-sidebar -->
			
			<!-- Content Wrapper. Contains page content -->
			<div class="content-wrapper">
				<!-- Content Header (Page header) -->
				<section class="content-header">
					<h1>북톡 현황판</h1>
				</section>

				<!-- Main content -->
				<section class="content">
					<div class="row">
						<div class="col-md-3 col-sm-6 col-xs-12">
							<div class="info-box">
							<span class="info-box-icon bg-aqua"><i class="fa fa-fw fa-book"></i></span>
				
							<div class="info-box-content">
								<span class="info-box-text">누적 판매 부수</span>
								<span class="info-box-number"><?php echo number_format($total_sale_book) ?><small>권</small></span>
							</div>
							<!-- /.info-box-content -->
							</div>
							<!-- /.info-box -->
						</div>
						<!-- /.col -->
						<div class="col-md-3 col-sm-6 col-xs-12">
							<div class="info-box">
							<span class="info-box-icon bg-red"><i class="glyphicon glyphicon-book"></i></span>
				
							<div class="info-box-content">
								<span class="info-box-text">오늘 판매 부수</span>
								<span class="info-box-number"><?php echo number_format($today_sale_book) ?><small>권</small></span>
							</div>
							<!-- /.info-box-content -->
							</div>
							<!-- /.info-box -->
						</div>
						<!-- /.col -->
				
						<!-- fix for small devices only -->
						<div class="clearfix visible-sm-block"></div>
				
						<div class="col-md-3 col-sm-6 col-xs-12">
							<div class="info-box">
							<span class="info-box-icon bg-green"><i class="fa fa-fw fa-facebook-official"></i></span>
				
							<div class="info-box-content">
								<span class="info-box-text">SNS 공유 수</span>
								<span class="info-box-number"><?php echo number_format($sns_share_count) ?><small>건</small></span>
							</div>
							<!-- /.info-box-content -->
							</div>
							<!-- /.info-box -->
						</div>
						<!-- /.col -->
						<div class="col-md-3 col-sm-6 col-xs-12">
							<div class="info-box">
							<span class="info-box-icon bg-yellow"><i class="ion ion-ios-people-outline"></i></span>
				
							<div class="info-box-content">
								<span class="info-box-text">새로운 회원</span>
								<span class="info-box-number"><?php echo number_format($today_user_count) ?><small>명</small></span>
							</div>
							<!-- /.info-box-content -->
							</div>
							<!-- /.info-box -->
						</div>
						<!-- /.col -->
					</div>
					
					<div class="row">
						<div class="col-md-12">
							<div class="box">
								<div class="box-header with-border">
									<h4>매출 요약</h4>
								</div><!-- /.box-header -->
								<div class="box-body">
									<div class="col-md-6">
										<div class="box">
											<div class="box-body chart-responsive">
												
												<div id="morrisChartPayment" style="height: 250px;"></div>
												
											</div>
										</div>
									</div><!-- /.col-md-8 -->
									
									<div class="col-md-3">
										<!-- Info Boxes Style 2 -->
										<div class="info-box bg-green">
											<span class="info-box-icon"><i class="fa fa-fw fa-cubes"></i></span>
											<div class="info-box-content">
												<span class="info-box-text">베스트 장르(중분류)</span>
												<span class="info-box-number"><?php echo $best_genre_title ?></span>
											</div><!-- /.info-box-content -->
										</div><!-- /.info-box -->
										<!-- Info Boxes Style 2 -->
										<div class="info-box bg-green">
											<span class="info-box-icon"><i class="fa fa-fw fa-book"></i></span>
											<div class="info-box-content">
												<span class="info-box-text">베스트 단품</span>
												<span class="info-box-number"><?php echo $best_book_title ?></span>
											</div><!-- /.info-box-content -->
										</div><!-- /.info-box -->
										<!-- Info Boxes Style 2 -->
										<div class="info-box bg-aqua">
											<span class="info-box-icon"><i class="fa fa-fw fa-book"></i></span>
											<div class="info-box-content">
												<span class="info-box-text">승인 완료된 책</span>
												<span class="info-box-number"><?php echo number_format($total_accepted_book) ?><small>권</small></span>
											</div><!-- /.info-box-content -->
										</div><!-- /.info-box -->
										<!-- Info Boxes Style 2 -->
										<div class="info-box bg-red">
											<span class="info-box-icon"><i class="fa fa-fw fa-bullhorn"></i></span>
											<div class="info-box-content">
												<span class="info-box-text">수정,삭제 요청 중인 책</span>
												<span class="info-box-number"><?php echo number_format($total_waiting_update_book) ?><small>권</small></span>
											</div><!-- /.info-box-content -->
										</div><!-- /.info-box -->
									</div><!-- /.col-md-4 -->
								
									<div class="col-md-3">
										<!-- Info Boxes Style 2 -->
										<div class="info-box bg-aqua">
											<span class="info-box-icon"><i class="fa fa-fw fa-commenting-o"></i></span>
											<div class="info-box-content">
												<span class="info-box-text">커뮤니티 랭킹 1위</span>
												<span class="info-box-number"><?php echo $best_ranking_title ?></span>
											</div><!-- /.info-box-content -->
										</div><!-- /.info-box -->
										<!-- Info Boxes Style 2 -->
										<div class="info-box bg-green">
											<span class="info-box-icon"><i class="fa fa-fw fa-book"></i></span>
											<div class="info-box-content">
												<span class="info-box-text">베스트 세트</span>
												<span class="info-box-number"><?php echo $best_set_title ?></span>
											</div><!-- /.info-box-content -->
										</div><!-- /.info-box -->
										<!-- Info Boxes Style 2 -->
										<div class="info-box bg-red">
											<span class="info-box-icon"><i class="fa fa-fw fa-bullhorn"></i></span>
											<div class="info-box-content">
												<span class="info-box-text">등록 요청 중인 책</span>
												<span class="info-box-number"><?php echo number_format($total_waiting_new_book) ?><small>권</small></span>
											</div><!-- /.info-box-content -->
										</div><!-- /.info-box -->
										<!-- Info Boxes Style 2 -->
										<div class="info-box bg-yellow">
											<span class="info-box-icon"><i class="fa fa-fw fa-question-circle"></i></span>
											<div class="info-box-content">
												<span class="info-box-text">대기중 1:1 문의</span>
												<span class="info-box-number"><?php echo number_format($total_waiting_qna) ?><small>건</small></span>
											</div><!-- /.info-box-content -->
										</div><!-- /.info-box -->
									</div><!-- /.col-md-4 -->
								
								</div><!-- /.box-body -->
							</div><!-- /.body -->
						</div><!-- /.col-md-12 -->
					</div><!-- /.row -->
				</section><!-- /.content -->
			</div><!-- /.content-wrapper -->


			<script>
			// 결제
			var statData = [ <?php echo $stat_data ?> ];
			new Morris.Line({
				element: 'morrisChartPayment',
				data: statData,
				xkey: 'period',
				ykeys: ['value'],
				labels: ['결제금액']
			});
			</script>
<?php 
require_once ADMIN_PATH . '/admin-footer.php';
?>