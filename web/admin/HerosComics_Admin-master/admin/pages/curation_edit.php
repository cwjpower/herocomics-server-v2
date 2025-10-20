<?php
/*
 * 2016.10.10	softsyw
 * Desc : 큐레이팅 편집
 * 
 */
require_once '../../wps-config.php';
require_once FUNC_PATH . '/functions-page.php';
require_once FUNC_PATH . '/functions-book.php';

if ( empty($_GET['id']) ) {
	lps_js_back('큐레이팅을 선택해 주십시오.');
} else {
	$curation_id = $_GET['id'];
}

$crt_rows = lps_get_curation_by_id($curation_id);

$curation_title = $crt_rows['curation_title'];
$curation_sub_title = $crt_rows['curation_sub_title'];
$curation_content = $crt_rows['curation_content'];
$curation_status = $crt_rows['curation_status'];
$curator_level = $crt_rows['curator_level'];

$cover_img = unserialize($crt_rows['cover_img']);
$cover_path = $cover_img['file_path'];
$cover_url = $cover_img['file_url'];
$cover_name = $cover_img['file_name'];

$book_items = unserialize($crt_rows['curation_meta']);

//$book_lists = lps_get_books_by_user(wps_get_current_user_id());
$book_lists = lps_get_books_by_user();



require_once ADMIN_PATH . '/admin-header.php';

require_once './pages-lnb.php';
?>

			<link rel="stylesheet" href="<?php echo INC_URL ?>/css/jquery-ui.css">

			<!-- Content Wrapper. Contains page content -->
			<div class="content-wrapper">
				<!-- Content Header (Page header) -->
				<div class="content-header">
					<h1>
						큐레이팅 편집
					</h1>
					<ol class="breadcrumb">
						<li><a href="<?php echo ADMIN_URL ?>/admin.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li><a href="<?php echo ADMIN_URL ?>/pages/">페이지 관리</a></li>
						<li><a href="<?php echo ADMIN_URL ?>/pages/curation.php">큐레이팅</a></li>
						<li class="active"><b>큐레이팅 편집</b></li>
					</ol>
				</div>

				<!-- Main content -->
				<div class="content body">
					<form id="item-edit-form">
						<input type="hidden" name="curation_id" value="<?php echo $curation_id ?>">
						<div class="col-md-12">
							<table class="table table-bordered ls-table">
								<tbody>
									<tr>
										<td colspan="2">
											<div class="row">
												<div class="col col-md-5">
													<div class="box box-success">
														<div class="box-header">
															<div class="bg-olive color-palette pd-10">큐레이팅에 추가할 책</div>
														</div>
														<div class="box-body">
															<select id="pkg_books" name="pkg_books[]" class="form-control" multiple style="height: 200px;">
												<?php 
												foreach ($book_items as $key => $val) {
													$pkg_book = lps_get_book($val);
													$pkg_bk_id = $pkg_book['ID'];
													$pkg_bk_title = $pkg_book['book_title'];
													$pkg_bk_author = $pkg_book['author'];
												?>
																<option value="<?php echo $pkg_bk_id ?>"><?php echo $pkg_bk_title ?> (<?php echo $pkg_bk_author ?>)</option>
												<?php 
												}
												?>
															</select>
														</div>
													</div>
												</div>
												<div class="col col-md-1" style="padding-top: 20px;">
													<p style="margin-top: 10%; line-height: 100px;">
														<button type="button" id="add-book-btn" class="btn bg-olive btn-flat" title="추가합니다" style="width: 100%;">
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
					
						<div class="row">
							<div class="col-md-6">
								<div class="box box-info box-solid">
									<div class="box-body">
										<div class="form-group">
											<label>큐레이팅 제목 *</label>
											<input class="form-control" name="curation_title" placeholder="제목" required value="<?php echo $curation_title ?>">
										</div>
										<div class="form-group">
											<label>큐레이팅 소제목 *</label>
											<input class="form-control" name="curation_sub_title" placeholder="소제목" required value="<?php echo $curation_sub_title ?>">
										</div>
										<div class="form-group">
											<label>큐레이팅 내용 *</label>
											<textarea class="form-control" name="curation_content" id="" cols="30" rows="10"><?php echo $curation_content ?></textarea>

										</div>
										<div class="form-group">
											<label>상태</label>
								<?php 
								foreach ($wps_curation_status as $key => $val) {
									$checked = $key == $curation_status ? 'checked' : '';
								?>
											<div class="radio">
												<label>
													<input type="radio" name="curation_status" value="<?php echo $key ?>" <?php echo $checked ?>>
												 	<?php echo $val ?>
												</label>
											</div>
								<?php
								}
								?>
										</div>
									</div><!-- /.box-body -->
								</div><!-- /. box -->
							</div><!-- /.col -->
							<div class="col-md-6">
								<div class="box box-info">
									<div class="box-body">
										<div class="form-group">
											<div class="btn btn-warning btn-file">
												<i class="fa fa-image"></i> 표지 이미지 *
												<input type="file" name="attachment[]">
											</div>
											<p class="help-block">xxx * xxxpx 크기의 jpg, gif, png 파일 포멧 권장. Max. 1MB</p>
											<ul id="preview-attachment" class="list-group">
									<?php 
									if (is_file($cover_path)) {
									?>
												<li class="list-group-item">
													<input type="hidden" name="file_path[]" value="<?php echo $cover_path ?>">
													<input type="hidden" name="file_url[]" value="<?php echo $cover_url ?>">
													<input type="hidden" name="file_name[]" value="<?php echo $cover_name ?>">
													<img src="<?php echo $cover_url ?>" class="preview-attachment" style="border: 1px solid #eeeeee; max-width: 100%;">
												</li>
									<?php 
									}
									?>
											</ul>
										</div>
									</div><!-- /.box-body -->
								</div><!-- /. box -->
								
								<button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> 저장합니다</button> &nbsp;
								<a href="curation.php" class="btn btn-default btn-cancel">취소</a>
								
							</div><!-- /.col -->
						</div><!-- /.row -->
					</form>
				</div><!-- /.content -->
			</div><!-- /.content-wrapper -->
		
			<script src="<?php echo INC_URL ?>/js/jquery-ui.min.js"></script>
			<!-- jQuery Form plugin -->
			<script src="<?php echo INC_URL ?>/js/jquery/jquery.form.min.js"></script>
			<script src="<?php echo INC_URL ?>/js/jquery/jquery.serializeObject.min.js"></script>
			
			<script src="<?php echo INC_URL ?>/js/ls-util.js"></script>
			<script src="<?php echo INC_URL ?>/js/jquery/jquery.oLoader.min.js"></script>
			
			<script>
			$(function() {
				$("#item-edit-form").submit(function(e) {
					e.preventDefault();

					if ($("#pkg_books option").length == 0) {
						alert("책리스트에서 큐레이팅에서 사용할 책을 추가해 주십시오.");
						return false;
					}
					if ($('input[name="curation_title"]').val() == "") {
						alert("큐레이팅 제목을 입력해 주십시오.");
						return false;
					}
					if ($("#preview-attachment li").length == 0) {
						alert("표지 이미지 파일을 선택해 주십시오.");
						return false;
					}
					
					showLoader();

					$("#pkg_books option").prop("selected", true);

					$.ajax({
						type : "POST",
						url : "./ajax/curation-edit.php",
						data : $(this).serialize(),
						dataType : "json",
						success : function(res) {
							hideLoader();
							if ( res.code == "0" && res.result ) {
								location.reload();
							} else {
								alert( res.msg );
							}
						}
					});
				});
				
				$('input[name="attachment[]"]').change(function() {
					var size = this.files[0].size;
					var fext = this.files[0].name.split('.').pop().toLowerCase();
// 					var fname = this.files[0].name;

					if ( size > 1048576 ) {
						alert( "업로드할 수 있는 이미지 파일 용량은 1MB입니다.");
						$(this).val("");
						return;
					}

					if ( fext !=  'gif' && fext != 'jpg' && fext != 'png' ) {
						alert('확장자가 gif, jpg, png인 이미지 파일만 첨부해 주십시오.');
						$(this).val("");
						return;
					}

					showLoader();

					$("#item-edit-form").ajaxSubmit({
						type : "POST",
						url : "<?php echo INC_URL ?>/lib/upload-attachment.php",
						dataType : "json",
						success: function(xhr) {
							hideLoader();
							if ( xhr.code == "0" ) {
								for ( var i = 0; i < xhr.file_url.length; i++ ) {
									uploadedFiles =  '<li class="list-group-item">' +
														'<input type="hidden" name="file_path[]" value="' + xhr.file_path[i] + '">' +
														'<input type="hidden" name="file_url[]" value="' + xhr.file_url[i] + '">' +
														'<input type="hidden" name="file_name[]" value="' + xhr.file_name[i] + '">' +
														'<span class="badge bg-red delete-tmp" style="cursor: pointer;">삭제</span>' +
														'<img src="' + xhr.thumb_url[i] +
															'" title="' + xhr.file_name[i] +
															'" class="preivew-attachment" ' +
															'style="border: 1px solid #eeeeee; max-width: 100%;">' +
													'</li>';
								}
								$("#preview-attachment").html( uploadedFiles );
								$(".btn-file").fadeOut();
							} else {
								alert( xhr.msg );
							}
						}
					});
				});

				// File Deletion
				$(document).on("click", "#preview-attachment .delete-tmp", function() {
					var file = $(this).parent().find('input[name="file_path[]"]').val();
// 					if ( confirm("파일을 삭제하시겠습니까?") ) {
						$.ajax({
							type : "POST",
							url : "<?php echo INC_URL ?>/lib/delete-attachment.php",
							data : {
								"filePath" : file
							},
							dataType : "json",
							success : function(res) {
								$("#preview-attachment li").each(function(idx) {
									var file = $(this).find('input[name="file_path[]"]').val();
									if ( file == res.file_path ) {
										$(this).fadeOut("slow", function() { $(this).remove(); });
									}
								});
								if ( res.code != "0" ) {
									alert( res.msg );
								}
								$(".btn-file").fadeIn();
							}
						});
// 					}
				});

				// 책 추가
				$("#add-book-btn").click(function() {
					if ( $("#book-lists option:selected").length == 0 ) {
						alert("책 리스트에서 큐레이팅에 추가할 책를 선택해 주십시오.");
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
						alert("큐레이팅에서 삭제할 책를 선택해 주십시오.");
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

				// 책 검색
				$("#b_q").keypress(function(e) {
					if (e.which == 13) {
						e.preventDefault();
						searchBook();
					}
				});

				function searchBook() {
					var bt = $.trim($("#b_q").val());

					$.ajax({
						type : "POST",
						url : "../books/ajax/search-book.php",
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
				
			}); // $

			</script>

<?php 
require_once ADMIN_PATH . '/admin-footer.php';
?>