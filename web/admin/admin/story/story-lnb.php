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
						<li class="header">스토리 관리</li>
						<li <?php echo strpos($nav_filename, 'index.php') === false ? '' : 'class="active"'; ?>>
							<a href="<?php echo ADMIN_URL ?>/story/"><i class="fa fa-circle-o"></i> 세계관 관리</a>
						</li>
                        <li <?php echo strpos($nav_filename, 'index.php') === false ? '' : 'class="active"'; ?>>
                            <a href="<?php echo ADMIN_URL ?>/story/char_list.php"><i class="fa fa-circle-o"></i> 캐릭터/팀 관리</a>
                        </li>

                        <li <?php echo strpos($nav_filename, 'index.php') === false ? '' : 'class="active"'; ?>>
                            <a href="<?php echo ADMIN_URL ?>/story/reading_list.php"><i class="fa fa-circle-o"></i> 리딩가이드 관리</a>
                        </li>

						<!-- 
						<li <?php echo strpos($nav_filename, 'curation') === false ? '' : 'class="active"'; ?>>
							<a href="<?php echo ADMIN_URL ?>/community/freetalk.php"><i class="fa fa-circle-o"></i> 자유대화방</a>
						</li> -->
					</ul>
				</section>
				<!-- /.sidebar -->
			</aside>
			<!-- /.main-sidebar -->
			