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

$one_year_ago = date('Y-m-d', time() - (86400 * 0));
// $one_year_ago = date('Y-m-d', time() - (86400 * 365));
$one_year_ago .= ' 00:00:00';

$query = "
	SELECT
		u.*,
		COUNT(ml.user_id) AS mail_count
	FROM
		bt_users AS u
	LEFT JOIN
		bt_mail_logs AS ml
	ON
		u.ID = ml.user_id AND
	    ml.mail_type = 'dormancy_notice'
	WHERE
		u.user_level < 10 AND
		u.last_login_dt < '$one_year_ago' 
		$sql
	GROUP BY
		ml.user_id
	ORDER BY
		u.ID DESC
";
$paginator = new WpsPaginator($wdb, $page, 100);
$rows = $paginator->ls_init_pagination( $query, $sparam );
$total_count = $paginator->ls_get_total_rows();

require_once ADMIN_PATH . '/admin-header.php';

require_once './users-lnb.php';
?>

			<!-- Content Wrapper. Contains page content -->
			<div class="content-wrapper">
				<!-- Content Header (Page header) -->
				<div class="content-header">
					<h1>
						휴면 계정 관리
					</h1>
					<ol class="breadcrumb">
						<li><a href="<?php echo ADMIN_URL ?>/admin.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li><a href="<?php echo ADMIN_URL ?>/users/">회원관리</a></li>
						<li class="active">휴면 계정 관리</li>
					</ol>
				</div>

				<!-- Main content -->
				<div class="content body">
					<div class="box box-info">
						<form id="search-form" class="form-horizontal">
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
						<form id="user-list-form" method="post" action="dormancy_list_email.php">
							<div class="box-header">
								<div class="col-md-6">
									<i class="fa fa-circle-o text-yellow"></i> Total: <b><?php echo number_format($total_count) ?></b>
								</div>
								<div class="col-md-6 text-right">
									<button type="button" id="btn-send-email" class="btn bg-orange btn-sm"><i class="fa fa-fw fa-envelope-o"></i> 이메일 발송</button>
								</div>
							</div>
							<div class="box-body">
								<table class="table table-striped table-hover">
									<thead>
										<tr>
											<th><input type="checkbox" id="switch-all"></th>
											<th>이름</th>
											<th>닉네임</th>
											<th>최근 접속일</th>
											<th>메일 발송여부</th>
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
								$age = empty($val['birthday']) ? '-' : date('Y') - $val['birthday'];
								
								$last_login_dt = $val['last_login_dt'];
								
								$mail_count = $val['mail_count'];
								$mail_status = $mail_count > 0 ? '<span class="label label-success">완료</span>' : '<span class="label label-default">대기</span>';
								
						?>
										<tr>
											<td><input type="checkbox" class="user_list" name="user_list[]" value="<?php echo $user_id ?>">
											<td><a href="user_index.php?id=<?php echo $user_id ?>"><?php echo $user_name ?></a></td>
											<td><a href="user_index.php?id=<?php echo $user_id ?>"><?php echo $display_name ?></a></td>
											<td><?php echo $last_login_dt ?></td>
											<td><?php echo $mail_status ?></td>
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
				// 초기화
				$("#search-reset-btn").click(function() {
					$("#search-form :input").val("");
					$("#search-form :checkbox").prop( "checked", false );
				});

				$("#switch-all").click(function() {
					var chk = $(this).prop("checked");
					$(".user_list").prop("checked", chk);
				});

				$("#btn-send-email").click(function() {
					var chkLength = $(".user_list:checked").length;

					if (chkLength == 0) {
						alert("회원을 선택해 주십시오.");
						return;
					}
					$("#user-list-form").submit();
				});
			});
			</script>

<?php 
require_once ADMIN_PATH . '/admin-footer.php';
?>