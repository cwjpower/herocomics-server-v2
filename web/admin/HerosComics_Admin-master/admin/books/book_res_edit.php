<?php
/*
 * 2016.08.25	softsyw
 * Desc : 관리자가 수정요청(2001)중인 서적에 대해서 승인/거절을 처리하는 화면
 */
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
$is_free = $book_rows['is_free'];	// Y 무료, N 유료
$book_title = $book_rows['book_title'];
$author = $book_rows['author'];
$publisher = $book_rows['publisher'];
$comics_brand = $book_rows['comics_brand'];  // 마블 ,DC, 이미지 endstern 추가
$published_dt = $book_rows['published_dt'];  // 출간일
$isbn = $book_rows['isbn'];
$normal_price = $book_rows['normal_price'];
$sale_price = $book_rows['sale_price'];
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

$lps_req_reason = empty($book_meta['lps_req_reason_edit']) ? '' : $book_meta['lps_req_reason_edit'];

// 세트 상품
if (!strcmp($is_pkg, 'Y')) {
	$book_items = unserialize($book_meta['lps_pkg_book_list']);
}

// previous data
$pvd = unserialize($book_meta['lps_book_prev_data']);

$pvd_is_free = strcmp($pvd['is_free'], 'Y') ? '유료' : '무료';
$cud_is_free = strcmp($is_free, 'Y') ? '유료' : '무료';

require_once ADMIN_PATH . '/admin-header.php';

require_once './books-lnb.php';
?>

			<link rel="stylesheet" href="<?php echo INC_URL ?>/css/jquery-ui.css">
			
			<!-- Content Wrapper. Contains page content -->
			<div class="content-wrapper">
				<!-- Content Header (Page header) -->
				<div class="content-header">
					<h1>
						수정 요청 : <?php echo $book_title ?>
					</h1>
					<ol class="breadcrumb">
						<li><a href="<?php echo ADMIN_URL ?>/admin.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li><a href="<?php ECHO ADMIN_URL ?>/books/">책 관리</a></li>
						<li><a href="<?php ECHO ADMIN_URL ?>/books/book_req_edit_list.php">수정/삭제 요청 확인</a></li>
						<li class="active"><b>수정 요청</b></li>
					</ol>
				</div>

				<!-- Main content -->
				<div class="content body">
					<div class="box box-warning">
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
												
									<?php 
									if (!strcmp($pvd['lps_pkg_book_list'], $book_meta['lps_pkg_book_list'])) {	// same
									?>
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
									<?php 
									} else {	// diff
									?>
									
														<ul class="list-group"><!-- Previous data -->
											<?php 
											$pkg_bk_list = unserialize($pvd['lps_pkg_book_list']);
											foreach ($pkg_bk_list as $key => $val) {
												$pkg_book = lps_get_book($val);
												$pkg_bk_title = $pkg_book['book_title'];
											?>
														<li class="list-group-item"><?php echo $pkg_bk_title ?></li>
											<?php 
											}
											?>
														</ul>
													
														<ul class="list-group"><!-- Current data -->
											<?php 
											foreach ($book_items as $key => $val) {
												$pkg_book = lps_get_book($val);
												$pkg_bk_title = $pkg_book['book_title'];
											?>
														<li class="list-group-item list-group-item-info"><?php echo $pkg_bk_title ?></li>
											<?php 
											}
											?>
														</ul>
														
									<?php 
									}
									?>
													
												</td>
											</tr>
								<?php 
								} else {	// 단품
								?>
											<tr>
												<td class="item-label">EPUB 파일</td>
												<td>
													<?php echo lps_compare_both_data($pvd['epub_name'], $epub_name) ?>
												</td>
										</tr>
								<?php 
								}
								?>
											<tr>
												<td class="item-label"><?php echo !strcmp($is_pkg, 'Y') ? '세트 이름' : '제목'; ?></td>
												<td>
													<?php echo lps_compare_both_data($pvd['book_title'], $book_title) ?>
												</td>
											</tr>
											<tr>
												<td class="item-label">저자</td>
												<td>
													<?php echo lps_compare_both_data($pvd['author'], $author) ?>
												</td>
											</tr>
											<tr>
												<td class="item-label">출판사</td>
												<td>
													<?php echo lps_compare_both_data($pvd['publisher'], $publisher) ?>
												</td>
											</tr>

									<?php 
									if (!strcmp($is_pkg, 'N')) {	// 단품
									?>
											<tr>
												<td class="item-label">ISBN</td>
												<td>
													<?php echo lps_compare_both_data($pvd['isbn'], $isbn) ?>
												</td>
											</tr>
									<?php 
									}
									?>
											<tr>
												<td class="item-label">정가</td>
												<td>
													<div><?php echo lps_compare_both_data($pvd_is_free, $cud_is_free) ?></div>
													<div><?php echo lps_compare_both_data(number_format($pvd['normal_price']), number_format($normal_price)) ?></div>
												</td>
											</tr>
											<tr>
												<td class="item-label">판매가</td>
												<td>
													<?php echo lps_compare_both_data(number_format($pvd['sale_price']), number_format($sale_price)) ?>
												</td>
											</tr>
											<tr>
												<td class="item-label">코믹스 브랜드 <span class="label label-danger">추가</span></td>
												<td>
													<?php //echo lps_compare_both_data($wps_cmoics_brand[$pvd['comics_brand']], $wps_comics_brand[$comics_barnd]) ?>
													<?php echo $wps_comics_brand[$comics_brand] ?>
												</td>
											</tr>
											<tr>
												<td class="item-label">출간일 <span class="label label-danger">추가</span></td>
												<td>
													<?php echo substr($published_dt, 0, 10); ?>
												</td>
											</tr>
											<tr>
												<td class="item-label">등록 형태</td>
												<td>
													<?php echo lps_compare_both_data($wps_upload_type[$pvd['upload_type']], $wps_upload_type[$upload_type]) ?>
												</td>
											</tr>
											<tr>
												<td class="item-label">등록 기간</td>
												<td>
													<?php echo lps_compare_both_data($pvd['period_from'] . ' ~ ' . $pvd['period_to'], $period_from . ' ~ ' . $period_to) ?>
												</td>
											</tr>
											<tr>
												<td class="item-label">책 소개</td>
												<td>
											<?php 
											if (strcasecmp($introduction_book, $pvd['lps_introduction_book'])) {
												echo $pvd['lps_introduction_book'] . '<div class="callout callout-info">' . $introduction_book . '</div>'; 
											} else {
												echo $introduction_book;
											}
											?>
												</td>
											</tr>
											<tr>
												<td class="item-label">분류</td>
												<td>
													
											<?php 
											if ($category_first != $pvd['category_first'] || $category_second != $pvd['category_second'] || $category_third != $pvd['category_third']) {
											?>
													<?php echo wps_get_term_name($pvd['category_first']) ?>
													<i class="fa fa-chevron-right"></i>
													<?php echo wps_get_term_name($pvd['category_second']) ?>
													<i class="fa fa-chevron-right"></i>
													<?php echo wps_get_term_name($pvd['category_third']) ?>
													
													<code><i class="fa fa-arrow-circle-right"></i></code>
													
													<span class="label label-info" style="font-size: 100%;">
													<?php echo wps_get_term_name($category_first) ?>
													<i class="fa fa-chevron-right"></i>
													<?php echo wps_get_term_name($category_second) ?>
													<i class="fa fa-chevron-right"></i>
													<?php echo wps_get_term_name($category_third) ?>
													</span>
											<?php 
											} else {
											?>
											
											<?php 
											}
											?>
												</td>
											</tr>
											<tr>
												<td class="item-label">등록요청일</td>
												<td>
													<?php echo $created_dt ?>
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
											<?php 
											if (strcasecmp($introduction_author, $pvd['lps_introduction_author'])) {
												echo $pvd['lps_introduction_author'] . '<div class="callout callout-info">' . $introduction_author . '</div>'; 
											} else {
												echo $introduction_author;
											}
											?>
												</td>
											</tr>
											<tr>
												<td class="item-label">출판사 서평</td>
												<td>
											<?php 
											if (strcasecmp($publisher_review, $pvd['lps_publisher_review'])) {
												echo $pvd['lps_publisher_review'] . '<div class="callout callout-info">' . $publisher_review . '</div>'; 
											} else {
												echo $publisher_review;
											}
											?>
												</td>
											</tr>
									<?php 
									if (!strcmp($is_pkg, 'N')) {	// 단품
									?>
											<tr>
												<td class="item-label">목차</td>
												<td>
											<?php 
											if (strcasecmp($book_table, $pvd['lps_book_table'])) {
												echo $pvd['lps_book_table'] . '<div class="callout callout-info">' . $book_table . '</div>'; 
											} else {
												echo $book_table;
											}
											?>
												</td>
											</tr>
									<?php 
									}
									?>
											<tr>
												<td class="item-label">표지 이미지</td>
												<td>
													<div id="panel-cover-img">
											<?php 
											if (strcasecmp($cover_img, $pvd['cover_img'])) {
												echo '<img src="' . $pvd['cover_img'] . '">' . '<div class="callout callout-info"><img src="' . $cover_img . '" style="max-width: 100%;"></div>'; 
											} else {
												echo '<img src="' . $cover_img . '" style="max-width: 100%;">';
											}
											?>
													</div>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
								<div class="col-md-12">
									<table class="table table-bordered ls-table">
										<colgroup>
											<col style="width: 20%;">
											<col>
										</colgroup>
										<tbody>
											<tr>
												<td class="item-label">요청사유</td>
												<td>
													<?php echo nl2br($lps_req_reason) ?>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>	
							<div class="alert alert-danger">
								<h3><i class="fa fa-chevron-circle-right"></i> 수정 거절 사유</h3>
								<textarea name="res_reason" id="res_reason" class="form-control" style="height: 100px;"></textarea>
							</div>
						</div><!-- /.box-body -->
						<div class="box-footer text-center">
							<button type="button" id="btn-ok" class="btn btn-success">수락</button>
							<button type="button" id="btn-no" class="btn btn-danger">거절</button>
							<button type="button" id="btn-back" class="btn btn-default">취소</button>
						</div>
					</div><!-- /.box -->
				</div><!-- /.content -->
			</div><!-- /.content-wrapper -->
			
			<script src="<?php echo INC_URL ?>/js/jquery-ui.min.js"></script>
			<script src="<?php echo INC_URL ?>/js/ls-util.js"></script>
			<script src="<?php echo INC_URL ?>/js/jquery/jquery.oLoader.min.js"></script>
			
			<script>
			$(function() {
				// 승인
				$("#btn-ok").click(function() {
					displayLoader();
					$.ajax({
						type : "POST",
						url : "./ajax/response-for-book.php",
						data : {
							"id" : "<?php echo $book_id ?>",
							"type" : "accept_edit"
						},
						dataType : "json",
						success : function(res) {
							if (res.code == "0") {
								if (res.result == "0") {
									alert("해당 서적에 대해 승인하지 못했습니다.");
								} else {
									alert("승인했습니다.");
									location.href = "book_req_edit_list.php";
								}
							} else {
								alert(res.msg);
							}
						} 
					});
				});

				// 거절
				$("#btn-no").click(function() {
					var reason = $.trim($("#res_reason").val());
					if (reason == "") {
						alert("거절 사유를 입력해 주십시오.");
						$("#res_reason").focus();
						return;
					}
					displayLoader();
					$.ajax({
						type : "POST",
						url : "./ajax/response-for-book.php",
						data : {
							"id" : "<?php echo $book_id ?>",
							"type" : "reject_edit",
							"res_reason" :reason
						},
						dataType : "json",
						success : function(res) {
							if (res.code == "0") {
								if (res.result == "0") {
									alert("해당 서적에 대해 거절을 처리하지 못했습니다.");
								} else {
									alert("수정 신청을 거절했습니다.");
									location.href = "book_req_edit_list.php";
								}
							} else {
								alert(res.msg);
							}
						} 
					});
				});

				// 취소
				$("#btn-back").click(function() {
					location.href = "book_req_edit_list.php";
				});
			}); //$
			</script>

<?php 
require_once ADMIN_PATH . '/admin-footer.php';
?>