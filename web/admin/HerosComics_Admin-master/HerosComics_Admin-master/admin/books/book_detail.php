<?php 
require_once '../../wps-config.php';
require_once FUNC_PATH . '/functions-book.php';
require_once FUNC_PATH . '/functions-term.php';
require_once FUNC_PATH . '/functions-fancytree.php';

if ( empty($_GET['id']) ) {
	lps_js_back( '도서 아이디가 존재하지 않습니다.' );
} else {
	$book_id = $_GET['id'];
}

$book_rows = lps_get_book($book_id);

$is_pkg = $book_rows['is_pkg'];
$book_title = $book_rows['book_title'];
$author = $book_rows['author'];
$publisher = $book_rows['publisher'];
$isbn = $book_rows['isbn'];
$normal_price = $book_rows['normal_price'];
$sale_price = $book_rows['sale_price'];
$comics_brand = $book_rows['comics_brand'];
$published_dt = $book_rows['published_dt'];
$upload_type = $book_rows['upload_type'];
$period_from = $book_rows['period_from'];
$period_to = $book_rows['period_to'];
$book_status = $book_rows['book_status'];
$user_id = $book_rows['user_id'];
$created_dt = $book_rows['created_dt'];

$epub_path = $book_rows['epub_path'];
$epub_name = $book_rows['epub_name'];
$cover_img = $book_rows['cover_img'];

$category_first = $book_rows['category_first'];
$category_second = $book_rows['category_second'];
$category_third = $book_rows['category_third'];

$book_meta = lps_get_book_meta($book_id);

$introduction_book = empty($book_meta['lps_introduction_book']) ? '' : $book_meta['lps_introduction_book'];
$introduction_author = empty($book_meta['lps_introduction_author']) ? '' : $book_meta['lps_introduction_author'];
$publisher_review = empty($book_meta['lps_publisher_review']) ? '' : $book_meta['lps_publisher_review'];
$book_table = empty($book_meta['lps_book_table']) ? '' : $book_meta['lps_book_table'];
$lps_res_accept_new = empty($book_meta['lps_res_accept_new']) ? '': unserialize($book_meta['lps_res_accept_new']);
if ($lps_res_accept_new) {
	$book_new_accept_date = date('Y-m-d H:i:s', $lps_res_accept_new['accept_dt']);
} else {
	$book_new_accept_date = '';
}

// 거절 사유
if ($book_status == '4000') {
	$bmeta = unserialize($book_meta['lps_res_reject_new']);
	$reject_reason = $bmeta['reject_reason'];
	$reject_dt = date('Y.m.d H:i', $bmeta['reject_dt']);
} else if ($book_status == '4001') {
	$bmeta = unserialize($book_meta['lps_res_reject_edit']);
	$reject_reason = $bmeta['reject_reason'];
	$reject_dt = date('Y.m.d H:i', $bmeta['reject_dt']);
} else if ($book_status == '4101') {
	$bmeta = unserialize($book_meta['lps_res_reject_delete']);
	$reject_reason = $bmeta['reject_reason'];
	$reject_dt = date('Y.m.d H:i', $bmeta['reject_dt']);
} else {
	$reject_reason = '';
	$reject_dt = '';
}

// 책 카테고리
$book_cate_first = wps_fancytree_root_node_by_name('wps_category_books');

// 세트 상품
if (!strcmp($is_pkg, 'Y')) {
	$book_items = unserialize($book_meta['lps_pkg_book_list']);
}

if (wps_is_admin()) {
	require_once ADMIN_PATH . '/admin-header.php';
} else {
	require_once ADMIN_PATH . '/agent-header.php';
}

require_once './books-lnb.php';
?>

			<link rel="stylesheet" href="<?php echo INC_URL ?>/css/jquery-ui.css">
			
			<!-- Content Wrapper. Contains page content -->
			<div class="content-wrapper">
				<!-- Content Header (Page header) -->
				<div class="content-header">
					<h1>
						<?php echo $book_title ?>
						<small>
							<?php echo wps_get_term_name($category_first) ?> <i class="fa fa-chevron-right"></i>
							<?php echo wps_get_term_name($category_second) ?> <i class="fa fa-chevron-right"></i>
							<?php echo wps_get_term_name($category_third) ?>
						</small>
					</h1>
					<ol class="breadcrumb">
						<li><a href="<?php echo ADMIN_URL ?>/admin.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li><a href="<?php ECHO ADMIN_URL ?>/books/">책 관리</a></li>
						<li><a href="<?php ECHO ADMIN_URL ?>/books/">책 리스트</a></li>
						<li class="active"><b>책 상세 보기</b></li>
					</ol>
				</div>

				<!-- Main content -->
				<div class="content body">
					<div class="box box-info">
			<?php 
			if ($book_status >= 4000) {
			?>
						<div class="box-header">
							<div class="well">
								<h4>거절사유</h4>
								<?php echo nl2br($reject_reason) ?>
								<span class="label label-info pull-right"><?php echo $reject_dt ?></span>
							</div>
						</div>
			<?php 
			}
			?>
						<div class="box-body">
							<div class="row">
								<div class="col-md-6">
									<table class="table table-bordered ls-table">
										<colgroup>
											<col style="width: 20%;">
											<col>
										</colgroup>
										<tbody>
											
									<?php 
									if (!empty($book_items) && !strcmp($is_pkg, 'Y')) {
									?>
											<tr>
												<td class="item-label">책</td>
												<td>
													<ul class="list-group">
										<?php 
										foreach ($book_items as $key => $val) {
											$pkg_book = lps_get_book($val);
											$pkg_bk_title = $pkg_book['book_title'];
										?>
													<li class="list-group-item"><?php echo $pkg_bk_title ?></li>
										<?php 
										}
										?>
													</ul>
												</td>
											</tr>
									<?php 
									} else {	// 단품
									?>
											<tr>
												<td class="item-label">EPUB 파일</td>
												<td>
													<?php echo $epub_name ?>
										<?php 
										if (is_file($epub_path)) {
										?>
													<!-- a href="<?php echo INC_URL ?>/lib/download-epub-file.php?id=<?php echo $book_id ?>"><span class="label label-success"><i class="fa fa-fw fa-download"></i> Download</span></a> -->
										<?php 
										}
										?>

												</td>
											</tr>
									<?php 
									}
									?>
											<tr>
												<td class="item-label"><?php echo !strcmp($is_pkg, 'Y') ? '세트 이름' : '제목'; ?></td>
												<td>
													<?php echo $book_title ?>
												</td>
											</tr>
											<tr>
												<td class="item-label">저자</td>
												<td>
													<?php echo $author ?>
												</td>
											</tr>
											<tr>
												<td class="item-label">출판사</td>
												<td>
													<?php echo $publisher ?>
												</td>
											</tr>
									<?php 
									if (!strcmp($is_pkg, 'N')) {	// 단품
									?>
											<tr>
												<td class="item-label">ISBN</td>
												<td>
													<?php echo $isbn ?>
												</td>
											</tr>
									<?php 
									}
									?>
											<tr>
												<td class="item-label">정가</td>
												<td>
													<?php echo number_format($normal_price) ?>
												</td>
											</tr>
											<tr>
												<td class="item-label">판매가</td>
												<td>
													<?php echo number_format($sale_price) ?>
												</td>
											</tr>
											<tr>
												<td class="item-label">코믹스 브랜드 <span class="label label-danger">추가</span></td>
												<td>
													<?php echo $wps_comics_brand[$comics_brand] ?>
												</td>
											</tr>
											<tr>
												<td class="item-label">출간일 <span class="label label-danger">추가</span></td>
												<td>
													<?php echo substr($published_dt, 0, 10);  ?>
												</td>
											</tr>
											<tr>
												<td class="item-label">등록 형태</td>
												<td>
													<?php echo $wps_upload_type[$upload_type] ?>
												</td>
											</tr>
											<tr>
												<td class="item-label">등록 기간</td>
												<td>
													<?php echo $period_from ?> ~ <?php echo $period_to ?>
												</td>
											</tr>
											<tr>
												<td class="item-label">책 소개</td>
												<td>
													<?php echo $introduction_book ?>
												</td>
											</tr>
											<tr>
												<td class="item-label">등록요청날짜</td>
												<td>
													<?php echo $created_dt ?>
												</td>
											</tr>
											<tr>
												<td class="item-label">등록완료날짜</td>
												<td>
													<?php echo $book_new_accept_date ?>
												</td>
											</tr>
											<tr>
												<td class="item-label">상태</td>
												<td>
													<?php echo $wps_book_status[$book_status] ?>
												</td>
											</tr>

											<tr>
												<td class="item-label">리딩오더 before</td>
												<td>
													<?php //echo $wps_book_status[$book_status] ?>
												</td>
											</tr>
											<tr>
												<td class="item-label">리딩오더 after</td>
												<td>
													<?php //echo $wps_book_status[$book_status] ?>
												</td>
											</tr>

										</tbody>
									</table>
								</div>
								<div class="col-md-6">
									<table class="table table-bordered ls-table">
										<colgroup>
											<col style="width: 20%;">
											<col>
										</colgroup>
										<tbody>
											<tr>
												<td class="item-label">저자 소개</td>
												<td>
													<?php echo $introduction_author ?>
												</td>
											</tr>
											<tr>
												<td class="item-label">출판사 서평</td>
												<td>
													<?php echo $publisher_review ?>
												</td>
											</tr>
									<?php 
									if (!strcmp($is_pkg, 'N')) {	// 단품
									?>
											<tr>
												<td class="item-label">목차</td>
												<td>
													<?php echo $book_table ?>
												</td>
											</tr>
									<?php 
									}
									?>
											<tr>
												<td class="item-label"><?php echo !strcmp($is_pkg, 'Y') ? '대표 이미지' : '표지 이미지'; ?></td>
												<td>
													<div id="panel-cover-img">
														<img src="<?php echo $cover_img ?>" title="이미지" style="max-width: 100%;">
													</div>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>	
						</div><!-- /.box-body -->
						<div class="box-footer">
							<button type="button" id="btn-back" class="btn btn-primary">뒤로가기</button>
						</div>
					</div><!-- /.box -->
				</div><!-- /.content -->
			</div><!-- /.content-wrapper -->
			
			<script src="<?php echo INC_URL ?>/js/jquery-ui.min.js"></script>
			<script src="<?php echo INC_URL ?>/js/ls-util.js"></script>
			<script src="<?php echo INC_URL ?>/js/jquery/jquery.oLoader.min.js"></script>
			<!-- InputMask -->
			<script src="<?php echo ADMIN_URL ?>/js/jquery/input-mask/jquery.inputmask.js"></script>
			<script src="<?php echo ADMIN_URL ?>/js/jquery/input-mask/jquery.inputmask.date.extensions.js"></script>
			<script src="<?php echo ADMIN_URL ?>/js/jquery/input-mask/jquery.inputmask.extensions.js"></script>
			<!-- Number -->
			<script src="<?php echo INC_URL ?>/js/jquery/jquery.number.min.js"></script>
			<!-- CkEditor -->
			<script src="<?php echo INC_URL ?>/js/ckeditor-4.5.10/ckeditor.js"></script>
			<script src="<?php echo INC_URL ?>/js/ckeditor-4.5.10/adapters/jquery.js"></script>
			<!-- Form(file) -->
			<script src="<?php echo INC_URL ?>/js/jquery/jquery.form.min.js"></script>
			
			<script>
			$(function() {
				$("#btn-back").click(function() {
					history.back();
				});
			}); //$
			</script>

<?php 
require_once ADMIN_PATH . '/admin-footer.php';
?>