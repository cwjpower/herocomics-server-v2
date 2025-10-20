<?php 

echo "<!-- DEBUG: pages-lnb.php loaded -->";
error_log("pages-lnb.php loaded");
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
						<li <?php echo strpos($nav_filename, 'index.php') === false ? '' : 'class="active"'; ?>>
							<a href="<?php echo ADMIN_URL ?>/pages/"><i class="fa fa-circle-o"></i> 공지사항</a>
						</li>
						<li <?php echo strpos($nav_filename, 'news_list.php') === false ? '' : 'class="active"'; ?>>
							<a href="<?php echo ADMIN_URL ?>/pages/news_list.php"><i class="fa fa-circle-o"></i> 뉴스 관리</a>
						</li>
						<li <?php echo strpos($nav_filename, 'todays_new.php') === false ? '' : 'class="active"'; ?>>
							<a href="<?php echo ADMIN_URL ?>/pages/todays_new.php"><i class="fa fa-circle-o"></i> 오늘의 신간</a>
						</li>
						<li <?php echo strpos($nav_filename, 'best.php') === false ? '' : 'class="active"'; ?>>
							<a href="<?php echo ADMIN_URL ?>/pages/best.php"><i class="fa fa-circle-o"></i> 베스트(랭킹)</a>
						</li>
						<li <?php echo strpos($nav_filename, 'curation') === false ? '' : 'class="active"'; ?>>
							<a href="<?php echo ADMIN_URL ?>/pages/curation.php"><i class="fa fa-circle-o"></i> 추천도서 관리 </a>
						</li>
						<li class="header">배너 관리</li>

						<li <?php echo strpos($nav_filename, 'banner.php') === false ? '' : 'class="active"'; ?>>
							<a href="<?php echo ADMIN_URL ?>/pages/banner.php?section=main"><i class="fa fa-circle-o"></i> 메인 섹션 배너</a>
						</li>
						<li <?php echo strpos($nav_filename, 'banner.php') === false ? '' : 'class="active"'; ?>>
							<a href="<?php echo ADMIN_URL ?>/pages/banner.php?section=book"><i class="fa fa-circle-o"></i> 북 섹션 배너</a>
						</li>
						<li class="treeview <?php echo strpos($nav_filename, 'cs_') === false ? '' : 'active'; ?>">
							<a href="#"><i class="fa fa-circle-o"></i> <span>고객 센터 콘텐츠 관리 </span> <i class="fa fa-angle-left pull-right"></i></a>
							<ul class="treeview-menu">
								<li <?php echo strpos($nav_filename, 'cs_faq_') === false ? '' : 'class="active"'; ?>>
									<a href="<?php echo ADMIN_URL ?>/pages/cs_faq_list.php"><i class="fa fa-dot-circle-o"></i> 자주묻는질문</a>
								</li>
								<li <?php echo strpos($nav_filename, 'cs_faq_category') === false ? '' : 'class="active"'; ?>>
									<a href="<?php echo ADMIN_URL ?>/pages/cs_faq_category.php"><i class="fa fa-dot-circle-o"></i> 질문유형 그룹</a>
								</li>
								<li <?php echo strpos($nav_filename, 'cs_terms.php') === false ? '' : 'class="active"'; ?>>
									<a href="<?php echo ADMIN_URL ?>/pages/cs_terms.php"><i class="fa fa-dot-circle-o"></i> 이용약관,개인정보취급</a>
								</li>
							</ul>
						</li>
						<li class="header">미사용 메뉴</li>
						<li <?php echo strpos($nav_filename, 'publisher.php') === false ? '' : 'class="active"'; ?>>
							<a href="<?php echo ADMIN_URL ?>/pages/publisher.php"><i class="fa fa-circle-o"></i> 출판사 입점 도서</a>
						</li>

					</ul>
				</section>
				<!-- /.sidebar -->
			</aside>
			<!-- /.main-sidebar -->
			
