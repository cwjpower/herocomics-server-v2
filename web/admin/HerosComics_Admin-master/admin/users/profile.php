<?php 
require_once '../../wps-config.php';

$user_id = empty($_GET['id']) ? wps_get_current_user_id() : $_GET['id'];

if ( empty($user_id) ) {
	lps_js_back( '사용자 아이디가 존재하지 않습니다.' );
}

$user_row = wps_get_user_by( 'ID', $user_id );

$user_login = $user_row['user_login'];
$user_name = $user_row['user_name'];
$user_pass = $user_row['user_pass'];
// $user_email = $user_row['user_email'];
$user_registered = $user_row['user_registered'];
$user_status = $user_row['user_status'];
$user_status_label = $wps_user_status[$user_status];
$user_level = $user_row['user_level'];
$user_level_label = $wps_user_level[$user_level];
$display_name = $user_row['display_name'];
$mobile = $user_row['mobile'];
$birthday = $user_row['birthday'];
$gender = $user_row['gender'];
$gender_label = $wps_user_gender[$gender];
$join_path = $user_row['join_path'];
$join_path_label = empty($join_path) ? '' : $wps_user_join_path[$join_path];
$last_login_dt = $user_row['last_login_dt'];
$residence = $user_row['residence'];
$residence_label = empty($residence) ? '' : $wps_user_residence_area[$residence];
$last_school = $user_row['last_school'];
$last_school_label = empty($last_school) ? '' : $wps_user_last_school[$last_school];

$user_meta = wps_get_user_meta( $user_id );

$um_user_level = $user_meta['wps_user_level'];
$um_user_level_label = $wps_user_level[$um_user_level];
$um_block_log = empty($user_meta['wps_user_block_log']) ? '' : unserialize($user_meta['wps_user_block_log']);
$um_block_reason = empty($um_block_log['reason']) ? '' : $um_block_log['reason'];

if ($user_status == 4) {
	lps_js_back( '탈퇴한 회원은 접근할 수 없습니다.' );
}

if (wps_is_admin()) {
	require_once ADMIN_PATH . '/admin-header.php';
} else {
	require_once ADMIN_PATH . '/agent-header.php';
}

require_once './users-lnb.php';
?>

			<link rel="stylesheet" href="<?php echo INC_URL ?>/css/jquery-ui.css">
			<link rel="stylesheet" href="<?php echo INC_URL ?>/css/jquery.Jcrop.css">
			
			<!-- Content Wrapper. Contains page content -->
			<div class="content-wrapper">
				<!-- Content Header (Page header) -->
				<div class="content-header">
					<h1>
						회원 Profile
					</h1>
					<ol class="breadcrumb">
						<li><a href="<?php echo ADMIN_URL ?>/admin.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li><a href="<?php echo ADMIN_URL ?>/users/">회원관리</a></li>
						<li><a href="<?php echo ADMIN_URL ?>/users/user_index.php?id=<?php echo $user_id ?>"><?php echo $user_name ?></a></li>
						<li class="active"><b>Profile</b></li>
					</ol>
				</div>

				<!-- Main content -->
				<div class="content body">
					<form id="form-user-edit">
						<input type="hidden" name="user_id" id="user_id" value="<?php echo $user_id ?>">
						<div class="box box-info">
							<div class="box-header">
							</div>
							
							<div class="box-body">
							
								<h4>프로필 포토</h4>
								<div class="well">
									<img src="<?php echo lps_get_user_avatar($user_id) ?>" class="img-circle user-avatar" alt="User Image">
								</div>
								
								<h4>기본정보</h4>
								<table class="table table-bordered ls-table">
									<colgroup>
										<col style="width: 15%;">
										<col style="width: 35%;">
										<col style="width: 15%;">
										<col style="width: 35%;">
									</colgroup>
									<tbody>
										<tr>
											<td class="item-label">계정 *</td>
											<td>
												<?php echo $user_login ?>
											</td>
											<td class="item-label">이름 *</td>
											<td>
												<?php echo $user_name ?>
											</td>
										</tr>
										<tr>
											<td class="item-label">닉네임</td>
											<td colspan="3">
												<?php echo $display_name ?>
											</td>
										</tr>
										<tr>
											<td class="item-label">생년월일</td>
											<td>
												<?php echo $birthday ?>
											</td>
											<td class="item-label">성별</td>
											<td>
												<?php echo $wps_user_gender[$gender] ?>
											</td>
										</tr>
									</tbody>
								</table>
								<h4>추가정보</h4>
								<table class="table table-bordered ls-table">
									<colgroup>
										<col style="width: 15%;">
										<col style="width: 35%;">
										<col style="width: 15%;">
										<col style="width: 35%;">
									</colgroup>
									<tbody>
										<tr>
											<td class="item-label">가입경로</td>
											<td>
												<?php echo $join_path_label ?>
											</td>
											<td class="item-label">거주지</td>
											<td>
												<?php echo $residence_label ?>
											</td>
										</tr>
										<tr>
											<td class="item-label">학력</td>
											<td>
												<?php echo $last_school_label ?>
											</td>
											<td class="item-label">연락처</td>
											<td>
												<?php echo $mobile ?>
											</td>
										</tr>
										<tr>
											<td class="item-label">가입일</td>
											<td><?php echo $user_registered ?></td>
											<td class="item-label">접속로그</td>
											<td><?php echo $last_login_dt ?></td>
										</tr>									
									</tbody>
								</table>
								<h4>기타정보</h4>
								<table class="table table-bordered ls-table">
									<colgroup>
										<col style="width: 15%;">
										<col style="width: 35%;">
										<col style="width: 15%;">
										<col style="width: 35%;">
									</colgroup>
									<tbody>
										<tr>
											<td class="item-label">회원상태</td>
											<td id="user-status-label">
												<?php echo $user_status_label ?>
											</td>
											<td class="item-label">회원등급</td>
											<td>
												<?php echo $user_level_label ?>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
							<div class="box-footer">
								<button type="button" id="back-btn" class="btn btn-default">뒤로가기</button>
							</div>
						</div><!-- /.box-body -->
					</form>
				</div><!-- /.content -->
			</div><!-- /.content-wrapper -->
			
			<script>
			$(function() {
				$("#back-btn").click(function() {
					history.back();
				});
			}); //$
			</script>

<?php 
require_once ADMIN_PATH . '/admin-footer.php';
?>