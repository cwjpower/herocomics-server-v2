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
						<li class="header">문의관리</li>
						<li <?php echo strpos($nav_filename, 'qna_') === false ? '' : 'class="active"'; ?>>
							<a href="<?php echo ADMIN_URL ?>/customer/qna_list.php"><i class="fa fa-circle-o"></i> 1:1 문의</a>
						</li>
						<li <?php echo strpos($nav_filename, 'ask_book_') === false ? '' : 'class="active"'; ?>>
							<a href="<?php echo ADMIN_URL ?>/customer/ask_book_list.php"><i class="fa fa-circle-o"></i> 도서 신청</a>
						</li>
						<li <?php echo strpos($nav_filename, 'feedback_') === false ? '' : 'class="active"'; ?>>
							<a href="<?php echo ADMIN_URL ?>/customer/feedback_list.php"><i class="fa fa-circle-o"></i> 건의사항</a>
						</li>
					</ul>
				</section>
				<!-- /.sidebar -->
			</aside>
			<!-- /.main-sidebar -->
			