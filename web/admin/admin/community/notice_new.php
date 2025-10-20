<?php 
require_once '../../wps-config.php';
require_once FUNC_PATH . '/functions-book.php';

if ( empty($_GET['pt']) ) {
	lps_js_back('글의 종류에 대한 정보가 필요합니다.');
}
$post_type = $_GET['pt'];	// notice_new
$post_label = $wps_post_type[$post_type];

$post_type_secondary = 'community';

// 책 리스트
$book_lists = lps_get_books_by_user(wps_get_current_user_id());

$referer = $_SERVER['HTTP_REFERER'];

if (wps_is_admin()) {
	require_once ADMIN_PATH . '/admin-header.php';
} else {
	require_once ADMIN_PATH . '/agent-header.php';
}

require_once './community-lnb.php';
?>

			<link rel="stylesheet" href="<?php echo INC_URL ?>/css/jquery-ui.css">
			
			<!-- Content Wrapper. Contains page content -->
			<div class="content-wrapper">
				<!-- Content Header (Page header) -->
				<div class="content-header">
					<h1>
						<?php echo $post_label ?> 작성
					</h1>
					<ol class="breadcrumb">
						<li><a href="<?php echo ADMIN_URL ?>/admin.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li><a href="<?php ECHO ADMIN_URL ?>/community/">커뮤니티 관리</a></li>
						<li><a href="<?php ECHO ADMIN_URL ?>/community/">담벼락</a></li>
						<li><a href="#">공지사항 관리</a></li>
						<li class="active"><b>공지사항 작성</b></li>
					</ol>
				</div>

				<!-- Main content -->
				<div class="content body">
					<form id="item-new-form" method="post" enctype="multipart/form-data">
						<input type="hidden" name="post_type" value="<?php echo $post_type ?>">
						<input type="hidden" name="post_type_secondary" value="<?php echo $post_type_secondary ?>">
					
						<div class="box box-primary">
							<div class="box-body">
								<!-- div class="form-group">
									<label>
										<input type="checkbox" id="post_order" name="post_order" value="1"> 게시글을 상단에 노출합니다.
									</label>
								</div> -->
								<div class="form-group">
									<label>제목</label>
									<input type="text" id="post_title" name="post_title" class="form-control" placeholder="제목" required>
								</div>
								<div class="form-group">
									<label>등록범위</label> &nbsp; &nbsp;
									<input type="checkbox" id="post_status" name="post_status" value="all"> 모든 책에 등록합니다.
									<!-- 책 검색 -->
									<div class="col-md-12">
										<table class="table table-bordered ls-table">
											<tbody>
												<tr>
													<td colspan="2">
														<div class="row">
															<div class="col col-md-5">
																<div class="box box-danger">
																	<div class="box-header">
																		<div class="bg-maroon color-palette pd-10">공지사항을 등록할 책</div>
																	</div>
																	<div class="box-body">
																		<select id="notice_books" name="notice_books[]" class="form-control" multiple style="height: 200px;">
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
									
									<!-- /.책 검색 -->
									
									
								</div>
								<div class="form-group">
									<label>첨부파일</label>
									<input type="file" id="attachment" name="attachment[]" class="form-control">
								</div>
								<div class="form-group">
									<label>내용</label>
									<textarea id="post_content" name="post_content" class="form-control"></textarea>
								</div>
							</div><!-- /.box-body -->
							<div class="box-footer">
								<div class="pull-right">
									<button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> 등록합니다</button>
								</div>
								<button id="cancel-btn" type="button" class="btn btn-default"><i class="fa fa-times"></i> 취소</button>
							</div><!-- /.box-footer -->
						</div><!-- /. box -->
					</form>	
				</div><!-- /.content -->
			</div><!-- /.content-wrapper -->
			
			<!-- jQuery Form plugin -->
			<script src="<?php echo INC_URL ?>/js/jquery/jquery.form.min.js"></script>
			<script src="<?php echo INC_URL ?>/js/jquery/jquery.serializeObject.min.js"></script>
			
			<script src="<?php echo INC_URL ?>/js/jquery-ui.min.js"></script>
			<script src="<?php echo INC_URL ?>/js/ls-util.js"></script>
			<script src="<?php echo INC_URL ?>/js/jquery/jquery.oLoader.min.js"></script>
			<!-- CkEditor -->
			<script src="<?php echo INC_URL ?>/js/ckeditor-4.5.10/ckeditor.js"></script>
			<script src="<?php echo INC_URL ?>/js/ckeditor-4.5.10/adapters/jquery.js"></script>
			
			<script>
			$(function() {
				$("#item-new-form").submit(function(e) {
					e.preventDefault();
	
					if ( $.trim($("#post_title").val()) == "" ) {
						alert("제목을 입력해 주십시오.");
						$("#post_title").focus();
						return false;
					}
					if ($("#post_status").prop("checked") == false && $("#notice_books option").length == 0) {
						alert("책리스트에서 공지사항을 등록할 책을 추가해 주십시오.");
						return false;
					}
					if ( $.trim($("#post_content").val()) == "" ) {
						alert("내용을 입력해 주십시오.");
						return false;
					}

					$("#notice_books option").prop("selected", true);

					showLoader();

					$("#item-new-form").ajaxSubmit({
						type : "POST",
						url : "./ajax/notice-new.php",
// 						data : $(this).serialize(),
						data : $(this).serializeObject(),
						dataType : "json",
						success: function(xhr) {
							hideLoader();
							if ( xhr.code == "0" ) {
								location.href = "notice_list.php";
							} else {
								alert( xhr.msg );
							}
						}
					});
				});

				$("#post_content").ckeditor({});

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

				// 책 추가
				$("#add-book-btn").click(function() {
					if ( $("#book-lists option:selected").length == 0 ) {
						alert("책 리스트에서 공지사항을 등록할 책를 선택해 주십시오.");
					} else {
						$("#book-lists option:selected").each(function(idx) {
							var ovalue = $(this).val();
							var otext = $(this).text();
							var dup = 0;

							if ($("#notice_books option").length > 0) {
								$("#notice_books option").each(function(idx) {
									if (ovalue == $(this).val()) {
										dup = 1;
									}
								});
							}

							if (dup == 0) {
								$("#notice_books").append($('<option>', {
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
					if ( $("#notice_books option:selected").length == 0 ) {
						alert("공지사항에서 삭제할 책를 선택해 주십시오.");
					} else {
						$("#notice_books option:selected").each(function(idx) {
							$("#book-lists").append($('<option>', {
								value : $(this).val(),
								text : $(this).text()
							}));
							$(this).remove();
						});
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
			}); //$
			</script>

<?php 
require_once ADMIN_PATH . '/admin-footer.php';
?>