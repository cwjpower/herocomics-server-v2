<?php 
require_once '../wps-config.php';

if ( wps_exist_admin() ) {
	wps_redirect( ADMIN_URL . '/login.php' );
}

if ( wps_is_admin() ) {
	wps_redirect( ADMIN_URL . '/admin.php' );
}

if ( !empty($_POST) ) {
	if ( wps_add_admin() ) {
		wps_redirect( ADMIN_URL . '/login.php' );
	}
}

?>
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>BOOKTALK | CMS</title>
		<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
		<!-- Bootstrap 3.3.6 -->
		<link rel="stylesheet" href="/includes/css/bootstrap.min.css">
		<!-- Font Awesome -->
		<link rel="stylesheet" href="/admin/css/font-awesome.min.css">
		<!-- Ionicons -->
		<link rel="stylesheet" href="/admin/css/ionicons.min.css">
		<!-- Theme style -->
		<link rel="stylesheet" href="/admin/css/AdminLTE.min.css">
		<link rel="stylesheet" href="/admin/css/skins/_all-skins.min.css">
		<!-- bootstrap datepicker -->
		<link rel="stylesheet" href="/admin/css/datepicker3.css">
  
		<!-- Custom -->
		<link rel="stylesheet" href="/admin/css/ls-custom.css">

		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
				<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
				<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
		
		<!-- jQuery 2.2.3 -->
		<script src="/admin/js/jquery-2.2.3.min.js"></script>
		<!-- Bootstrap 3.3.6 -->
		<script src="/includes/js/bootstrap.min.js"></script>
		<!-- FastClick -->
		<script src="/admin/js/fastclick.min.js"></script>
		<!-- AdminLTE App -->
		<script src="/admin/js/app.min.js"></script>
		<!-- SlimScroll 1.3.0 -->
		<script src="/admin/js/jquery/jquery.slimscroll.min.js"></script>
		
	</head>

	<body class="hold-transition register-page">
		<div class="register-box">
			<div class="register-logo">
				<b>LAMP</b>SOFT
			</div><!-- /.register-logo -->
			<div class="register-box-body">
				<p class="login-box-msg">관리자 추가</p>
				<form role="form" method="post" id="user-new-form">
					<div class="form-group has-feedback">
						<input type="text" name="userid" id="userid" class="form-control" placeholder="아이디" maxlength="20" required oncontextmenu="return false">
						<span class="glyphicon glyphicon-user form-control-feedback"></span>
					</div>
					<div class="form-group has-feedback">
						<input type="password" name="userpw" id="userpw" class="form-control" placeholder="비밀번호" maxlength="20" required oncontextmenu="return false">
						<span class="glyphicon glyphicon-lock form-control-feedback"></span>
					</div>
					<div class="form-group has-feedback">
						<input type="password" name="userpw2" id="userpw2" class="form-control" placeholder="비밀번호 확인" maxlength="20" required oncontextmenu="return false">
						<span class="glyphicon glyphicon-ok-circle form-control-feedback"></span>
					</div>
					<div class="row">
						<div class="col-xs-12">
							<button type="submit" class="btn btn-warning btn-block btn-flat">등록합니다</button>
						</div><!-- /.col -->
					</div>
				</form>

			</div><!-- /.register-box-body -->
		</div><!-- /.register-box -->

		<script>
		$(function () {
			$("#user-new-form").submit(function(e) {
				var userpw = $("#userpw").val();
				var userpw2 = $("#userpw2").val();
				
				if ( userpw != userpw2 ) {
					alert("비밀번호가 일치하지 않습니다.");
					return false;
				} 
			});
			$("#userid").on("keydown, keypress, keyup", function() {
				$(this).val( $(this).val().replace(/[^a-zA-Z0-9]/g, "") );
			});
		});
		</script>
	</body>
</html>
