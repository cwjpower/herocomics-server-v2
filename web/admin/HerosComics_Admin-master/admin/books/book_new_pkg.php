<?php 
require_once '../../wps-config.php';
require_once FUNC_PATH . '/functions-fancytree.php';
require_once FUNC_PATH . '/functions-book.php';

// 책 카테고리
$book_cate_first = wps_fancytree_root_node_by_name('wps_category_books');
// 책 리스트
$book_lists = lps_get_books_by_user(wps_get_current_user_id());

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
						책 등록
					</h1>
					<ol class="breadcrumb">
						<li><a href="<?php echo ADMIN_URL ?>/admin.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li><a href="<?php ECHO ADMIN_URL ?>/books/">책 관리</a></li>
						<li><a href="#">책 등록</a></li>
						<li class="active"><b>세트 등록</b></li>
					</ol>
				</div>

				<!-- Main content -->
				<div class="content body">
				
		<?php 
		if (!wps_is_publisher()) {
		?>
					<div class="callout callout-danger lead">
						<h4>Tip!</h4>
						<p>책 등록은 출판사와 1인 작가만 할 수 있습니다.</p>
					</div>
		<?php 
		}
		?>
		
					<div class="well">
						<a href="book_new.php" class="btn btn-info btn-flat">단품 등록</a>
						<a href="#" class="btn btn-success btn-flat">세트 등록</a>
					</div>
				
					<form id="form-book-new">
						<input type="hidden" name="is_pkg" value="Y">
						<div class="box box-info">
							<div class="box-header">
								<h4>기본 정보 작성</h4>
							</div>
							<div class="box-body">
								<div class="row">
									<div class="col-md-12">
										<table class="table table-bordered ls-table">
											<tbody>
												<tr>
													<td colspan="2">
														<div class="row">
															<div class="col col-md-5">
																<div class="box box-danger">
																	<div class="box-header">
																		<div class="bg-maroon color-palette pd-10">세트에 추가할 책(세트)</div>
																	</div>
																	<div class="box-body">
																		<select id="pkg_books" name="pkg_books[]" class="form-control" multiple style="height: 200px;">
																		</select>
																	</div>
																</div>
															</div>
															<div class="col col-md-1" style="padding-top: 20px;">
																<p style="margin-top: 10%; line-height: 100px;">
																	<button type="button" id="add-book-btn" class="btn bg-maroon btn-flat" title="추가합니다" style="width: 100%;">
																		<span class="glyphicon glyphicon-triangle-left"></span>
																	</button>
																</p>
																<p>
																	<button type="button" id="remove-book-btn" class="btn bg-navy btn-flat" title="제거합니다" style="width: 100%;">
																		<span class="glyphicon glyphicon-triangle-right"></span>
																	</button>
																</p>
															</div>
															<div class="col col-md-6">
																<div class="box">
																	<div class="box-header">
																		<div class="form-inline">
																			<div class="form-group">
																				<div class="input-group">
																					<!-- span class="input-group-btn">
																						<a class="btn bg-navy">책 리스트</a>
																					</span -->
																					<input type="text" id="b_q" class="form-control" placeholder="책 제목을 입력해 주십시오" style="width: 350px;">
																					<span class="input-group-btn">
																						<button type="button" id="search-book-btn" class="btn btn-primary btn-flat">찾기</button>
																					</span>
																				</div>
																			</div>
																		</div>
																	</div>
																	<div class="box-body">
																		<select id="book-lists" class="form-control" multiple style="height: 200px;">
															<?php 
															if ( !empty($book_lists) ) {
																foreach ( $book_lists as $key => $val ) {
																	$ID = $val['ID'];
																	$author = $val['author'];
																	$book_title = $val['book_title'];
															?>
																			<option value="<?php echo $ID ?>"><?php echo $book_title ?> (<?php echo $author ?>)</option>
															<?php 
																}
															}
															?>
																		</select>
																	</div>
																</div>
															</div>
														</div>
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
													<td class="item-label">세트 이름 *</td>
													<td>
														<input type="text" id="book_title" name="book_title" class="form-control" required>
													</td>
												</tr>
									<?php 
									if (wps_get_user_level() == '6') {	// 1인 작가
									?>
												<tr>
													<td class="item-label">저자 *</td>
													<td>
														<input type="text" id="author" name="author" class="form-control" value="<?php echo wps_get_current_user_name() ?>" readonly>
													</td>
												</tr>
									<?php 
									} else {
									?>
												<tr>
													<td class="item-label">저자 *</td>
													<td>
														<input type="text" id="author" name="author" class="form-control" required>
													</td>
												</tr>
									<?php 
									}
									?>
												
									<?php 
									if (wps_get_user_level() == '7') {	// 출판사
									?>
												<tr>
													<td class="item-label">출판사 *</td>
													<td>
														<input type="text" id="publisher" name="publisher" class="form-control" value="<?php echo wps_get_current_user_name() ?>" readonly>
													</td>
												</tr>
									<?php 
									} else if (wps_get_user_level() == '6') {	// 1인 작가
									?>
												<tr>
													<td class="item-label">출판사 *</td>
													<td>
														<input type="text" id="publisher" name="publisher" class="form-control" value="북톡출판사" readonly>
													</td>
												</tr>
									<?php 
									} else {
									?>
												<tr>
													<td class="item-label">출판사 *</td>
													<td>
														<input type="text" id="publisher" name="publisher" class="form-control" required>
													</td>
												</tr>
									<?php 
									}
									?>
												<tr>
													<td class="item-label">정가 *</td>
													<td>
														<div class="input-group">
															<input type="text" id="normal_price" name="normal_price" class="form-control price" required>
															<span class="input-group-addon">원</span>
														</div>
													</td>
												</tr>
												<tr>
													<td class="item-label">판매가 *</td>
													<td>
														<div class="input-group">
															<input type="text" id="sale_price" name="sale_price" class="form-control price" required>
															<span class="input-group-addon">원</span>
														</div>
													</td>
												</tr>
												<tr>
													<td class="item-label">등록 형태 *</td>
													<td>
											<?php 
											foreach ($wps_upload_type as $key => $val ) {
												if (!empty($key)) {
											?>
														<label><input type="radio" name="upload_type" value="<?php echo $key ?>"> <?php echo $val ?></label>  &nbsp; &nbsp;
											<?php 
												}
											}
											?>
													</td>
												</tr>
												<tr>
													<td class="item-label">등록 기간</td>
													<td>
														<div class="input-group">
															<input type="text" id="period_from" name="period_from" class="form-control datepicker actual_range">
															<div class="input-group-addon">
																<i class="fa fa-calendar"></i>
															</div>
															<div class="input-group-addon">
																~
															</div>
															<input type="text" id="period_to" name="period_to" class="form-control datepicker actual_range">
															<div class="input-group-addon">
																<i class="fa fa-calendar"></i>
															</div>
														</div>
													</td>
												</tr>
												<tr>
													<td class="item-label">책 소개 *</td>
													<td>
														<textarea id="introduction_book" name="introduction_book" class="form-control ckeditor_content" required></textarea>
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
													<td class="item-label">저자 소개 *</td>
													<td>
														<textarea id="introduction_author" name="introduction_author" class="form-control ckeditor_content" required></textarea>
													</td>
												</tr>
												<tr>
													<td class="item-label">출판사 서평 *</td>
													<td>
														<textarea id="publisher_review" name="publisher_review" class="form-control ckeditor_content" required></textarea>
													</td>
												</tr>
												<tr>
													<td class="item-label">대표 이미지 *</td>
													<td>
														<div id="panel-cover-img">
															<div class="btn btn-warning btn-file">
																<i class="fa fa-image"></i> 대표 이미지
																<input type="file" name="cover_img[]" class="form-control">
															</div>
															<p class="help-block">jpg, gif, png 파일 포멧을 이용해 주십시오. Max. 5MB</p>
														</div>
														<ul id="preview-cover_img" class="list-group"></ul>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>	
								<div class="row">
									<div class="col-md-12">
										<h4>카테고리 선택</h4>
									</div>
									<div class="col-md-4">
										<div class="box box-warning">
											<div class="panel panel-warning">
												<div class="panel-heading">대분류 *</div>
												<ul id="category_first" class="list-group ls-book-category">
										<?php 
										if (!empty($book_cate_first)) {
											foreach ($book_cate_first as $key => $val) {
												$tid = $val['term_id'];
												$tname = $val['name'];
										?>
													<li class="list-group-item" id="tid-<?php echo $tid ?>"><?php echo $tname ?></li>
										<?php 
											}
										}
										?>
												</ul>
												<input type="hidden" name="category_first">
											</div>
										</div>
									</div>
									<div class="col-md-4">
										<div class="box box-info">
											<div class="panel panel-info">
												<div class="panel-heading">중분류 *</div>
												<ul id="category_second" class="list-group ls-book-category">
												</ul>
												<input type="hidden" name="category_second">
											</div>
										</div>
									</div>
									<div class="col-md-4">
										<div class="box box-success">
											<div class="panel panel-success">
												<div class="panel-heading">소분류 *</div>
												<ul id="category_third" class="list-group ls-book-category">
												</ul>
												<input type="hidden" name="category_third">
											</div>
										</div>
									</div>
								</div>
							</div>
							<!-- /.box-body -->
							<div class="box-footer">
					<?php 
					if (wps_is_publisher()) {
					?>
								<button type="submit" class="btn btn-primary">추가합니다</button> &nbsp;
								<button type="button" id="reset-btn" class="btn btn-default">초기화</button> &nbsp;
					<?php 
					} else {
					?>
								<div class="callout callout-danger lead">
									<h4>Tip!</h4>
									<p>책 등록은 출판사와 1인 작가만 할 수 있습니다.</p>
								</div>
					<?php 
					}
					?>
							</div>
						</div><!-- /.box-body -->
					</form>
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
				$("#reset-btn").click(function() {
					location.reload();
				});
				
				$("#form-book-new").submit(function(e) {
					e.preventDefault();

					if ($("#pkg_books option").length == 0) {
						alert("책리스트에서 세트로 책을 추가해 주십시오.");
						return false;
					}
					if ($("#book_title").val() == "") {
						alert("세트 이름을 입력해 주십시오.");
						return false;
					}
					if ($("#author").val() == "") {
						alert("저자를 입력해 주십시오.");
						return false;
					}
					if ($("#publisher").val() == "") {
						alert("츨판사를 입력해 주십시오.");
						return false;
					}
					if ($("#normal_price").val() == "") {
						alert("정가를 입력해 주십시오.");
						return false;
					}
					if ($("#sale_price").val() == "") {
						alert("판매가를 입력해 주십시오.");
						return false;
					}
					if ($('input[name="upload_type"]:checked').length == 0) {
						alert("등록형태를 입력해 주십시오.");
						return false;
					}
					if ($("#introduction_book").val() == "") {
						alert("책 소개를 입력해 주십시오.");
						return false;
					}
					if ($("#introduction_author").val() == "") {
						alert("저자 소개를 입력해 주십시오.");
						return false;
					}
					if ($("#publisher_review").val() == "") {
						alert("출판사 서평을 입력해 주십시오.");
						return false;
					}
					if ($("#preview-cover_img li").length == 0) {
						alert("대표이미지를 선택해 주십시오.");
						return false;
					}
					if ($('input[name="category_first"]').val() == "") {
						alert("대분류를 선택해 주십시오.");
						return false;
					}
					if ($('input[name="category_second"]').val() == "") {
						alert("중분류를 선택해 주십시오.");
						return false;
					}
					if ($('input[name="category_third"]').val() == "") {
						alert("소분류를 선택해 주십시오.");
						return false;
					}

					$("#pkg_books option").prop("selected", true);
					
					showLoader();
					$.ajax({
						type : "POST",
						url : "./ajax/book-new-pkg.php",
						data : $(this).serialize(),
						dataType : "json",
						success : function(res) {
							if (res.code == "0") {
								location.href = "./index.php";
							} else {
								hideLoader();
								alert(res.msg);
							}
						}
					});
				});

				// jquery ui calendar
				$( "#period_from" ).datepicker({
					dateFormat : "yy-mm-dd",
					monthNames: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
					monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
					dayNamesMin: ['일','월','화','수','목','금','토'],
					minDate: 0,
					changeMonth: true,
					changeYear: true,
					numberOfMonths: 2,
					showMonthAfterYear: true,
					onClose: function( selectedDate ) {
						$( "#period_to" ).datepicker( "option", "minDate", selectedDate );
					}
				}).inputmask('yyyy-mm-dd');
				
				$( "#period_to" ).datepicker({
					dateFormat : "yy-mm-dd",
					monthNames: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
					monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
					dayNamesMin: ['일','월','화','수','목','금','토'],
					defaultDate: "+1w",
					changeMonth: true,
					changeYear: true,
					showMonthAfterYear: true,
					onClose: function( selectedDate ) {
						$( "#period_from" ).datepicker( "option", "maxDate", selectedDate );
					}
				}).inputmask('yyyy-mm-dd');

				$("#isbn").inputmask("999-99-999999-9-9");
				$(".price").number(true).attr("maxlength", 9);

				// CKEditor
				var config = {
						height: 100,
						extraPlugins: 'autogrow',
						autoGrow_bottomSpace: 50,
						toolbar:
						[
							['FontSize', 'TextColor', 'Bold', 'Italic', 'Underline', '-', 'NumberedList', 'BulletedList', 'Blockquote', 'Table', '-', 'Undo', 'Redo', '-', 'SelectAll'],
							['UIColor']
						]
				}; 
				$(".ckeditor_content").ckeditor(config);

				// category
				$(document).on("click", "ul.ls-book-category .list-group-item", function() {
					var id = $(this).attr("id").replace(/\D/g, "");
					var category = $(this).closest("ul").attr("id");

					$(this).parent().find("li").removeClass("active");
					$(this).addClass("active");

					$('input[name="' + category + '"]').val(id);

					showLoader();

					$.ajax({
						type : "POST",
						url : "./ajax/get-child-category.php",
						data : {
							"id" : id,
							"level" : $(this).closest("ul").attr("id")
						},
						dataType : "json",
						success : function(res) {
							hideLoader();
							if (res.code != "0") {
								alert(res.msg);
							} else {
								if (res.target) {
									$("#" + res.target).html(res.lists);
								}
							}
						}
					});
				});

				// 표지이미지
				$('input[name="cover_img[]"]').change(function() {
					var size = this.files[0].size;
					var fext = this.files[0].name.split('.').pop().toLowerCase();
//					var fname = this.files[0].name;

					if ( size > 5242880 ) {
						alert( "업로드할 수 있는 이미지 파일 크기는 5MB입니다.");
						$(this).val("");
						return;
					}

					if ( fext !=  'gif' && fext != 'jpg' && fext != 'png' ) {
						alert('확장자가 gif, jpg, png인 이미지 파일만 첨부해 주십시오.');
						$(this).val("");
						return;
					}

					showLoader();

					$("#form-book-new").ajaxSubmit({
						type : "POST",
						url : "<?php echo INC_URL ?>/lib/upload-attachment.php",
						data : {
							"eleName" : $(this).attr("name")
						},
						dataType : "json",
						success: function(xhr) {
							hideLoader();
							if ( xhr.code == "0" ) {
								for ( var i = 0; i < xhr.file_url.length; i++ ) {
									uploadedFiles =  '<li class="list-group-item">' +
														'<input type="hidden" name="file_path_cover[]" value="' + xhr.file_path[i] + '">' +
														'<input type="hidden" name="file_url_cover[]" value="' + xhr.file_url[i] + '">' +
														'<input type="hidden" name="file_name_cover[]" value="' + xhr.file_name[i] + '">' +
														'<span class="badge bg-red delete-tmp" style="cursor: pointer;">삭제</span>' +
														'<img src="' + xhr.thumb_url[i] +
															'" title="' + xhr.file_name[i] +
															'" class="preivew-cover_img" ' +
															'style="border: 1px solid #eeeeee; max-width: 100%;">' +
													'</li>';
								}
								$("#preview-cover_img").append( uploadedFiles );
								$("#panel-cover-img").fadeOut();
							} else {
								alert( xhr.msg );
							}
						}
					});
					$('input[name="cover_img[]"]').val("");
				});

				// 표지이미지 삭제
				$(document).on("click", "#preview-cover_img .delete-tmp", function() {
					var file = $(this).parent().find('input[name="file_path_cover[]"]').val();
//					if ( confirm("파일을 삭제하시겠습니까?") ) {
						$.ajax({
							type : "POST",
							url : "<?php echo INC_URL ?>/lib/delete-attachment.php",
							data : {
								"filePath" : file
							},
							dataType : "json",
							success : function(res) {
								$("#preview-cover_img li").each(function(idx) {
									var file = $(this).find('input[name="file_path_cover[]"]').val();
									if ( file == res.file_path ) {
										$(this).fadeOut("slow", function() { $(this).remove(); });
									}
								});
								if ( res.code != "0" ) {
									alert( res.msg );
									if ( res.code == "404" ) {
										$("#preview-cover_img li").fadeOut();
									}
								}
								$("#panel-cover-img").fadeIn();
							}
						});
//					}
				});
				
				// 책 추가
				$("#add-book-btn").click(function() {
					if ( $("#book-lists option:selected").length == 0 ) {
						alert("책 리스트에서 세트에 추가할 책를 선택해 주십시오.");
					} else {
						$("#book-lists option:selected").each(function(idx) {
							var ovalue = $(this).val();
							var otext = $(this).text();
							var dup = 0;

							if ($("#pkg_books option").length > 0) {
								$("#pkg_books option").each(function(idx) {
									if (ovalue == $(this).val()) {
										dup = 1;
									}
								});
							}

							if (dup == 0) {
								$("#pkg_books").append($('<option>', {
									value : ovalue,
									text : otext
								}));
								$(this).remove();
							} else {
								alert("이미 추가된 책입니다.");
								return;
							}
						});
					}
				});

				// 책 삭제
				$("#remove-book-btn").click(function() {
					if ( $("#pkg_books option:selected").length == 0 ) {
						alert("세트에서 삭제할 책를 선택해 주십시오.");
					} else {
						$("#pkg_books option:selected").each(function(idx) {
							$("#book-lists").append($('<option>', {
								value : $(this).val(),
								text : $(this).text()
							}));
							$(this).remove();
						});
					}
				});

				// 책 검색
				$("#search-book-btn").click(function(e) {
					searchBook();
				});
				
				$("#b_q").keypress(function(e) {
					if (e.which == 13) {
						e.preventDefault();
						searchBook();
					}
				});

				// 정가 > 판매가
				$("#normal_price, #sale_price").blur(function() {
					var nprice = parseInt($("#normal_price").val().replace(/\D/g, ""));
					var sprice = parseInt($("#sale_price").val().replace(/\D/g, ""));

					if (nprice < sprice) {
						alert("판매가는 정가보다 적은 금액으로 입력해 주십시오.");
						$("#sale_price").val(nprice);
					}
				});

				function searchBook() {
					var bt = $.trim($("#b_q").val());

					$.ajax({
						type : "POST",
						url : "./ajax/search-book.php",
						data : {
							"q" : bt
						},
						dataType : "json",
						success : function(res) {
							if (res.code == "0") {
								$("#book-lists").empty().append(res.result);
							} else {
								alert(res.msg);
							}
						}
					});
				}
				
			}); //$
			</script>

<?php 
require_once ADMIN_PATH . '/admin-footer.php';
?>