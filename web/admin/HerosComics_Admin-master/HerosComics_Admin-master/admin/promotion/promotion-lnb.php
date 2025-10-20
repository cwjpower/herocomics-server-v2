<?php 
$nav_filename = basename( $_SERVER['PHP_SELF'] );
?>
			<!-- Left side column. contains the logo and sidebar -->
			<aside class="main-sidebar">
				<!-- sidebar: style can be found in sidebar.less -->
				<section class="sidebar" style="height: auto;" id="scrollspy">
					<!-- Sidebar user panel -->
<?php 
require_once ADMIN_PATH . '/sidebar_profile.php';
?>
					<!-- sidebar menu: : style can be found in sidebar.less -->
					<ul class="sidebar-menu">
						<li class="header">프로모션</li>
						<li class="treeview <?php echo strpos($nav_filename, 'point_') === false ? '' : 'active'; ?>">
							<a href="#"><i class="fa fa-circle-o"></i> <span>포인트 관리</span> <i class="fa fa-angle-left pull-right"></i></a>
							<ul class="treeview-menu">
								<li <?php echo strpos($nav_filename, 'point_get_') === false ? '' : 'class="active"'; ?>>
									<a href="<?php echo ADMIN_URL ?>/promotion/point_get_list.php"><i class="fa fa-dot-circle-o"></i> 포인트 적립내역</a>
								</li>
								<li <?php echo strpos($nav_filename, 'point_spend_') === false ? '' : 'class="active"'; ?>>
									<a href="<?php echo ADMIN_URL ?>/promotion/point_spend_list.php"><i class="fa fa-dot-circle-o"></i> 포인트 사용내역</a>
								</li>
							</ul>
						</li>
						<li <?php echo strpos($nav_filename, 'coupon_') === false ? '' : 'class="active"'; ?>>
							<a href="<?php echo ADMIN_URL ?>/promotion/coupon_list.php"><i class="fa fa-circle-o"></i> 쿠폰 관리</a>
						</li>
						<li class="treeview <?php echo strpos($nav_filename, 'sms_') === false ? '' : 'active'; ?>">
							<a href="#"><i class="fa fa-circle-o"></i> <span>SMS 관리</span> <i class="fa fa-angle-left pull-right"></i></a>
							<ul class="treeview-menu">
								<li <?php echo strpos($nav_filename, 'sms_list.php') === false ? '' : 'class="active"'; ?>>
									<a href="<?php echo ADMIN_URL ?>/promotion/sms_list.php"><i class="fa fa-dot-circle-o"></i> SMS 전송내역</a>
								</li>
								<li <?php echo strpos($nav_filename, 'sms_new.php') === false ? '' : 'class="active"'; ?>>
									<a href="<?php echo ADMIN_URL ?>/promotion/sms_new.php"><i class="fa fa-dot-circle-o"></i> SMS 전송</a>
								</li>
							</ul>
						</li>
						<li class="treeview <?php echo strpos($nav_filename, 'email_') === false ? '' : 'active'; ?>">
							<a href="#"><i class="fa fa-circle-o"></i> <span>Email 관리</span> <i class="fa fa-angle-left pull-right"></i></a>
							<ul class="treeview-menu">
								<li <?php echo strpos($nav_filename, 'email_list.php') === false ? '' : 'class="active"'; ?>>
									<a href="<?php echo ADMIN_URL ?>/promotion/email_list.php"><i class="fa fa-dot-circle-o"></i> Email 전송내역</a>
								</li>
								<li <?php echo strpos($nav_filename, 'email_new.php') === false ? '' : 'class="active"'; ?>>
									<a href="<?php echo ADMIN_URL ?>/promotion/email_new.php"><i class="fa fa-dot-circle-o"></i> Email 전송</a>
								</li>
							</ul>
						</li>
					</ul>
				</section>
				<!-- /.sidebar -->
			</aside>
			<!-- /.main-sidebar -->
			