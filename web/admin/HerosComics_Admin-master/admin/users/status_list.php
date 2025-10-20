<?php 
require_once '../../wps-config.php';
require_once INC_PATH . '/classes/WpsPaginator.php';

// page number
$page = empty($_GET['page']) ? 1 : $_GET['page'];

// search
$sts = !isset($_GET['status']) ? -1 : trim($_GET['status']);
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

if ($sts > -1) {
	if ( $sts == 0 ) {
		$today = date('Y-m-d');
		$sql .= " AND user_registered BETWEEN '$today 00:00:00' AND '$today 23:59:59' ";
	} else {
		$sql .= " AND user_status = ?";
		array_push( $sparam, $sts );
	}
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
		user_status <> 8
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
						신규,탈퇴,블랙 회원 관리
					</h1>
					<ol class="breadcrumb">
						<li><a href="<?php echo ADMIN_URL ?>/admin.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li><a href="<?php echo ADMIN_URL ?>/users/">회원관리</a></li>
						<li class="active">신규,탈퇴,블랙</li>
					</ol>
				</div>

				<!-- Main content -->
				<div class="content body">
				
					<div class="well">
						<a href="<?php echo $_SERVER['PHP_SELF'] ?>" class="btn <?php echo $sts == -1 ? 'btn-success' : 'btn-info'; ?> btn-flat">전체</a>
		<?php 
		foreach ($wps_user_state as $key => $val) {
			$str = strip_tags($val);
		?>
						<a href="?status=<?php echo $key ?>" class="btn <?php echo $sts == $key ? 'btn-success' : 'btn-info'; ?> btn-flat"><?php echo $str ?></a>
		<?php
		}
		?>
					</div>
					
					<div class="box box-info">
						<form id="search-form" class="form-horizontal">
							<input type="hidden" name="status" value="<?php echo $sts ?>">
							<div class="box-body">
								<div class="row">
									<div class="col-sm-6">
										<div class="form-group">
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
											<div class="col-sm-4">
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
								</div>
							</div>
						</form>
					</div>

					<div class="box box-primary">
						<form id="user-list-form">
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
											<td><a href="user_index.php?id=<?php echo $user_id ?>"><?php echo $user_name ?></a></td>
											<td><a href="user_index.php?id=<?php echo $user_id ?>"><?php echo $display_name ?></a></td>
											<td><?php echo $wps_user_level[$user_level] ?></td>
											<td><?php echo $age ?></td>
											<td><?php echo $wps_user_gender[$gender] ?></td>
											<td><?php echo $wps_user_status[$user_status] ?></td>
											<td><?php echo $mobile ?></td>
											<td><a href="user_index.php?id=<?php echo $user_id ?>"><?php echo $user_login ?></a></td>
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
								<div class="text-center">
									<?php echo $paginator->ls_bootstrap_pagination_link(); ?>
								</div>
							</div>
						</form>
					</div><!-- /.box -->

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