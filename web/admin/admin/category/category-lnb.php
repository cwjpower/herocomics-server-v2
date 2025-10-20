<?php 
$nav_filename = basename( $_SERVER['PHP_SELF'] );
?>
			<!-- Left side column. contains the logo and sidebar -->
			<aside class="main-sidebar">
				<!-- sidebar: style can be found in sidebar.less -->
				<section class="sidebar" style="height: auto;" id="scrollspy">
					<!-- Sidebar user panel -->
					<div class="user-panel">
						<div class="pull-left image">
							<img src="<?php echo IMG_URL ?>/common/photo-default.png" class="img-circle" alt="User Image">
						</div>
						<div class="pull-left info" style="width: 70%;">
							<div class="pull-right"><a href="<?php echo ADMIN_URL ?>/users/profile.php"><i class="fa fa-gear"></i></a></div>
							<div class="pull-left"><?php echo wps_get_current_user_name() ?></div>
							<div style="clear: both; padding-top: 10px;"><a href="<?php echo ADMIN_URL ?>/logout.php" class="label label-warning">로그아웃</a></div>
						</div>
					</div>
				</section>
				<!-- /.sidebar -->
			</aside>
			<!-- /.main-sidebar -->
			