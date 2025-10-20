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
						<li class="header">통계</li>
						
						<!-- 
						<li class="treeview <?php echo strpos($nav_filename, 'sale_') === false ? '' : 'active'; ?>">
							<a href="#"><i class="fa fa-circle-o"></i> <span>판매분석</span> <i class="fa fa-angle-left pull-right"></i></a>
							<ul class="treeview-menu">
								<li <?php echo strpos($nav_filename, 'sale_count') === false ? '' : 'class="active"'; ?>>
									<a href="<?php echo ADMIN_URL ?>/statistics/sale_count.php"><i class="fa fa-dot-circle-o"></i> 판매부수</a>
								</li>
								<li <?php echo strpos($nav_filename, 'sale_ranking') === false ? '' : 'class="active"'; ?>>
									<a href="<?php echo ADMIN_URL ?>/statistics/sale_ranking.php"><i class="fa fa-dot-circle-o"></i> 판매순위</a>
								</li>
								<li <?php echo strpos($nav_filename, 'sale_pay_method') === false ? '' : 'class="active"'; ?>>
									<a href="<?php echo ADMIN_URL ?>/statistics/sale_pay_method.php"><i class="fa fa-dot-circle-o"></i> 결제수단</a>
								</li>
								<li <?php echo strpos($nav_filename, 'sale_pay_ranking') === false ? '' : 'class="active"'; ?>>
									<a href="<?php echo ADMIN_URL ?>/statistics/sale_pay_ranking.php"><i class="fa fa-dot-circle-o"></i> 결제순위</a>
								</li>
							</ul>
						</li>
						<li class="treeview <?php echo strpos($nav_filename, 'book_') === false ? '' : 'active'; ?>">
							<a href="#"><i class="fa fa-circle-o"></i> <span>도서분석</span> <i class="fa fa-angle-left pull-right"></i></a>
							<ul class="treeview-menu">
								<li <?php echo strpos($nav_filename, 'book_ranking') === false ? '' : 'class="active"'; ?>>
									<a href="<?php echo ADMIN_URL ?>/statistics/book_ranking.php"><i class="fa fa-dot-circle-o"></i> 독서순위</a>
								</li>
								<li <?php echo strpos($nav_filename, 'book_buy_path') === false ? '' : 'class="active"'; ?>>
									<a href="<?php echo ADMIN_URL ?>/statistics/book_buy_path.php"><i class="fa fa-dot-circle-o"></i> 구입경로</a>
								</li>
								<li <?php echo strpos($nav_filename, 'book_favorite') === false ? '' : 'class="active"'; ?>>
									<a href="<?php echo ADMIN_URL ?>/statistics/book_favorite.php"><i class="fa fa-dot-circle-o"></i> 선호독서</a>
								</li>
								<li <?php echo strpos($nav_filename, 'book_interest') === false ? '' : 'class="active"'; ?>>
									<a href="<?php echo ADMIN_URL ?>/statistics/book_interest.php"><i class="fa fa-dot-circle-o"></i> 관심도서</a>
								</li>
								<li <?php echo strpos($nav_filename, 'book_read_time') === false ? '' : 'class="active"'; ?>>
									<a href="<?php echo ADMIN_URL ?>/statistics/book_read_time.php"><i class="fa fa-dot-circle-o"></i> 독서시점</a>
								</li>
								<li <?php echo strpos($nav_filename, 'book_read_rate') === false ? '' : 'class="active"'; ?>>
									<a href="<?php echo ADMIN_URL ?>/statistics/book_read_rate.php"><i class="fa fa-dot-circle-o"></i> 독서진행률</a>
								</li>
							</ul>
						</li>
						<li class="treeview <?php echo strpos($nav_filename, 'user_') === false ? '' : 'active'; ?>">
							<a href="#"><i class="fa fa-circle-o"></i> <span>회원분석</span> <i class="fa fa-angle-left pull-right"></i></a>
							<ul class="treeview-menu">
								<li <?php echo strpos($nav_filename, 'user_login.php') === false ? '' : 'class="active"'; ?>>
									<a href="<?php echo ADMIN_URL ?>/statistics/user_login.php"><i class="fa fa-dot-circle-o"></i> 로그인</a>
								</li>
								<li <?php echo strpos($nav_filename, 'user_join.php') === false ? '' : 'class="active"'; ?>>
									<a href="<?php echo ADMIN_URL ?>/statistics/user_join.php"><i class="fa fa-dot-circle-o"></i> 회원가입</a>
								</li>
								<li <?php echo strpos($nav_filename, 'user_buy') === false ? '' : 'class="active"'; ?>>
									<a href="<?php echo ADMIN_URL ?>/statistics/user_buy.php"><i class="fa fa-dot-circle-o"></i> 도서구매</a>
								</li>
								<li <?php echo strpos($nav_filename, 'user_type.php') === false ? '' : 'class="active"'; ?>>
									<a href="<?php echo ADMIN_URL ?>/statistics/user_type.php"><i class="fa fa-dot-circle-o"></i> 유형별분석</a>
								</li>
								<li <?php echo strpos($nav_filename, 'user_read.php') === false ? '' : 'class="active"'; ?>>
									<a href="<?php echo ADMIN_URL ?>/statistics/user_read.php"><i class="fa fa-dot-circle-o"></i> 독서현황</a>
								</li>
							</ul>
						</li>
						 -->
						<li <?php echo strpos($nav_filename, 'by_period.php') === false ? '' : 'class="active"'; ?>>
							<a href="<?php echo ADMIN_URL ?>/statistics/by_period.php"><i class="fa fa-circle-o"></i> 기간별</a>
						</li>
						<li <?php echo strpos($nav_filename, 'by_book.php') === false ? '' : 'class="active"'; ?>>
							<a href="<?php echo ADMIN_URL ?>/statistics/by_book.php"><i class="fa fa-circle-o"></i> 도서별</a>
						</li>
						<!-- li <?php echo strpos($nav_filename, 'by_user.php') === false ? '' : 'class="active"'; ?>>
							<a href="<?php echo ADMIN_URL ?>/statistics/by_user.php"><i class="fa fa-circle-o"></i> 회원별</a>
						</li> -->								
					</ul>
				</section>
				<!-- /.sidebar -->
			</aside>
			<!-- /.main-sidebar -->
			