<?php 
require_once '../../wps-config.php';
require_once INC_PATH . '/classes/WpsPaginator.php';

// page number
$page = empty($_GET['page']) ? 1 : $_GET['page'];

// search
$lvl = empty($_GET['user_level']) ? '' : trim($_GET['user_level']);
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

if (!empty($lvl)) {
	$sql .= " AND user_level = ?";
	array_push( $sparam, $lvl );
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

			<!-- Content Wrapper. Contains page content -->
			<div class="content-wrapper">
				<!-- Content Header (Page header) -->
				<div class="content-header">
					<h1>
						회원등급 조회 및 조정
					</h1>
					<ol class="breadcrumb">
						<li><a href="<?php echo ADMIN_URL ?>/admin.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li><a href="<?php echo ADMIN_URL ?>/users/">회원관리</a></li>
						<li><a href="#">등급관리</a></li>
						<li class="active">조회 및 조정</li>
					</ol>
				</div>

				<!-- Main content -->
				<div class="content body">
					<div class="box box-info">
						<form id="search-form" class="form-horizontal">
							<div class="box-body">
								<div class="row">
									<div class="col-sm-8">
										<div class="form-group">
											<label class="col-sm-2 control-label">회원검색</label>
											<div class="col-sm-2">
												<select name="user_level" class="form-control">
													<optgroup label="회원등급">
														<option value="">-전체-</option>
									<?php 
									foreach ($wps_user_level as $key => $val ) {
										$selected = $lvl == $key ? 'selected' : '';
									?>
														<option value="<?php echo $key ?>" <?php echo $selected ?>><?php echo $val ?></option>
									<?php 
									}
									?>
													</optgroup>
												</select>
											</div>
											<div class="col-sm-3">
												<select name="qa" class="form-control">
													<optgroup label="회원정보">
														<option value="">-전체-</option>
														<option value="user_name" <?php echo strcmp($qa, 'user_name') ? '' : 'selected'; ?>>이름</option>
														<option value="display_name" <?php echo strcmp($qa, 'display_name') ? '' : 'selected'; ?>>닉네임</option>
														<option value="user_login" <?php echo strcmp($qa, 'user_login') ? '' : 'selected'; ?>>아이디(이메일)</option>
														<option value="mobile" <?php echo strcmp($qa, 'mobile') ? '' : 'selected'; ?>>휴대전화번호</option>
														<!-- option value="user_email" <?php echo strcmp($qa, 'user_email') ? '' : 'selected'; ?>>이메일주소</option> -->
													</optgroup>
												</select>
											</div>
											<div class="col-sm-3">
												<div class="input-group input-group-sm">
													<input type="text" name="q" value="<?php echo $q ?>" class="form-control">
													<span class="input-group-btn">
														<button type="submit" class="btn btn-primary btn-flat">검색</button>
													</span>
												</div>
											</div>
											<div class="col-sm-2">
												<button type="button" id="search-reset-btn" class="btn btn-default btn-sm">초기화</button>
											</div>
										</div>
									</div>
									<div class="col-sm-4">
										<div class="pull-right">
											<a href="user_new.php" class="btn btn-info btn-sm">회원추가</a>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>

					<form id="user-list-form">
						<div class="box box-primary">
							<div class="box-header">
								<div>
									<i class="fa fa-circle-o text-red"></i> Total: <b><?php echo number_format($total_records) ?></b>  &nbsp;
									<i class="fa fa-circle-o text-yellow"></i> 검색: <b><?php echo number_format($total_count) ?></b>
								</div>
							</div>
							<div class="box-body">
								<table class="table table-striped table-hover">
									<thead>
										<tr>
											<th><input type="checkbox" id="switch-all"></th>
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
// 								$user_email = $val['user_email'];
								$display_name = $val['display_name'];
								$user_status = $val['user_status'];
								$user_registered = $val['user_registered'];
								$user_level = $val['user_level'];
	
								$mobile = $val['mobile'];
								$gender = $val['gender'];
								$age = empty($val['birthday']) ? '-' : date('Y') - intval($val['birthday']);
								
						?>
										<tr>
											<td><input type="checkbox" class="user_list" name="user_list[]" value="<?php echo $user_id ?>"></td>
											<td><a href="user_index.php?id=<?php echo $user_id ?>"><?php echo $user_name ?></a></td>
											<td><?php echo $display_name ?></td>
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
							<div class="box-footer">
								<div class="col-sm-6">
									<div class="col-sm-4">
										<select name="change_level" id="change_level" class="form-control">
											<option value="">회원등급 변경...</option>
						<?php 
						foreach ($wps_user_level as $key => $val ) {
						?>
											<option value="<?php echo $key ?>"><?php echo $val ?></option>
						<?php 
						}
						?>
										</select>
									</div>
									<div class="col-sm-3">
										<button type="button" id="btn-change-level" class="btn btn-primary btn-sm">등급변경</button>
									</div>							
								</div>
								<div class="col-sm-6">
									<?php echo $paginator->ls_bootstrap_pagination_link(); ?>
								</div>
							</div>
						</div><!-- /.box -->
					</form>

				</div><!-- /.content -->
			</div><!-- /.content-wrapper -->

			<script src="<?php echo INC_URL ?>/js/ls-util.js"></script>
			<script src="<?php echo INC_URL ?>/js/jquery/jquery.oLoader.min.js"></script>
			
			<script>
			$(function() {
				$("#switch-all").click(function() {
					var chk = $(this).prop("checked");
					$(".user_list").prop("checked", chk);
				});

				// 등급 변경
				$("#btn-change-level").click(function() {
					var chkLength = $(".user_list:checked").length;

					if (chkLength == 0) {
						alert("회원을 선택해 주십시오.");
						return;
					}
					if ($("#change_level").val() == "") {
						alert("변경하실 회원등급을 선택해 주십시오.");
						$("#change_level").focus();
						return;
					}

					showLoader();
					
					$.ajax({
						type : "POST",
						url : "./ajax/change-user-level.php",
						data : $("#user-list-form").serialize(),
						dataType : "json",
						success : function(res) {
							if (res.code == "0") {
								location.reload();
							} else {
								hideLoader();
								alert(res.msg);
							}
						}
					});
				});

				// 초기화
				$("#search-reset-btn").click(function() {
					$("#search-form :input").val("");
					$("#search-form :checkbox").prop( "checked", false );
				});
			});
			</script>

<?php 
require_once ADMIN_PATH . '/admin-footer.php';
?>