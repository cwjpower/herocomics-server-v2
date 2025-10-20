<?php
/*
 * 2016.10.07	softsyw
 * Desc : 큐레이팅 관리
 * 
 */
require_once '../../wps-config.php';
require_once FUNC_PATH . '/functions-page.php';
require_once FUNC_PATH . '/functions-book.php';


/**
 * 전체 관리자가 큐레이션을 할 것인가 출판사 사용자가 큐레이션을 할 것인가 확인 후 아이디 넘기는 부분 적용
 */
$book_lists = lps_get_books_by_user();


echo "<!-- DEBUG: pages-lnb.php included successfully -->";

$curations = lps_get_curations();

require_once ADMIN_PATH . '/admin-header.php';

require_once './pages-lnb.php';
?>

			<link rel="stylesheet" href="<?php echo INC_URL ?>/css/jquery-ui.css">

			<!-- Content Wrapper. Contains page content -->
			<div class="content-wrapper">
				<!-- Content Header (Page header) -->
				<div class="content-header">
					<h1>
						큐레이팅(Main 화면)
						<small><button type="button" id="btn-new-item" class="btn btn-info">등록</button></small>
					</h1>
					<ol class="breadcrumb">
						<li><a href="<?php echo ADMIN_URL ?>/admin.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li><a href="<?php echo ADMIN_URL ?>/pages/">페이지 관리</a></li>
						<li class="active"><b>큐레이팅</b></li>
					</ol>
				</div>

				<!-- Main content -->
				<div class="content body">
					<form id="item-new-form" class="hide">
					
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
											<input class="form-control" name="curation_title" placeholder="제목" required>
										</div>
                                        <div class="form-group">
                                            <label>큐레이팅 소제목 *</label>
                                            <input class="form-control" name="curation_sub_title" placeholder="소제목" required>
                                        </div>
                                        <div class="form-group">
                                            <label>큐레이팅 내용 *</label>
                                            <textarea class="form-control" name="curation_content" id="" cols="30" rows="10"></textarea>

                                        </div>

										<div class="form-group">
											<label>상태</label>
								<?php 
								foreach ($wps_curation_status as $key => $val) {
									$checked = $key ==  1001 ? 'checked' : '';
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
											<ul id="preview-attachment" class="list-group"></ul>
										</div>
									</div><!-- /.box-body -->
								</div><!-- /. box -->
								<p>
									<button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> 등록합니다</button>
								</p>
							</div><!-- /.col -->
						</div><!-- /.row -->
					</form>
					
					<div class="box box-primary">
						<div class="box-body">
							<form id="item-list-form">
								<table class="table table-striped table-hover">
									<thead>
										<tr>
											<!-- th><input type="checkbox" id="switch-all"></th> -->
											<th>No.</th>
											<th>제목</th>
											<th>작성자</th>
											<th>포함된 책</th>
											<th>등록일</th>
											<th>표지보기</th>
											<th>상태</th>
										</tr>
									</thead>
									<tbody id="sortable">
						<?php
						if ( !empty($curations) ) {
							foreach ( $curations as $key => $val ) {
								$curation_id = $val['ID'];
								$curation_title = $val['curation_title'];
								$cover_img = $val['cover_img'];
								$curation_order = $val['curation_order'];
								$curation_status = $val['curation_status'];
								$curator_level = $val['curator_level'];
								$curation_meta = $val['curation_meta'];
								$user_id = $val['user_id'];
								$created_dt = $val['created_dt'];
								
								$book_count = count(unserialize($curation_meta));
								
								$cover_file = unserialize($cover_img);
								
								$file_url = $cover_file['file_url'];
								$file_name = $cover_file['file_name'];
								
								$users = wps_get_user_by('ID', $user_id);
								$user_name = $users['user_name'];
		
						?>
										<tr id="curation-<?php echo $curation_id ?>">
											<td><?php echo $key + 1 ?></td>
											<td><?php echo $curation_title ?></td>
											<td><?php echo $user_name ?></td>
											<td><?php echo number_format($book_count) ?></td>
											<td><?php echo $created_dt ?></td>
											<td>
												<img src="<?php echo $file_url ?>" style="width: 100px;" title="<?php echo $file_name ?>">
												<input type="hidden" name="curation_id[]" value="<?php echo $curation_id ?>">
											</td>
											<td><?php echo $wps_curation_status[$curation_status] ?></td>
											<td>
												<a href="curation_edit.php?id=<?php echo $curation_id ?>" class="btn btn-xs btn-primary btn-edit">편집</a>
												<a href="javascript:;" class="btn btn-xs btn-danger btn-delete">삭제</a>
											</td>
										</tr>
						<?php
							}
						}
						?>
									</tbody>
								</table>
							</form>
						</div>
					</div><!-- /.box -->

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
				$("#btn-new-item").click(function() {
					if($("#item-new-form").hasClass("hide")) {
						$("#item-new-form").removeClass("hide");
						$(this).html("등록취소");
					} else {
						$("#item-new-form").addClass("hide");
						$(this).html("등록");
					}
				});
				
				$("#item-new-form").submit(function(e) {
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
						url : "./ajax/curation-new.php",
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

					$("#item-new-form").ajaxSubmit({
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
								$("#preview-attachment").append( uploadedFiles );
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

				$("#sortable").sortable({
			 		stop : function(event, ui) {
			 			reOderBanner();
			 		}
			 	});
				$("#sortable").disableSelection();

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

				// 큐레이팅 삭제
				$(".btn-delete").click(function() {
					var id = $(this).closest("tr").attr("id").replace(/\D/g, "");
					if ( !confirm("삭제하시겠습니까?") ) {
						return;
					}
					
					$.ajax({
						type : "POST",
						url : "./ajax/curation-delete.php",
						data : {
							"id" : id
						},
						dataType : "json",
						success : function(res) {
							if ( res.code == "0" ) {
								location.reload();
							} else {
								alert( res.msg );
							}
						}
					});
				});
				
			}); // $

			function reOderBanner() {
				$.ajax({
					type : "POST",
					url : "./ajax/curation-reorder.php",
					data : $("#item-list-form").serialize(),
					dataType : "json",
					success : function(res) {
						if ( res.code == "0" && res.result ) {
							location.reload();
						} else {
							alert( res.msg );
						}
					}
				});
			}
			</script>

<?php 
require_once ADMIN_PATH . '/admin-footer.php';
?>
