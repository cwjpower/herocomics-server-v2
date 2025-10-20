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
						<li class="header">책 관리</li>
						<li <?php echo strpos($nav_filename, 'index.php') === false ? '' : 'class="active"'; ?>>
							<a href="<?php echo ADMIN_URL ?>/books/"><i class="fa fa-circle-o"></i> 책 리스트</a>
						</li>
			<?php //if (!wps_is_admin()) : ?>
						<li <?php echo strpos($nav_filename, 'book_new') === false ? '' : 'class="active"'; ?>>
							<a href="<?php echo ADMIN_URL ?>/books/book_new.php"><i class="fa fa-circle-o"></i> 책 등록</a>
						</li>
						<li <?php echo strpos($nav_filename, 'book_req_edit.php') === false ? '' : 'class="active"'; ?>>
							<a href="<?php echo ADMIN_URL ?>/books/book_req_list.php"><i class="fa fa-circle-o"></i> 수정/삭제 요청</a>
						</li>
			<?php //endif;?>
			<?php 
			if (wps_is_admin()) {
			?>
						<li class="header">책 등록 / 수정 관리 </li>
						<li <?php echo strpos($nav_filename, 'book_req_new_list.php') === false ? '' : 'class="active"'; ?>>
							<a href="<?php echo ADMIN_URL ?>/books/book_req_new_list.php"><i class="fa fa-circle-o text-red"></i> 등록 요청 확인</a>
						</li>
						<li <?php echo strpos($nav_filename, 'book_req_edit_list.php') === false ? '' : 'class="active"'; ?>>
							<a href="<?php echo ADMIN_URL ?>/books/book_req_edit_list.php"><i class="fa fa-circle-o text-red"></i> 수정/삭제 요청 확인</a>
						</li>
						<li class="header">부가정보관리</li>
						<li <?php echo strpos($nav_filename, 'category.php') === false ? '' : 'class="active"'; ?>>
							<a href="<?php echo ADMIN_URL ?>/books/category.php"><i class="fa fa-circle-o text-red"></i> 책 분류 관리</a>
						</li>
						<li <?php echo strpos($nav_filename, 'book_trailer_list.php') === false ? '' : 'class="active"'; ?>>
							<a href="<?php echo ADMIN_URL ?>/books/book_trailer_list.php"><i class="fa fa-circle-o text-red"></i> 북트레일러 관리</a>
						</li>
<!--						<li --><?php //echo strpos($nav_filename, 'book_preview.php') === false ? '' : 'class="active"'; ?><!-->-->
<!--							<a href="--><?php //echo ADMIN_URL ?><!--/books/book_preview.php"><i class="fa fa-circle-o text-red"></i> 미리보기 관리</a>-->
<!--						</li>-->
			<?php 
			}
			?>
						
					</ul>
				</section>
				<!-- /.sidebar -->
			</aside>
			<!-- /.main-sidebar -->
			