<?php
/*
 * Desc : 출판사 등록 , 오늘의 신간과 유사한 UI
 * 		단, 등록일, 등록기간, CI 에 대해선 연구 필요
 */
require_once '../../wps-config.php';
require_once FUNC_PATH . '/functions-page.php';

$publisher_lists = lps_get_users_by( 'user_level', 7 );

$publisher_rows = lps_get_publishers();

require_once ADMIN_PATH . '/admin-header.php';

require_once './pages-lnb.php';
?>

			<link rel="stylesheet" href="<?php echo INC_URL ?>/css/jquery-ui.css">

			<!-- Content Wrapper. Contains page content -->
			<div class="content-wrapper">
				<!-- Content Header (Page header) -->
				<div class="content-header">
					<h1>
						출판사 입점도서
						<small><button type="button" id="btn-new-item" class="btn btn-info">등록</button></small>
					</h1>
					<ol class="breadcrumb">
						<li><a href="<?php echo ADMIN_URL ?>/admin.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li><a href="<?php echo ADMIN_URL ?>/pages/">페이지 관리</a></li>
						<li class="active"><b>출판사 입점도서</b></li>
					</ol>
				</div>

				<!-- Main content -->
				<div class="content body">
					<form id="item-new-form" class="hide">
						<div class="row">
							<table class="table table-bordered ls-table">
								<tbody>
									<tr>
										<td colspan="2">
											<div class="row">
												<div class="col col-md-5">
													<div class="box box-success">
														<div class="box-header">
															<div class="bg-olive color-palette pd-10">선택 출판사</div>
														</div>
														<div class="box-body">
															<select id="publisher" name="publisher" class="form-control">
																<option value="">-출판사를 선택해 주십시오-</option>
															</select>
														</div>
													</div>
												</div>
												<div class="col col-md-1" style="padding-top: 20px;">
													<p style="margin-top: 10%; line-height: 100px;">
														<button type="button" id="add-publisher-btn" class="btn bg-olive btn-flat" title="추가합니다" style="width: 100%;">
															<span class="glyphicon glyphicon-triangle-left"></span>
														</button>
													</p>
													<p>
														<button type="button" id="remove-publisher-btn" class="btn bg-navy btn-flat" title="제거합니다" style="width: 100%;">
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
																		<input type="text" id="b_q" class="form-control" placeholder="출판사명을 입력해 주십시오" style="width: 350px;">
																		<span class="input-group-btn">
																			<button type="button" id="search-item-btn" class="btn btn-primary btn-flat">찾기</button>
																		</span>
																	</div>
																</div>
															</div>
														</div>
														<div class="box-body">
															<select id="publisher-lists" class="form-control" multiple style="height: 200px;">
												<?php 
												if ( !empty($publisher_lists) ) {
													foreach ( $publisher_lists as $key => $val ) {
														$ID = $val['ID'];
														$user_name = $val['user_name'];
														$display_name = $val['display_name'];
												?>
																<option value="<?php echo $ID ?>"><?php echo $user_name ?> (<?php echo $display_name ?>)</option>
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
											<label>등록기간 *</label>
											
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
											
										</div>
									</div><!-- /.box-body -->
								</div><!-- /. box -->
								<p class="text-right">
									<button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> 등록합니다</button>
								</p>
							</div><!-- /.col -->
							<div class="col-md-6">
								<div class="box box-info">
									<div class="box-body">
										<div class="form-group">
											<div class="btn btn-warning btn-file">
												<i class="fa fa-image"></i> 출판사 CI
												<input type="file" name="attachment[]">
											</div>
											<p class="text-danger">새로운 CI를 등록하시면 출판사의 프로필 사진을 수정합니다. </p>
											<ul id="preview-attachment" class="list-group"></ul>
										</div>
									</div><!-- /.box-body -->
								</div><!-- /. box -->
							</div><!-- /.col -->
						</div><!-- /.row -->
					</form>
					
					<div class="box box-primary">
						<div class="box-header">
							<button type="button" id="btn-remove-publisher" class="btn btn-danger btn-sm">삭제</button>
						</div>
						<div class="box-body">
							<form id="item-list-form">
								<table class="table table-striped table-hover">
									<thead>
										<tr>
											<th><input type="checkbox" id="switch-all"></th>
											<th>No.</th>
											<th>출판사</th>
											<th>등록일</th>
											<th>등록기간</th>
											<th>CI</th>
											<th>상태</th>
										</tr>
									</thead>
									<tbody id="sortable">
						<?php
						if ( !empty($publisher_rows) ) {
							foreach ( $publisher_rows as $key => $val ) {
								$pb_id = $val['ID'];
								$publisher_id = $val['publisher_id'];
								$period_from = $val['period_from'];
								$period_to = $val['period_to'];
								$created_dt = $val['created_dt'];
								
								$users = wps_get_user_by('ID', $publisher_id);
								$user_name = $users['user_name'];
								$avatar = lps_get_user_avatar($publisher_id);
						?>
										<tr>
											<td><input type="checkbox" class="pb_uid" name="pb_uid[]" value="<?php echo $pb_id ?>"></td>
											<td><?php echo $key + 1 ?></td>
											<td><?php echo $user_name ?></td>
											<td><?php echo $created_dt ?></td>
											<td><?php echo $period_from ?> ~ <?php echo $period_to ?></td>
											<td>
												<img src="<?php echo $avatar ?>" style="width: 100px;" title="<?php echo $file_name ?>">
											</td>
											<td>게시중</td>
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
			<!-- InputMask -->
			<script src="<?php echo ADMIN_URL ?>/js/jquery/input-mask/jquery.inputmask.js"></script>
			<script src="<?php echo ADMIN_URL ?>/js/jquery/input-mask/jquery.inputmask.date.extensions.js"></script>
			<script src="<?php echo ADMIN_URL ?>/js/jquery/input-mask/jquery.inputmask.extensions.js"></script>
			
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

					if ($("#publisher option").length == 1) {
						alert("출판사를 검색하여 선택 추가해 주십시오.");
						return false;
					}
					if ($("#period_from").val() == "") {
						alert("등록기간을 입력해 주십시오.");
						$("#period_from").focus();
						return false;
					}
					if ($("#period_to").val() == "") {
						alert("등록기간을 입력해 주십시오.");
						$("#period_to").focus();
						return false;
					}
					
					showLoader();

					$.ajax({
						type : "POST",
						url : "./ajax/publisher-new.php",
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

				$("#switch-all").click(function() {
					var chk = $(this).prop("checked");
					$(".pb_uid").prop("checked", chk);
				});

				// 
				$("#btn-remove-publisher").click(function() {
					var chkLength = $(".pb_uid:checked").length;

					if (chkLength == 0) {
						alert("출판사를 선택해 주십시오.");
						return;
					}

					showLoader();
					
					$.ajax({
						type : "POST",
						url : "./ajax/publisher-delete.php",
						data : $("#item-list-form").serialize(),
						dataType : "json",
						success : function(res) {
							if (res.code == "0") {
								location.reload();
							} else {
								hideLoader();
								alert(res.msg);
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

				// 검색한 출판사 추가
				$("#add-publisher-btn").click(function() {
					if ( $("#publisher-lists option:selected").length == 0 ) {
						alert("춮판사를  선택해 주십시오.");
					} else if ( $("#publisher-lists option:selected").length > 1 ) {
						alert("입점도서에 추가할 춮판사는 1곳만 선택해 주십시오.");
					} else {
						$("#publisher-lists option:selected").each(function(idx) {
							var ovalue = $(this).val();
							var otext = $(this).text();

							if ($("#publisher option").size() > 1) {
								movePublisher();
								$("#publisher option:eq(1)").replaceWith($('<option>', {
									value : ovalue,
									text : otext
								}));
							} else {
								$("#publisher").append($('<option>', {
									value : ovalue,
									text : otext
								}));
							}

							$(this).remove();
							$("#publisher option:eq(1)").attr("selected", "selected");

							getUserAvatar( $("#publisher option:eq(1)").val() );

						});
					}
				});

				$("#remove-publisher-btn").click(function() {
					movePublisher();
					$("#publisher option:eq(1)").remove();
				});
				
				// 선택에서 검색으로 출판사 이동
				function movePublisher() {
					$("#publisher option:eq(1)").each(function(idx) {
						$("#publisher-lists").append($('<option>', {
							value : $(this).val(),
							text : $(this).text()
						}));
					});
					$("#preview-attachment").html("");
				}

				// 출판사 검색
				$("#search-item-btn").click(function(e) {
					searchPublisher();
				});

				// 출판사 검색
				$("#b_q").keypress(function(e) {
					if (e.which == 13) {
						e.preventDefault();
						searchPublisher();
					}
				});

				function searchPublisher() {
					var bt = $.trim($("#b_q").val());

					$.ajax({
						type : "POST",
						url : "../users/ajax/search-publisher.php",
						data : {
							"q" : bt
						},
						dataType : "json",
						success : function(res) {
							if (res.code == "0") {
								$("#publisher-lists").empty().append(res.result);
							} else {
								alert(res.msg);
							}
						}
					});
				}

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
				
			}); // $

			function reOderBanner() {
				$(".pb_uid").prop("checked", true);	// checkbox checked
				
				$.ajax({
					type : "POST",
					url : "./ajax/publisher-reorder.php",
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

			function getUserAvatar(userid) {
				$.ajax({
					type : "POST",
					url : "../users/ajax/get-user-meta.php",
					data : {
						"user_id" : userid,
						"meta_key" : "wps_user_avatar"
					},
					dataType : "json",
					success : function(res) {
						if ( res.code == "0" ) {
							var avatar = '/includes/images/common/photo-default.png';
							if ( res.user_meta ) {
								avatar = res.user_meta;
							}
							$("#preview-attachment").append('<li class="list-group-item"><img src="' + avatar + '" class="preivew-attachment" style="border: 1px solid #eeeeee; max-width: 100%;"></li>');
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