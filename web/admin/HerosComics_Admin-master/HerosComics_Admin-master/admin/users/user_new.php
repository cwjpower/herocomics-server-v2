<?php 
require_once '../../wps-config.php';

require_once ADMIN_PATH . '/admin-header.php';

require_once './users-lnb.php';
?>

			<link rel="stylesheet" href="<?php echo INC_URL ?>/css/jquery-ui.css">
			
			<!-- Content Wrapper. Contains page content -->
			<div class="content-wrapper">
				<!-- Content Header (Page header) -->
				<div class="content-header">
					<h1>
						회원 등록
					</h1>
					<ol class="breadcrumb">
						<li><a href="<?php echo ADMIN_URL ?>/admin.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li><a href="#">회원관리</a></li>
						<li><a href="#">등급관리</a></li>
						<li><a href="<?php echo ADMIN_URL ?>/users/level_list.php">조회 및 조정</a></li>
						<li class="active"><b>회원추가</b></li>
					</ol>
				</div>

				<!-- Main content -->
				<div class="content body">
					<form id="form-user-new">
						<div class="box box-info">
							<div class="box-header">
							</div>
							
							<div class="box-body">
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
												<input type="text" name="user_login" class="form-control" maxlength="60" placeholder="이메일 주소">
											</td>
											<td class="item-label">이름 *</td>
											<td>
												<input type="text" name="user_name" class="form-control" maxlength="20">
											</td>
										</tr>
										<tr>
											<td class="item-label">닉네임</td>
											<td>
												<input type="text" name="display_name" class="form-control">
											</td>
											<td class="item-label">비밀번호 *</td>
											<td>
												<input type="password" name="user_pass" class="form-control" maxlength="20" placeholder="변경시에만 입력해 주십시오">
											</td>
										</tr>
										<tr>
											<td class="item-label">생년월일</td>
											<td>
												<input type="text" name="birthday" class="form-control datepicker" maxlength="10">
											</td>
											<td class="item-label">성별</td>
											<td>
									<?php 
									foreach ($wps_user_gender as $key => $val) {
										if ( !empty($val) ) {
									?>
												<label><input type="radio" name="gender" value="<?php echo $key ?>"><?php echo $val ?></label> &nbsp; &nbsp;
									<?php 
										}
									}
									?>
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
									<?php 
									foreach ($wps_user_join_path as $key => $val) {
										if ( !empty($val) ) {
											$checked = strcmp($key, 'cms') ? '' : 'checked';
									?>
												<label><input type="radio" name="join_path" value="<?php echo $key ?>" <?php echo $checked ?>><?php echo $val ?></label> &nbsp; &nbsp;
									<?php 
										}
									}
									?>
											</td>
											<td class="item-label">거주지</td>
											<td>
												<select name="residence" class="form-control">
									<?php 
									foreach ($wps_user_residence_area as $key => $val) {
										if ( !empty($val) ) {
									?>
													<option value="<?php echo $key ?>"><?php echo $val ?></option>
									<?php 
										}
									}
									?>
												</select>
											</td>
										</tr>
										<tr>
											<td class="item-label">학력</td>
											<td>
												<select name="last_school" class="form-control">
									<?php 
									foreach ($wps_user_last_school as $key => $val) {
										if ( !empty($val) ) {
									?>
													<option value="<?php echo $key ?>"><?php echo $val ?></option>
									<?php 
										}
									}
									?>
												</select>
											</td>
											<td class="item-label">연락처</td>
											<td>
												<input type="text" id="mobile" name="mobile" class="form-control">
											</td>
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
												<select name="user_status" class="form-control">
									<?php 
									foreach ($wps_user_status as $key => $val) {
										if ( !empty($val) ) {
											$selected = $key == $user_status ? 'selected' : '';
									?>
													<option value="<?php echo $key ?>"><?php echo $val ?></option>
									<?php 
										}
									}
									?>
												</select>
											</td>
											<td class="item-label">회원등급</td>
											<td>
												<select name="user_level" class="form-control">
									<?php 
									foreach ($wps_user_level as $key => $val) {
										if ( !empty($val) ) {
											$selected = $key == $user_level ? 'selected' : '';
									?>
													<option value="<?php echo $key ?>"><?php echo $val ?></option>
									<?php 
										}
									}
									?>
												</select>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
							<div class="box-footer">
								<button type="submit" class="btn btn-primary">추가합니다</button> &nbsp;
								<button type="reset" class="btn btn-default">초기화</button> &nbsp;
							</div>
						</div><!-- /.box-body -->
					</form>
				</div><!-- /.content -->
			</div><!-- /.content-wrapper -->
			
			<script src="<?php echo INC_URL ?>/js/jquery-ui.min.js"></script>
			<script src="<?php echo INC_URL ?>/js/ls-util.js"></script>
			<script src="<?php echo INC_URL ?>/js/jquery/jquery.oLoader.min.js"></script>
			<!-- InputMask -->
			<script src="<?php echo ADMIN_URL ?>/js/jquery/input-mask/jquery.inputmask.js"></script>
			<script src="<?php echo ADMIN_URL ?>/js/jquery/input-mask/jquery.inputmask.date.extensions.js"></script>
			<script src="<?php echo ADMIN_URL ?>/js/jquery/input-mask/jquery.inputmask.extensions.js"></script>
			<!-- Numeric hyphen -->
			<script src="<?php echo INC_URL ?>/js/jquery/jquery.numeric.hyphen.min.js"></script>
			
			<script>
			$(function() {
				$("#form-user-new").submit(function(e) {
					e.preventDefault();
					showLoader();
					$.ajax({
						type : "POST",
						url : "./ajax/user-new.php",
						data : $(this).serialize(),
						dataType : "json",
						success : function(res) {
							if (res.code == "0") {
								location.href = "./level_list.php";
							} else {
								hideLoader();
								alert(res.msg);
							}
						}
					});
				});

				// jquery ui calendar
				$(".datepicker").datepicker({
					dateFormat: 'yy-mm-dd',
					monthNames: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
					monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
					dayNamesMin: ['일','월','화','수','목','금','토'],
					changeMonth: true,
					changeYear: true,
					yearRange : "-100:+0",
					showMonthAfterYear: true
				}).inputmask('yyyy-mm-dd');


				$("#mobile").numeric_hyphen().blur(function(e) {
					$(this).val(phoneFormat($(this).val()));
				});
// 				$("#mobile").numeric_hyphen();
				
			}); //$
			</script>

<?php 
require_once ADMIN_PATH . '/admin-footer.php';
?>