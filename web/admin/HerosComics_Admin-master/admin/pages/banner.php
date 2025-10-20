<?php
/*
 * 2016.09.26	softsyw
 * Desc : 배너관리
 * 
 */
require_once '../../wps-config.php';
require_once FUNC_PATH . '/functions-page.php';

$section = $_GET['section'];
if(!isset($section))
	$section = 'main';

$banners = lps_get_banners($section);

require_once ADMIN_PATH . '/admin-header.php';

require_once './pages-lnb.php';
?>

			<link rel="stylesheet" href="<?php echo INC_URL ?>/css/jquery-ui.css">

			<!-- Content Wrapper. Contains page content -->
			<div class="content-wrapper">
				<!-- Content Header (Page header) -->
				<div class="content-header">
					<h1>
						<?php echo $section; ?> 배너
						<small><button type="button" id="btn-new-item" class="btn btn-info">등록</button></small>
					</h1>
					<ol class="breadcrumb">
						<li><a href="<?php echo ADMIN_URL ?>/admin.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li><a href="<?php echo ADMIN_URL ?>/pages/">페이지 관리</a></li>
						<li class="active"><b>배너</b></li>
					</ol>
				</div>

				<!-- Main content -->
				<div class="content body">
					<form id="item-new-form" method="post" enctype="multipart/form-data" class="hide">
						<input type="hidden" name="bnr_section" value="<?php echo $section; ?>">
						<div class="row">
							<div class="col-md-4">
								<div class="box box-info box-solid">
									<div class="box-body">
										<div class="form-group">
											<label>배너 제목 *</label>
											<input class="form-control" name="banner_title" placeholder="제목" required>
										</div>
										<div class="form-group">
											<label>배너 등록기간</label>
											<div class="input-group">
												<input type="text" id="banner_from1" name="banner_from" class="form-control datepicker actual_range" value="<?php echo date('Y-m-d') ?>">
												<div class="input-group-addon">
													<i class="fa fa-calendar"></i>
												</div>
												<div class="input-group-addon">
													~
												</div>
												<input type="text" id="banner_to1" name="banner_to" class="form-control datepicker actual_range">
												<div class="input-group-addon">
													<i class="fa fa-calendar"></i>
												</div>
											</div>
										</div>
										<div class="form-group">
											<label>배너 URL</label>
											<input class="form-control" name="banner_url" placeholder="http://">
										</div>
										<div class="form-group">
											<label>Target Window</label>
											<div class="radio">
												<label>
												  <input type="radio" name="banner_target" value="_self" checked>
												 현재 창
												</label>
											</div>
											<div class="radio">
												<label>
												  <input type="radio" name="banner_target" value="_blank">
												 새로운 창
												</label>
											</div>
										</div>
										<div class="form-group">
											<label>사용여부</label>
											<div class="checkbox">
												<label>
													<input type="checkbox" name="hide_or_show" value="hide">사용하지 않습니다 (감춤)
												</label>
											</div>
										</div>
									</div><!-- /.box-body -->
								</div><!-- /. box -->
							</div><!-- /.col -->
							<div class="col-md-8">
								<div class="box box-info">
									<div class="box-body">
										<div class="form-group">
											<div class="btn btn-warning btn-file">
												<i class="fa fa-image"></i> 배너 이미지 *
												<input type="file" name="attachment[]">
											</div>
											<p class="help-block">xxx * xxxpx 크기의 jpg, gif, png 파일 포멧 권장. Max. 1MB</p>
											<ul id="preview-attachment" class="list-group"></ul>
										</div>
									</div><!-- /.box-body -->
									<div class="box-footer">
										<div class="pull-right">
											<button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> 등록합니다</button>
										</div>
									</div><!-- /.box-footer -->
								</div><!-- /. box -->
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
											<th>배너</th>
											<th>제목</th>
											<th>URL</th>
											<th>등록자</th>
											<th>등록기간</th>
											<th></th>
										</tr>
									</thead>
									<tbody id="sortable">
						<?php
						if ( !empty($banners) ) {
							foreach ( $banners as $key => $val ) {
								$banner_id = $val['ID'];
								$banner_title = $val['bnr_title'];
								$banner_link = $val['bnr_url'];
								$target = $val['bnr_target'];
								$show = $val['hide_or_show'];
								$user_id = $val['user_id'];
								$bnr_from = $val['bnr_from'];
								$bnr_to = $val['bnr_to'];
								
								$file_url = $val['bnr_file_url'];
								$file_name = $val['bnr_file_name'];
								
								$users = wps_get_user_by('ID', $user_id);
								$user_name = $users['user_name'];
	// 							$checked_1 = !strcmp($target, '_blank') ? 'checked' : '';
	// 							$checked_2 = !strcmp($show, 'show') ? 'checked' : '';
		
						?>
										<tr>
											<td><?php echo $key + 1 ?></td>
											<td>
												<img src="<?php echo $file_url ?>" style="width: 200px;" title="<?php echo $file_name ?>">
												<input type="hidden" name="banner_id[]" value="<?php echo $banner_id ?>">
											</td>
											<td>
												<?php echo $banner_title ?>
									<?php 
									if ( !strcmp($show, 'show') ) {
									?>
												<span class="label label-success" data-toggle="tooltip" data-placement="bottom" title="보임"><i class="fa fa-eye"></i></span>
									<?php 
									} else {
									?>
												<span class="label label-danger" data-toggle="tooltip" data-placement="bottom" title="감춤"><i class="fa fa-eye-slash"></i></span>
									<?php 
									}
									?>
											</td>
											<td>
												<?php echo $banner_link ?>
									<?php 
									if ( !strcmp($target, '_self') ) {
									?>
												<span class="label label-info" data-toggle="tooltip" data-placement="bottom" title="현재 창"><i class="fa fa-square-o"></i></span>
									<?php 
									} else {
									?>
												<span class="label label-warning" data-toggle="tooltip" data-placement="bottom" title="새로운 창"><i class="fa fa-clone"></i></span>
									<?php 
									}
									?>
											</td>
											<td>
												<?php echo $user_name ?>
											</td>
											<td>
												<?php echo $bnr_from ?> ~ <?php echo $bnr_to ?>
											</td>
											<td>
												<a href="javascript:;" class="btn btn-xs btn-primary btn-edit">편집</a>
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
			
			<div class="modal fade" id="banner-modal">
				<form id="item-edit-form">
					<input type="hidden" name="bkey" id="bkey">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title">배너 편집</h4>
							</div>
							<div class="modal-body">
								<div class="form-group">
									<label>배너 제목 *</label>
									<input class="form-control" name="banner_title" id="banner_title" placeholder="제목" required>
								</div>
								<div class="form-group">
									<label>배너 등록기간</label>
									<div class="input-group">
										<input type="text" id="banner_from2" name="banner_from" class="form-control datepicker actual_range" style="z-index: 100000;">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										<div class="input-group-addon">
											~
										</div>
										<input type="text" id="banner_to2" name="banner_to" class="form-control datepicker actual_range" style="z-index: 100001;">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label>배너 URL</label>
									<input type="text" class="form-control" name="banner_url" id="banner_url"  placeholder="http://">
								</div>
								<div class="form-group">
									<label>Target Window</label>
									<div class="radio">
										<label>
										  <input type="radio" name="banner_target" id="banner_target_s" value="_self" checked>
										 
										</label>
										현재 창 
									</div>
									<div class="radio">
										<label>
										  <input type="radio" name="banner_target" id="banner_target_b" value="_blank">
										 새로운 창
										</label>
									</div>
								</div>
								<div class="form-group">
									<label>사용여부</label>
									<div class="checkbox">
										<label>
											<input type="checkbox" name="hide_or_show" id="hide_or_show" value="hide">사용하지 않습니다 (감춤)
										</label>
									</div>
								</div>
								<div class="form-group">
									<label>배너 이미지</label>
									<input type="file" class="form-control" name="attachment" id="attachment">
								</div>
							</div>
							<div class="modal-footer">
								<button type="submit" class="btn btn-primary">저장합니다</button>
								<button type="button" class="btn btn-default" data-dismiss="modal">닫기</button>
							</div>
						</div><!-- /.modal-content -->
					</div><!-- /.modal-dialog -->
				</form>
			</div><!-- /.modal -->
			
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

					if ($('input[name="banner_title"]').val() == "") {
						alert("책 제목을 입력해 주십시오.");
						return false;
					}
					if ($("#preview-attachment li").length == 0) {
						alert("배너 이미지 파일을 선택해 주십시오.");
						return false;
					}
					
					showLoader();

					$.ajax({
						type : "POST",
						url : "./ajax/banner-new.php",
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

				// Banner 편집
				$(".btn-edit").click(function() {
					var id = $(this).parent().parent().find('input[name="banner_id[]"]').val();
					$("#bkey").val(id);

					$.ajax({
						type : "POST",
						url : "./ajax/banner-get.php",
						data : {
							"id" : id
						},
						dataType : "json",
						success : function(res) {
							if ( res.code == "0" ) {
								$("#banner_title").val( res.banner.bnr_title );
								$("#banner_from2").val( res.banner.bnr_from );
								$("#banner_to2").val( res.banner.bnr_to );
								$("#banner_url").val( res.banner.bnr_url );
								if ( res.banner.bnr_target == "_self" ) {
									$("#banner_target_s").prop("checked", true);
								} else {
									$("#banner_target_b").prop("checked", true);
								}
								if ( res.banner.hide_or_show == "hide" ) {
									$("#hide_or_show").prop("checked", true);
								}
							} else {
								alert( res.msg );
							}
						}
					});
					$("#banner-modal").modal("show");
				});
				
				// banner 편집 적용
				$("#item-edit-form").submit(function(e) {
					e.preventDefault();

					$(this).ajaxSubmit({
						type : "POST",
						url : "./ajax/banner-edit.php",
						data : $(this).serializeObject(),
						dataType : "json",
						success: function(xhr) {
							if ( xhr.code == "0" ) {
								location.reload();
							} else {
								alert( xhr.msg );
							}
						}
					});					
				});

				// modal form reset
				$('#banner-modal').on('hidden.bs.modal', function () {
					document.getElementById("item-edit-form").reset();
				});

				// Banner 삭제
				$(".btn-delete").click(function() {
					var id = $(this).parent().parent().find('input[name="banner_id[]"]').val();
					if ( !confirm("삭제하시겠습니까?") ) {
						return;
					}
					
					$.ajax({
						type : "POST",
						url : "./ajax/banner-delete.php",
						data : {
							"id" : id
						},
						dataType : "json",
						success : function(res) {
							if ( res.code == "0" ) {
// 								$("#item_table tbody tr").eq(id).fadeOut();
								location.reload();
							} else {
								alert( res.msg );
							}
						}
					});
				});

				// jquery ui calendar
				$( "#banner_from1" ).datepicker({
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
						$( "#banner_to1" ).datepicker( "option", "minDate", selectedDate );
					}
				}).inputmask('yyyy-mm-dd');
				
				$( "#banner_to1" ).datepicker({
					dateFormat : "yy-mm-dd",
					monthNames: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
					monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
					dayNamesMin: ['일','월','화','수','목','금','토'],
					defaultDate: "+1w",
					changeMonth: true,
					changeYear: true,
					showMonthAfterYear: true,
					onClose: function( selectedDate ) {
						$( "#banner_from1" ).datepicker( "option", "maxDate", selectedDate );
					}
				}).inputmask('yyyy-mm-dd');
				
				// jquery ui calendar
				$( "#banner_from2" ).datepicker({
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
						$( "#banner_to2" ).datepicker( "option", "minDate", selectedDate );
					}
				}).inputmask('yyyy-mm-dd');
				
				$( "#banner_to2" ).datepicker({
					dateFormat : "yy-mm-dd",
					monthNames: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
					monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
					dayNamesMin: ['일','월','화','수','목','금','토'],
					defaultDate: "+1w",
					changeMonth: true,
					changeYear: true,
					showMonthAfterYear: true,
					onClose: function( selectedDate ) {
						$( "#banner_from2" ).datepicker( "option", "maxDate", selectedDate );
					}
				}).inputmask('yyyy-mm-dd');

				$("#sortable").sortable({
			 		stop : function(event, ui) {
			 			reOderBanner();
			 		}
			 	});
				$("#sortable").disableSelection();

				$("#switch-all").click(function() {
					var chk = $(this).prop("checked");
					$(".banner_list").prop("checked", chk);
				});
				
			}); // $

			function reOderBanner() {
				$.ajax({
					type : "POST",
					url : "./ajax/banner-reorder.php",
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