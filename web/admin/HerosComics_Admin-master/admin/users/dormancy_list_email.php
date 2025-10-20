<?php 
require_once '../../wps-config.php';

if (empty($_POST['user_list'])) {
	lps_alert_back('메일을 보낼 회원을 선택해 주십시오.');
	exit;
}

$user_list = implode(',', $_POST['user_list']);
$user_list_desc = count($_POST['user_list']) . '명';

$user_emails = lps_get_user_emails($user_list);

// 발신자 이메일 주소
$return_email = wps_get_option('return_email');

require_once ADMIN_PATH . '/admin-header.php';

require_once './users-lnb.php';
?>

			<link rel="stylesheet" href="<?php echo INC_URL ?>/css/jquery-ui.css">
			
			<!-- Content Wrapper. Contains page content -->
			<div class="content-wrapper">
				<!-- Content Header (Page header) -->
				<div class="content-header">
					<h1>
						메일 전송
					</h1>
					<ol class="breadcrumb">
						<li><a href="<?php echo ADMIN_URL ?>/admin.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li><a href="<?php echo ADMIN_URL ?>/users/">회원관리</a></li>
						<li><a href="<?php echo ADMIN_URL ?>/users/dormancy_list.php">휴면계정관리</a></li>
						<li class="active"><b>이메일 발송</b></li>
					</ol>
				</div>
				<!-- Main content -->
				<div class="content body">
					<div class="row">
						<div class="col-md-3">
							<a href="javascript:history.back();" class="btn btn-primary btn-block margin-bottom">뒤로 가기</a>
					
							<div class="box box-solid">
								<div class="box-header with-border">
									<h3 class="box-title">받는 사람</h3>
					
									<div class="box-tools">
										<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
										</button>
									</div>
								</div>
								<div class="box-body no-padding">
									<ul class="nav nav-pills nav-stacked">
							<?php 
							foreach ($user_emails as $key => $val) {
								$uname = $val['user_name'];
								$uemail = $val['user_login']; 
							?>
										<li><a><i class="fa fa-envelope-o"></i> <?php echo $uname ?> <?php echo $uemail ?></a></li>
							<?php 
							}
							?>
									</ul>
								</div>
								<!-- /.box-body -->
							</div>
							<!-- /. box -->
						</div>
						<!-- /.col -->
						<div class="col-md-9">
							<form id="mail-send-form" class="form-horizontal">
								<input type="hidden" id="to" name="to" value='<?php echo $user_list ?>'>
								
								<div class="box box-primary">
									<div class="box-header with-border">
										<h3 class="box-title">메일 작성</h3>
									</div>
									<!-- /.box-header -->
									<div class="box-body">
										<div class="form-group">
											<label for="from" class="col-sm-2 control-label">보내는 사람</label>
											<div class="col-sm-10">
												<input type="email" id="from" name="from" class="form-control" placeholder="From:" value="<?php echo $return_email ?>">
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-2 control-label">받는 사람</label>
											<div class="col-sm-10">
												<input class="form-control" value="<?php echo $user_list_desc ?>" readonly>
											</div>
										</div>
										<div class="form-group">
											<label for="subject" class="col-sm-2 control-label">제목</label>
											<div class="col-sm-10">
												<input type="text" id="subject" name="subject" class="form-control" placeholder="Subject:" value="[북톡] 회원님의 아이디가 휴면상태로 전환될 예정입니다.">
											</div>
										</div>
										<div class="form-group">
											<div class="col-sm-12">
												<textarea id="ckeditor_content" name="ckeditor_content" class="form-control"></textarea>
											</div>
										</div>
									</div>
									<!-- /.box-body -->
									<div class="box-footer">
										<div class="pull-right">
											<button type="submit" class="btn btn-primary"><i class="fa fa-arrow-circle-right"></i> 보내기</button>
										</div>
										<button type="reset" id="reset-btn" class="btn btn-default"><i class="fa fa-times"></i> 초기화</button>
									</div>
									<!-- /.box-footer -->
								</div>
								<!-- /. box -->
							</form>
						</div>
						<!-- /.col -->
					</div>
				</div><!-- /.Main content -->

			</div><!-- /.content-wrapper -->
			
			<script src="<?php echo INC_URL ?>/js/jquery-ui.min.js"></script>
			<script src="<?php echo INC_URL ?>/js/ls-util.js"></script>
			<script src="<?php echo INC_URL ?>/js/jquery/jquery.oLoader.min.js"></script>
			
			<!-- CkEditor -->
			<script src="<?php echo INC_URL ?>/js/ckeditor-4.5.10/ckeditor.js"></script>
			<script src="<?php echo INC_URL ?>/js/ckeditor-4.5.10/adapters/jquery.js"></script>
			
			<script>
			$(function() {
				$("#mail-send-form").submit(function(e) {
					e.preventDefault();

					if (!$("#to").val()) {
						alert("메일을 수신할 회원을 선택해 주십시오.");
						return;
					}
					if (!$("#from").val()) {
						alert("보내는 사람 이메일을 입력해 주십시오.");
						$("#from").focus();
						return;
					}
					if (!$("#subject").val()) {
						alert("메일 제목을 입력해 주십시오.");
						$("#subject").focus();
						return;
					}
					if (!$("#ckeditor_content").val()) {
						alert("메일 본문 내용을 입력해 주십시오.");
						return;
					}

					showLoader();
					
					$.ajax({
						type : "POST",
						url : "./ajax/dormancy-send-mail.php",
						data : $(this).serialize(),
						dataType : "json",
						success : function(res) {
							if (res.code == "0") {
								location.replace("dormancy_list.php");
							} else {
								hideLoader();
								alert(res.msg);
							}
						}
					});
				});
				
				$("#ckeditor_content").ckeditor({
					height: 500
				});

				$("#reset-btn").click(function() {
					$("#ckeditor_content").val("");
				});
			}); //$
			</script>

<?php 
require_once ADMIN_PATH . '/admin-footer.php';
?>