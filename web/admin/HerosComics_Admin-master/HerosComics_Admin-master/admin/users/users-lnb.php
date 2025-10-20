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
			<?php 
			if (wps_is_admin()) {
			?>
						<li class="header">회원관리</li>
						<li <?php echo strpos($nav_filename, 'index.php') === false ? '' : 'class="active"'; ?>>
							<a href="<?php echo ADMIN_URL ?>/users/"><i class="fa fa-circle-o"></i> 회원조회</a>
						</li>
						<li class="treeview <?php echo strpos($nav_filename, 'level') === false ? '' : 'active'; ?>">
							<a href="#"><i class="fa fa-circle-o"></i> <span>등급 관리</span> <i class="fa fa-angle-left pull-right"></i></a>
							<ul class="treeview-menu">
								<li <?php echo strpos($nav_filename, 'level_list.php') === false ? '' : 'class="active"'; ?>>
									<a href="<?php echo ADMIN_URL ?>/users/level_list.php"><i class="fa fa-dot-circle-o"></i> 조회 및 조정</a>
								</li>
								<!-- 
								<li <?php echo strpos($nav_filename, 'level_set.php') === false ? '' : 'class="active"'; ?>>
									<a href="<?php echo ADMIN_URL ?>/users/level_set.php"><i class="fa fa-dot-circle-o"></i> 등급 권한 설정</a>
								</li>
								 -->
							</ul>
						</li>
						
						<li <?php echo strpos($nav_filename, 'status_list.php') === false ? '' : 'class="active"'; ?>>
							<a href="<?php echo ADMIN_URL ?>/users/status_list.php"><i class="fa fa-circle-o"></i> <span>신규/탈퇴/블랙</span></a>
						</li>
						<li <?php echo strpos($nav_filename, 'dormancy_list.php') === false ? '' : 'class="active"'; ?>>
							<a href="<?php echo ADMIN_URL ?>/users/dormancy_list.php"><i class="fa fa-circle-o"></i> <span>휴면계정관리</span></a>
						</li>
			<?php 
			}
			?>
					</ul>
				</section>
				<!-- /.sidebar -->
			</aside>
			<!-- /.main-sidebar -->
			