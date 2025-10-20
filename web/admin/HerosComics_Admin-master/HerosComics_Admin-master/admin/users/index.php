<?php 
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
		$sql = " AND ( user_login = ? OR user_name LIKE ? OR user_email LIKE ? OR display_name LIKE ? OR mobile LIKE ? ) ";
		array_push( $sparam, $q, '%' . $q . '%', '%' . $q . '%', '%' . $q . '%', '%' . $q . '%' );
	}
} else {
	if ( $q != '' ) {
		if ( !strcmp($qa, 'user_login') ) {
			$sql = " AND $qa = ?";
			array_push( $sparam, $q );
		} else {
			$sql = " AND $qa LIKE ?";
			array_push( $sparam, '%' . $q . '%' );
		}
	}
}

// Advanced search
$disp = empty($_GET['disp']) ? 'hide' : '';	// 상세검색 노출 여부
$today = date('Y-m-d');
$from_logged = empty($_GET['from_logged']) ? '' : $_GET['from_logged'];
$to_logged = empty($_GET['to_logged']) ? $today: $_GET['to_logged'];
$from_registered = empty($_GET['from_registered']) ? '' : $_GET['from_registered'];
$to_registered = empty($_GET['to_registered']) ? $today : $_GET['to_registered'];
$from_age = empty($_GET['from_age']) ? '' : $_GET['from_age'];
$to_age = empty($_GET['to_age']) ? '' : $_GET['to_age'];

$fulevel = empty($_GET['ulevel']) ? array(): $_GET['ulevel'];
$fgender = empty($_GET['gender']) ? array(): $_GET['gender'];
$fjpath = empty($_GET['jpath']) ? array(): $_GET['jpath'];

if ( !empty($from_logged) && !empty($to_logged) ) {
	$from_logged_deep = $from_logged . ' 00:00:00';
	$to_logged_deep = $to_logged . ' 23:59:59';
	$sql .= " AND last_login_dt >= ? AND last_login_dt <= ? ";
	array_push( $sparam, $from_logged_deep, $to_logged_deep );
}

if ( !empty($from_registered) && !empty($to_registered) ) {
	$from_registered_deep = $from_registered . ' 00:00:00';
	$to_registered_deep = $to_registered . ' 23:59:59';
	$sql .= " AND user_registered >= ? AND user_registered <= ? ";
	array_push( $sparam, $from_registered_deep, $to_registered_deep );
}

if ( !empty($from_age) && !empty($to_age) ) {
	$from_age_deep = date('Y') - $from_age . '-01-01';
	$to_age_deep = date('Y') - $to_age . '-12-31';
	$sql .= " AND birthday BETWEEN ? AND ? ";
	array_push( $sparam, $to_age_deep, $from_age_deep );
}

if ( !empty($fulevel) ) {
	$impsql = '';

	foreach ( $fulevel as $key => $val ) {
		$impsql .= "OR user_level = ? ";
		array_push($sparam, $val);
	}
	$sql .= ' AND (' . substr($impsql, 3) . ')';
}

if ( !empty($fgender) ) {
	$impsql = '';
	
	foreach ( $fgender as $key => $val ) {
		$impsql .= "OR gender = ? ";
		array_push($sparam, $val);
	}
	$sql .= ' AND (' . substr($impsql, 3) . ')';
	
}

if ( !empty($fjpath) ) {
	$impsql = '';
	
	foreach ( $fjpath as $key => $val ) {
		$impsql .= "OR join_path = ? ";
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

$query = "
	SELECT
		*
	FROM
		bt_users
	WHERE
		user_status <> '4'
		$sql
	ORDER BY
		ID DESC
";
$paginator = new WpsPaginator($wdb, $page);
$rows = $paginator->ls_init_pagination( $query, $sparam );
$total_count = $paginator->ls_get_total_rows();
$total_records = $paginator->ls_get_total_records();

require_once ADMIN_PATH . '/admin-header.php';

require_once './users-lnb.php';
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
						회원정보 조회
					</h1>
					<ol class="breadcrumb">
						<li><a href="<?php echo ADMIN_URL ?>/admin.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li><a href="<?php echo ADMIN_URL ?>/users/">회원관리</a></li>
						<li class="active">회원정보 조회</li>
					</ol>
				</div>

				<!-- Main content -->
				<div class="content body">
					<div class="box box-info">
						<form id="simple-search-form" class="form-horizontal">
							<div class="box-body">
								<div class="row">
									<div class="col-sm-2"></div>
									<div class="col-sm-8">
										<div class="form-group">
											<label class="col-sm-2 control-label">회원정보</label>
											<div class="col-sm-3">
												<select name="qa" class="form-control">
													<option value="">전체</option>
													<option value="user_name" <?php echo strcmp($qa, 'user_name') ? '' : 'selected'; ?>>이름</option>
													<option value="display_name" <?php echo strcmp($qa, 'display_name') ? '' : 'selected'; ?>>닉네임</option>
													<option value="user_login" <?php echo strcmp($qa, 'user_login') ? '' : 'selected'; ?>>아이디(이메일)</option>
													<option value="mobile" <?php echo strcmp($qa, 'mobile') ? '' : 'selected'; ?>>휴대전화번호</option>
													<!-- option value="user_email" <?php echo strcmp($qa, 'user_email') ? '' : 'selected'; ?>>이메일주소</option> -->
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
												<button type="button" id="simple-reset-btn" class="btn btn-default">초기화</button> &nbsp;
												<button type="button" id="adv-display-btn" class="btn btn-success btn-sm">상세검색</button>
											</div>
										</div>
									</div>
									<div class="col-sm-2"></div>
								</div>
							</div>
						</form>
						
						<form id="adv-search-form" class="form-horizontal <?php echo $disp ?>">
							<input type="hidden" id="disp" name="disp" value="1">
							<div class="box-footer">
								<div class="col-sm-1"></div>
								<div class="col-sm-9">
									<table class="table table-bordered ls-table">
										<colgroup>
											<col style="width: 15%;">
											<col style="width: 35%;">
											<col style="width: 15%;">
											<col style="width: 35%;">
										</colgroup>
										<tbody>
											<tr>
												<td class="item-label">회원등급</td>
												<td>
													<label><input type="checkbox" id="switch-ulevel"> 전체</label> &nbsp;
									<?php 
									foreach ($wps_user_level as $key => $val ) {
										$checked = in_array($key, $fulevel) ? 'checked' : '';
									?>
													<label><input type="checkbox" name="ulevel[]" value="<?php echo $key ?>" <?php echo $checked ?>> <?php echo $val ?></label> &nbsp;
									<?php 
									}
									?>
												</td>
												<td class="item-label">접속일</td>
												<td>
													<div class="input-group datepicker input-daterange">
														<input type="text" id="from_logged" name="from_logged" value="<?php echo $from_logged ?>" class="form-control">
														<span class="input-group-addon">~</span>
														<input type="text" id="to_logged" name="to_logged" value="<?php echo $to_logged ?>" class="form-control">
													</div>
												</td>
											</tr>
											<tr>
												<td class="item-label">가입일</td>
												<td>
													<div class="input-group datepicker input-daterange">
														<input type="text" id="from_registered" name="from_registered" value="<?php echo $from_registered ?>" class="form-control datepicker actual_range">
														<span class="input-group-addon">~</span>
														<input type="text" id="to_registered" name="to_registered" value="<?php echo $to_registered ?>" class="form-control datepicker actual_range">
													</div>
												</td>
												<td class="item-label">나이</td>
												<td>
													<div class="input-group">
														<input type="text" name="from_age" class="form-control numeric" maxlength="3" value="<?php echo $from_age ?>">
														<div class="input-group-addon">
															세
														</div>
														<div class="input-group-addon">
															~
														</div>
														<input type="text" name="to_age" class="form-control numeric" maxlength="3" value="<?php echo $to_age ?>">
														<div class="input-group-addon">
															세
														</div>
													</div>
												</td>
											</tr>
											<tr>
												<td class="item-label">성별</td>
												<td>
										<?php 
										foreach ( $wps_user_gender as $key => $val ) {
											if ( !empty($key) ) {
												$checked = in_array($key, $fgender) ? 'checked' : ''; 
										?>
													<label><input type="checkbox" name="gender[]" value="<?php echo $key ?>" <?php echo $checked ?>> <?php echo $val ?></label> &nbsp; &nbsp;
										<?php 
											}
										}
										?> 
												</td>
												<td class="item-label">가입경로</td>
												<td>
										<?php 
										foreach ( $wps_user_join_path as $key => $val ) {
											if ( !empty($key) ) {
												$checked = in_array($key, $fjpath) ? 'checked' : '';
										?>
													<label><input type="checkbox" name="jpath[]" value="<?php echo $key ?>" <?php echo $checked ?>> <?php echo $val ?></label> &nbsp; &nbsp;
										<?php 
											}
										}
										?> 
												</td>
											</tr>
										</tbody>
									</table>
									<div class="text-center">
										<div class="pull-right">
											<a href="#" id="download-excel" class="btn btn-success btn-sm"><i class="fa fa-fw fa-download"></i> Excel 다운로드</a>
										</div>
										<button type="submit" class="btn btn-info">검색</button> &nbsp; &nbsp;
										<button type="button" id="adv-reset-btn" class="btn btn-default">초기화</button>
									</div>
								</div>
								<div class="col-sm-2"></div>
							</div>
						</form>
					</div>

					<div class="box box-primary">
						<div class="box-header">
							<div>
								<i class="fa fa-circle-o text-red"></i> Total: <b><?php echo number_format($total_records) ?></b>  &nbsp;
								<i class="fa fa-circle-o text-yellow"></i> 검색: <b><?php echo number_format($total_count) ?></b>
							</div>
							<div class="box-tools">
								현재 접속자만 보기
								<button type="button" class="btn btn-default btn-sm checkbox-toggle">OFF</button>
							</div>
						</div>
						<div class="box-body">
							<table class="table table-striped table-hover">
								<thead>
									<tr class="info">
										<th>이름</th>
										<th>닉네임</th>
										<th>등급</th>
										<th>나이</th>
										<th>성별</th>
										<th>상태</th>
										<th>휴대폰번호</th>
										<th>아이디(이메일 주소)</th>
									</tr>
								</thead>
								<tbody>
					<?php
					if ( !empty($rows) ) {
						$list_no = $page == 1 ? $total_count : $total_count - (($page - 1) * $paginator->rows_per_page);
	
						foreach ( $rows as $key => $val ) {
							$user_id = $val['ID'];
							$user_login = $val['user_login'];
							$user_name = $val['user_name'];
// 							$user_email = $val['user_email'];
							$display_name = $val['display_name'];
							$user_status = $val['user_status'];
							$user_registered = $val['user_registered'];
							$user_level = $val['user_level'];

							$mobile = $val['mobile'];
							$gender = $val['gender'];
							$age = empty($val['birthday']) ? '-' : date('Y') - intval($val['birthday']);
							
							$status_label = '차단';
							$tr_bg_class = '';
							$hide = '';		// 탈퇴회원일 경우, "편집/차단" 메뉴 감춤
					?>
									<tr>
										<td><a href="user_index.php?id=<?php echo $user_id ?>"><?php echo $user_name ?></a></td>
										<td><a href="user_index.php?id=<?php echo $user_id ?>"><?php echo $display_name ?></a></td>
										<td><?php echo $wps_user_level[$user_level] ?></td>
										<td><?php echo $age ?></td>
										<td><?php echo $wps_user_gender[$gender] ?></td>
										<td><?php echo $wps_user_status[$user_status] ?></td>
										<td><?php echo $mobile ?></td>
										<td><?php echo $user_login ?></td>
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
					</div>

				</div><!-- /.content -->
			</div><!-- /.content-wrapper -->

			<!-- InputMask -->
			<script src="<?php echo ADMIN_URL ?>/js/jquery/input-mask/jquery.inputmask.js"></script>
			<script src="<?php echo ADMIN_URL ?>/js/jquery/input-mask/jquery.inputmask.date.extensions.js"></script>
			<script src="<?php echo ADMIN_URL ?>/js/jquery/input-mask/jquery.inputmask.extensions.js"></script>
			<!-- Numeric -->
			<script src="<?php echo INC_URL ?>/js/jquery/jquery.numeric.min.js"></script>
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
	
			    $(".checkbox-toggle").click(function () {
					var clicks = $(this).data('clicks');
					if (clicks) {
						$(this).removeClass("btn-success").addClass('btn-default');
						$(this).html("OFF");
						//Uncheck all checkboxes
	// 					$(".fa", this).removeClass("fa-check-square-o").addClass('fa-square-o');
					} else {
						$(this).removeClass("btn-default").addClass('btn-success');
						$(this).html("ON");
						//Check all checkboxes
	// 					$(".fa", this).removeClass("fa-square-o").addClass('fa-check-square-o');
					}
					$(this).data("clicks", !clicks);
				});

			    $("#adv-display-btn").click(function() {
					if ( $("#adv-search-form").hasClass("hide") ) {
						$("#adv-search-form").removeClass("hide");
						$("#disp").val(1);
					} else {
						$("#adv-search-form").addClass("hide");
						$("#disp").val(0);
					}
				});
			    
				$("#simple-reset-btn").click(function() {
					$("#simple-search-form :input").val("");
				});
				
				$("#adv-reset-btn").click(function() {
					$("#adv-search-form :input").val("");
					$("#adv-search-form :checkbox").prop( "checked", false );
					$("#disp").val("1");
				});

				// 회원등급 전체선택/해제
				$("#switch-ulevel").click(function() {
					$('input[name="ulevel[]"]').prop("checked", $(this).prop("checked"));
				});

				$(".numeric").numeric();

				$("#download-excel").click(function(e) {
					e.preventDefault();
					location.href = "download_user.php" + location.search;
				});
			});
			</script>

<?php 
require_once ADMIN_PATH . '/admin-footer.php';
?>