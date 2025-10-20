<?php
/*
 * 2016.08.25	softsyw
 * Desc : 출판사 > 책 삭제 요청 화면
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
$book_title = $book_rows['book_title'];
$author = $book_rows['author'];
$publisher = $book_rows['publisher'];
$isbn = $book_rows['isbn'];
$normal_price = $book_rows['normal_price'];
$sale_price = $book_rows['sale_price'];
$upload_type = $book_rows['upload_type'];
$period_from = $book_rows['period_from'];
$period_to = $book_rows['period_to'];
$book_status = $book_rows['book_status'];
$user_id = $book_rows['user_id'];
$created_dt = $book_rows['created_dt'];

$category_first = $book_rows['category_first'];
$category_second = $book_rows['category_second'];
$category_third = $book_rows['category_third'];

$epub_path = $book_rows['epub_path'];
$epub_name = $book_rows['epub_name'];
$cover_img = $book_rows['cover_img'];

$book_meta = lps_get_book_meta($book_id);

$introduction_book = empty($book_meta['lps_introduction_book']) ? '' : $book_meta['lps_introduction_book'];
$introduction_author = empty($book_meta['lps_introduction_author']) ? '' : $book_meta['lps_introduction_author'];
$publisher_review = empty($book_meta['lps_publisher_review']) ? '' : $book_meta['lps_publisher_review'];
$book_table = empty($book_meta['lps_book_table']) ? '' : $book_meta['lps_book_table'];

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
						삭제 요청
					</h1>
					<ol class="breadcrumb">
						<li><a href="<?php echo ADMIN_URL ?>/admin.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li><a href="<?php ECHO ADMIN_URL ?>/books/">책 관리</a></li>
						<li><a href="<?php ECHO ADMIN_URL ?>/books/book_req_list.php">수정/삭제 요청</a></li>
						<li class="active"><b>삭제 요청</b></li>
					</ol>
				</div>

				<!-- Main content -->
				<div class="content body">
					<div class="box box-info">
						<div class="box-body">
							<div class="row">
								<div class="col-md-6">
									<table class="table table-bordered ls-table">
										<colgroup>
											<col style="width: 20%;">
											<col>
										</colgroup>
										<tbody>
											<tr>
												<td class="item-label">EPUB 파일</td>
												<td>
													<?php echo $epub_name ?>
										<?php 
										if (is_file($epub_path)) {
										?>
													<a href="<?php echo INC_URL ?>/lib/download-epub-file.php?id=<?php echo $book_id ?>"><span class="label label-success"><i class="fa fa-fw fa-download"></i> Download</span></a>
										<?php 
										}
										?>
												</td>
											</tr>
											<tr>
												<td class="item-label">제목</td>
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
											<tr>
												<td class="item-label">ISBN</td>
												<td>
													<?php echo $isbn ?>
												</td>
											</tr>
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
												<td class="item-label">분류</td>
												<td>
													<?php echo wps_get_term_name($category_first) ?> <i class="fa fa-chevron-right"></i>
													<?php echo wps_get_term_name($category_second) ?> <i class="fa fa-chevron-right"></i>
													<?php echo wps_get_term_name($category_third) ?>
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
													<?php echo $introduction_author ?>
												</td>
											</tr>
											<tr>
												<td class="item-label">출판사 서평</td>
												<td>
													<?php echo $publisher_review ?>
												</td>
											</tr>
											<tr>
												<td class="item-label">목차</td>
												<td>
													<?php echo $book_table ?>
												</td>
											</tr>
											<tr>
												<td class="item-label">표지 이미지</td>
												<td>
													<div id="panel-cover-img">
														<img src="<?php echo $cover_img ?>" title="이미지">
													</div>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>	
							<div class="alert alert-danger">
								<h3><i class="fa fa-chevron-circle-right"></i> 삭제 요청 사유를 작성해 주십시오</h3>
								<textarea name="req_reason" id="req_reason" class="form-control" style="height: 100px;"></textarea>
							</div>
						</div><!-- /.box-body -->
						<div class="box-footer">
							<button type="button" class="btn btn-primary" id="apply-btn">신청합니다</button> &nbsp;
							<button type="button" class="btn btn-default" id="cancel-btn">취소</button> &nbsp;
						</div>
					</div><!-- /.box -->
				</div><!-- /.content -->
			</div><!-- /.content-wrapper -->
			
			<script src="<?php echo INC_URL ?>/js/jquery-ui.min.js"></script>
			<script src="<?php echo INC_URL ?>/js/ls-util.js"></script>
			<script src="<?php echo INC_URL ?>/js/jquery/jquery.oLoader.min.js"></script>
			
			<script>
			$(function() {
				// 삭제 요청
				$("#apply-btn").click(function() {
					var reason = $.trim($("#req_reason").val());
					if (reason == "") {
						alert("삭제 요청 사유를 입력해 주십시오.");
						$("#req_reason").focus();
						return;
					}
					displayLoader();
					$.ajax({
						type : "POST",
						url : "./ajax/book-req-del.php",
						data : {
							"book_id" : "<?php echo $book_id ?>",
							"req_reason" : reason
						},
						dataType : "json",
						success : function(res) {
							if (res.code == "0") {
								if (res.result != true) {
									alert("해당 서적의 삭제 신청을 처리하지 못했습니다.");
								} else {
									alert("신청이 처리되었습니다.");
									location.href = "book_req_list.php";
								}
							} else {
								alert(res.msg);
							}
						} 
					});
				});

				$("#cancel-btn").click(function() {
					history.back();
				});
			}); //$
			</script>

<?php 
require_once ADMIN_PATH . '/admin-footer.php';
?>