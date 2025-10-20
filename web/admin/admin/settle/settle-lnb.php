<?php 
$nav_filename = basename( $_SERVER['PHP_SELF'] );
$nav_qs = $_SERVER['QUERY_STRING'];
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
						<li class="header">페이지 관리</li>
						<li class="treeview <?php echo strpos($nav_filename, 'period_') === false ? '' : 'active'; ?>">
							<a href="#"><i class="fa fa-circle-o"></i> <span>기간별</span> <i class="fa fa-angle-left pull-right"></i></a>
							<ul class="treeview-menu">
								<li <?php echo strpos($nav_filename, 'period_day') === false ? '' : 'class="active"'; ?>>
									<a href="<?php echo ADMIN_URL ?>/settle/period_day.php"><i class="fa fa-dot-circle-o"></i> 일별</a>
								</li>
								<li <?php echo strpos($nav_filename, 'period_week') === false ? '' : 'class="active"'; ?>>
									<a href="<?php echo ADMIN_URL ?>/settle/period_week.php"><i class="fa fa-dot-circle-o"></i> 주별</a>
								</li>
								<li <?php echo strpos($nav_filename, 'period_month') === false ? '' : 'class="active"'; ?>>
									<a href="<?php echo ADMIN_URL ?>/settle/period_month.php"><i class="fa fa-dot-circle-o"></i> 월별</a>
								</li>
								<li <?php echo strpos($nav_filename, 'period_time') === false ? '' : 'class="active"'; ?>>
									<a href="<?php echo ADMIN_URL ?>/settle/period_time.php"><i class="fa fa-dot-circle-o"></i> 시간별</a>
								</li>
								<li <?php echo strpos($nav_filename, 'period_manual') === false ? '' : 'class="active"'; ?>>
									<a href="<?php echo ADMIN_URL ?>/settle/period_manual.php"><i class="fa fa-dot-circle-o"></i> 기간 설정</a>
								</li>
							</ul>
						</li>
						<li <?php echo strpos($nav_filename, 'by_book.php') === false ? '' : 'class="active"'; ?>>
							<a href="<?php echo ADMIN_URL ?>/settle/by_book.php"><i class="fa fa-circle-o"></i> 도서별</a>
						</li>
						<li <?php echo strpos($nav_filename, 'for_admin.php') === false ? '' : 'class="active"'; ?>>
							<a href="<?php echo ADMIN_URL ?>/settle/for_admin.php"><i class="fa fa-circle-o"></i> 관리자용</a>
						</li>								
					</ul>
				</section>
				<!-- /.sidebar -->
			</aside>
			<!-- /.main-sidebar -->
			