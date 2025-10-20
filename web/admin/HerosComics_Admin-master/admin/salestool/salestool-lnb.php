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
						<li class="header">판매 관리</li>
						<li <?php echo strpos($nav_filename, 'order_') === false ? '' : 'class="active"'; ?>>
							<a href="<?php echo ADMIN_URL ?>/salestool/order_list.php"><i class="fa fa-circle-o"></i> 거래 내역 조회</a>
						</li>
						<li <?php echo strpos($nav_filename, 'refund_list.php') === false ? '' : 'class="active"'; ?>>
							<a href="<?php echo ADMIN_URL ?>/salestool/refund_list.php"><i class="fa fa-circle-o text-red"></i> 환불 신청 관리</a>
						</li>
					</ul>
				</section>
				<!-- /.sidebar -->
			</aside>
			<!-- /.main-sidebar -->
			