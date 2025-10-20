<?php 
require_once '../wps-config.php';

$error = false;

if ( empty($_COOKIE['remember_me']) ) {
	$remember_me = 0;
	$remember_userid = '';
} else {
	$remember_me = $_COOKIE['remember_me'];
	$remember_userid = $_COOKIE['remember_userid'];
}

if ( !empty($_POST['userlogin']) && !empty($_POST['passwd']) ) {

	$userlogin = $_POST['userlogin'];
	$passwd = wps_get_password($_POST['passwd']);

	if ( !empty($_POST['remember_me'] )) {
		// 기억하기 체크시에는 아이디를 쿠키로 저장함.
		setcookie( 'remember_me', $_POST['remember_me'], time() + 86400 * 30 );
		setcookie( 'remember_userid', $userlogin, time() + 86400 * 30 );

		$remember_me = 1;
		$remember_userid = $userlogin;

	} else {
		setcookie( 'remember_me', '', time() - 86400 );
		setcookie( 'remember_userid', '', time() - 86400 );
	}

	$users = wps_get_user_by( 'user_login', $userlogin );

	if ( empty($users) ) {
		$error = '존재하지 않는 아이디입니다.';
	} else {
		if ( strcmp($users['user_pass'], $passwd) ) {
			$error = '비밀번호가 일치하지 않습니다.';
		}
	}

	if ( !$error ) {
		$user_level = wps_get_user_meta( $users['ID'], 'wps_user_level' );

		if ( $user_level > 5 ) {
			$_SESSION['login']['userid'] = $users['ID'];
			$_SESSION['login']['user_login'] = $users['user_login'];
			$_SESSION['login']['user_name'] = $users['user_name'];
			$_SESSION['login']['display_name'] = $users['display_name'];
			$_SESSION['login']['user_level'] = $user_level;
			
			// 세션 업데이트
			wps_update_user_meta( $users['ID'], 'wps_session_id', session_id() );
				
			if ( $user_level == 10 ) {
				header( 'Location: admin.php' );
			} else {
				header( 'Location: agent.php' );
			}
			exit;
		} else {
			$error = '관리자 권한이 없습니다.';
		}
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
		<link rel="stylesheet" href="<?php echo INC_URL ?>/css/bootstrap.min.css">
		<!-- Font Awesome -->
		<link rel="stylesheet" href="<?php echo ADMIN_URL ?>/css/font-awesome.min.css">
		<!-- Ionicons -->
		<link rel="stylesheet" href="<?php echo ADMIN_URL ?>/css/ionicons.min.css">
		<!-- Theme style -->
		<link rel="stylesheet" href="<?php echo ADMIN_URL ?>/css/AdminLTE.min.css">
		<link rel="stylesheet" href="<?php echo ADMIN_URL ?>/css/skins/_all-skins.min.css">
		<link rel="stylesheet" href="<?php echo ADMIN_URL ?>/css/iCheck/square/blue.css">

		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
				<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
				<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
		
		<!-- jQuery 2.2.3 -->
		<script src="<?php echo ADMIN_URL ?>/js/jquery-2.2.3.min.js"></script>
		<!-- Bootstrap 3.3.6 -->
		<script src="<?php echo INC_URL ?>/js/bootstrap.min.js"></script>
		<!-- AdminLTE App -->
		<script src="<?php echo ADMIN_URL ?>/js/app.min.js"></script>
		<script src="<?php echo ADMIN_URL ?>/js/icheck.min.js"></script>
		
	</head>

	<body class="hold-transition login-page">
		<div class="login-box">
			<div class="login-logo">
				<b>BOOK</b>TALK
			</div><!-- /.login-logo -->
			<div class="login-box-body">
				
	<?php
	if ( wps_get_ie_version() > 4 ) {
	?>
				<p class="login-box-msg">LOG-IN</p>
				<form role="form" method="post" id="admin-login-form">
					<div class="form-group has-feedback">
						<input type="text" name="userlogin" id="userlogin" class="form-control" placeholder="아이디" maxlength="40" required <?php echo $remember_me ? '' : 'autofocus'; ?> value="<?php echo $remember_userid ?>">
						<span class="glyphicon glyphicon-user form-control-feedback"></span>
					</div>
					<div class="form-group has-feedback">
						<input type="password" name="passwd" id="passwd" class="form-control" placeholder="비밀번호" maxlength="20" required <?php echo $remember_me ? 'autofocus' : ''; ?>>
						<span class="glyphicon glyphicon-lock form-control-feedback"></span>
					</div>
					<div class="row">
						<div class="col-xs-8">
							<div class="checkbox icheck">
								<label>
									<input type="checkbox" name="remember_me" value="1" <?php echo $remember_me ? 'checked' : ''; ?>> <small>아이디 기억하기</small>
								</label>
							</div>
						</div><!-- /.col -->
						<div class="col-xs-4">
							<button type="submit" class="btn btn-primary btn-block btn-flat">로그인</button>
						</div><!-- /.col -->
					</div>
					
		<?php
		if ( $error ) {
		?>
					<hr>
					<div class="alert alert-danger alert-dismissable">
						<h4>
							<i class="icon fa fa-warning"></i>Error!
						</h4>
						<?php echo $error ?>
					</div>
		<?php
		}
		?>
					
				</form>
	<?php
	} else {
	?>
				<div class="info-box bg-red">
					<span class="info-box-icon"><i class="fa fa-ban"></i></span>
					<div class="info-box-content">
						<span class="info-box-text">인터넷 익스플로러 8 이하 버전은</span>
						<span class="info-box-number">사용하실 수 없습니다</span>
						<div class="progress">
							<div class="progress-bar" style="width: 100%"></div>
						</div>
						<span class="progress-description">
							IE 9.0 이상을 사용하십시오.
						</span>
					</div><!-- /.info-box-content -->
				</div>
				
				<a href="http://windows.microsoft.com/ko-kr/internet-explorer/download-ie" class="btn btn-block btn-info btn-flat">
					최신 Internet Explorer 다운로드
					<i class="fa fa-download"></i>
				</a>

	<?php
	}
	?>
			</div><!-- /.login-box-body -->
		</div><!-- /.login-box -->

		<script>
		$(function () {
			$('input').iCheck({
				checkboxClass: 'icheckbox_square-blue',
				radioClass: 'iradio_square-blue',
				increaseArea: '20%' // optional
			});
		});
		</script>
	</body>
</html>
