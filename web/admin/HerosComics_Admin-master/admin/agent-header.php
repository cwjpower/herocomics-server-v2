<?php
/**
 * 관리자인 경우에 전체 관리자만 체크 하고 있는데,
 * 출판사 사용자인 경우도 체크 해야 함.
 */

if ( !wps_is_agent() ) {
	wps_redirect( ADMIN_URL . '/login.php' );
}

$lps_dir_path = dirname($_SERVER['PHP_SELF']);

?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>HeroComics | CMS</title>
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
  
		<!-- Custom -->
		<link rel="stylesheet" href="<?php echo ADMIN_URL ?>/css/ls-custom.css">

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
		<!-- SlimScroll 1.3.0 -->
		<script src="<?php echo ADMIN_URL ?>/js/jquery/jquery.slimscroll.min.js"></script>
		
		<script src="<?php echo ADMIN_URL ?>/js/icheck.min.js"></script>
		
	</head>
	
	<body class="skin-blue fixed" data-spy="scroll" data-target="#scrollspy">
		<div class="wrapper">
			<header class="main-header">
				<!-- Logo -->
				<!-- Logo -->
				<a href="<?php echo ADMIN_URL ?>/admin.php" class="logo">
					<!-- mini logo for sidebar mini 50x50 pixels -->
					<span class="logo-mini"><b>B</b>T</span>
					<!-- logo for regular state and mobile devices -->
					<span class="logo-lg"><b>HeroComics</b></span>
				</a>
				<!-- Header Navbar: style can be found in header.less -->
				<nav class="navbar navbar-static-top" role="navigation">
					<!-- Sidebar toggle button-->
					<a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
						<span class="sr-only">Toggle navigation</span>
					</a>
					<!-- Navbar Menu -->
					<div class="navbar-custom-menu pull-left">
						<ul class="nav navbar-nav">
							<li <?php echo stripos($lps_dir_path, '/books') ? 'class="active"' : ''; ?>><a href="<?php echo ADMIN_URL ?>/books/">책 관리</a></li>
							<li <?php echo stripos($lps_dir_path, '/community') ? 'class="active"' : ''; ?>><a href="<?php echo ADMIN_URL ?>/community/">커뮤니티 관리</a></li>
							<li <?php echo stripos($lps_dir_path, '/settle') ? 'class="active"' : ''; ?>><a href="<?php echo ADMIN_URL ?>/settle/period_day.php">정산</a></li>
							<li <?php echo stripos($lps_dir_path, '/statistics') ? 'class="active"' : ''; ?>><a href="<?php echo ADMIN_URL ?>/statistics/by_period.php">통계</a></li>
						</ul>
					</div>
				</nav>
			</header>
			

			