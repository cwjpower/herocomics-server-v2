<?php 
require_once '../../wps-config.php';

$user_id = empty($_GET['id']) ? wps_get_current_user_id() : $_GET['id'];

if ( empty($user_id) ) {
	lps_js_back( '사용자 아이디가 존재하지 않습니다.' );
}

$user_row = wps_get_user_by( 'ID', $user_id );

$user_login = $user_row['user_login'];
$user_name = $user_row['user_name'];
$user_pass = $user_row['user_pass'];
// $user_email = $user_row['user_email'];
$user_registered = $user_row['user_registered'];
$user_status = $user_row['user_status'];
$user_status_label = $wps_user_status[$user_status];
$user_level = $user_row['user_level'];
$user_level_label = $wps_user_level[$user_level];
$display_name = $user_row['display_name'];
$mobile = $user_row['mobile'];
$birthday = $user_row['birthday'];
$gender = $user_row['gender'];
$gender_label = $wps_user_gender[$gender];
$join_path = $user_row['join_path'];
$join_path_label = empty($join_path) ? '' : $wps_user_join_path[$join_path];
$last_login_dt = $user_row['last_login_dt'];
$residence = $user_row['residence'];
$residence_label = empty($residence) ? '' : $wps_user_residence_area[$residence];
$last_school = $user_row['last_school'];
$last_school_label = empty($last_school) ? '' : $wps_user_last_school[$last_school];

$user_meta = wps_get_user_meta( $user_id );

$um_user_level = $user_meta['wps_user_level'];
$um_user_level_label = $wps_user_level[$um_user_level];
$um_block_log = empty($user_meta['wps_user_block_log']) ? '' : unserialize($user_meta['wps_user_block_log']);
$um_block_reason = empty($um_block_log['reason']) ? '' : $um_block_log['reason'];

if ($user_status == 4) {
	lps_js_back( '탈퇴처리한 회원은 수정하실 수 없습니다.' );
}

require_once ADMIN_PATH . '/admin-header.php';

require_once './users-lnb.php';
?>

			<link rel="stylesheet" href="<?php echo INC_URL ?>/css/jquery-ui.css">
			<link rel="stylesheet" href="<?php echo INC_URL ?>/css/jquery.Jcrop.css">
			
			<!-- Content Wrapper. Contains page content -->
			<div class="content-wrapper">
				<!-- Content Header (Page header) -->
				<div class="content-header">
					<h1>
						회원 편집
					</h1>
					<ol class="breadcrumb">
						<li><a href="<?php echo ADMIN_URL ?>/admin.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li><a href="<?php echo ADMIN_URL ?>/users/">회원관리</a></li>
						<li><a href="<?php echo ADMIN_URL ?>/users/user_index.php?id=<?php echo $user_id ?>"><?php echo $user_name ?></a></li>
						<li class="active"><b>회원편집</b></li>
					</ol>
				</div>

				<!-- Main content -->
				<div class="content body">
					<form id="form-user-edit">
						<input type="hidden" name="user_id" id="user_id" value="<?php echo $user_id ?>">
						<div class="box box-info">
							<div class="box-header">
							</div>
							
							<div class="box-body">
								<h4>기본정보</h4>
								<table class="table table-bordered ls-table">
									<colgroup>
										<col style="width: 15%;">
										<col style="width: 35%;">
										<col style="width: 15%;">
										<col style="width: 35%;">
									</colgroup>
									<tbody>
										<tr>
											<td class="item-label">계정 *</td>
											<td>
												<input type="text" name="user_login" value="<?php echo $user_login ?>" class="form-control" maxlength="60">
											</td>
											<td class="item-label">이름 *</td>
											<td>
												<input type="text" name="user_name" value="<?php echo $user_name ?>" class="form-control" maxlength="20">
											</td>
										</tr>
										<tr>
											<td class="item-label">닉네임</td>
											<td>
												<input type="text" name="display_name" value="<?php echo $display_name ?>" class="form-control">
											</td>
											<td class="item-label">비밀번호</td>
											<td>
												<input type="password" name="user_pass" class="form-control" maxlength="20" placeholder="변경시에만 입력해 주십시오">
											</td>
										</tr>
										<tr>
											<td class="item-label">생년월일</td>
											<td>
												<input type="text" name="birthday" value="<?php echo $birthday ?>" class="form-control datepicker" maxlength="10">
											</td>
											<td class="item-label">성별</td>
											<td>
									<?php 
									foreach ($wps_user_gender as $key => $val) {
										if ( !empty($val) ) {
											$checked = $key == $gender ? 'checked' : '';
									?>
												<label><input type="radio" name="gender" value="<?php echo $key ?>" <?php echo $checked ?>><?php echo $val ?></label> &nbsp; &nbsp;
									<?php 
										}
									}
									?>
											</td>
										</tr>
									</tbody>
								</table>
								<h4>추가정보</h4>
								<table class="table table-bordered ls-table">
									<colgroup>
										<col style="width: 15%;">
										<col style="width: 35%;">
										<col style="width: 15%;">
										<col style="width: 35%;">
									</colgroup>
									<tbody>
										<tr>
											<td class="item-label">가입경로</td>
											<td>
									<?php 
									foreach ($wps_user_join_path as $key => $val) {
										if ( !empty($val) ) {
											$checked = $key == $join_path ? 'checked' : '';
									?>
												<label><input type="radio" name="join_path" value="<?php echo $key ?>" <?php echo $checked ?>><?php echo $val ?></label> &nbsp; &nbsp;
									<?php 
										}
									}
									?>
											</td>
											<td class="item-label">거주지</td>
											<td>
												<select name="residence" class="form-control">
													<option value="">-선택-</option>
									<?php 
									foreach ($wps_user_residence_area as $key => $val) {
										if ( !empty($val) ) {
											$selected = $key == $residence ? 'selected' : '';
									?>
													<option value="<?php echo $key ?>" <?php echo $selected ?>><?php echo $val ?></option>
									<?php 
										}
									}
									?>
												</select>
											</td>
										</tr>
										<tr>
											<td class="item-label">학력</td>
											<td>
												<select name="last_school" class="form-control">
													<option value="">-선택-</option>
									<?php 
									foreach ($wps_user_last_school as $key => $val) {
										if ( !empty($val) ) {
											$selected = $key == $last_school ? 'selected' : '';
									?>
													<option value="<?php echo $key ?>" <?php echo $selected ?>><?php echo $val ?></option>
									<?php 
										}
									}
									?>
												</select>
											</td>
											<td class="item-label">연락처</td>
											<td>
												<input type="text" id="mobile" name="mobile" value="<?php echo $mobile ?>" class="form-control">
											</td>
										</tr>
										<tr>
											<td class="item-label">가입일</td>
											<td><?php echo $user_registered ?></td>
											<td class="item-label">접속로그</td>
											<td><?php echo $last_login_dt ?></td>
										</tr>									
									</tbody>
								</table>
								<h4>기타정보</h4>
								<table class="table table-bordered ls-table">
									<colgroup>
										<col style="width: 15%;">
										<col style="width: 35%;">
										<col style="width: 15%;">
										<col style="width: 35%;">
									</colgroup>
									<tbody>
										<tr>
											<td class="item-label">회원상태</td>
											<td id="user-status-label">
												<select id="user_status" name="user_status" class="form-control">
									<?php 
									foreach ($wps_user_status as $key => $val) {
										if ( !empty($val) ) {
											$selected = $key == $user_status ? 'selected' : '';
									?>
													<option value="<?php echo $key ?>" <?php echo $selected ?>><?php echo $val ?></option>
									<?php 
										}
									}
									?>
												</select>
									<?php 
									if ($user_status == '1') {	// 차단
									?>
												<div><span class="label label-warning">차단사유</span><?php echo nl2br($um_block_reason) ?></div>
									<?php 
									}
									?>
												<textarea id="quit_reason" name="quit_reason" class="form-control hide"></textarea>
												
											</td>
											<td class="item-label">회원등급</td>
											<td>
												<select name="user_level" id="user_level" class="form-control">
									<?php 
									foreach ($wps_user_level as $key => $val) {
										if ( !empty($val) ) {
											$selected = $key == $user_level ? 'selected' : '';
									?>
													<option value="<?php echo $key ?>" <?php echo $selected ?>><?php echo $val ?></option>
									<?php 
										}
									}
									?>
												</select>
											</td>
										</tr>
									</tbody>
								</table>
								
								<h4>프로필 포토</h4>
								<div class="col-md-4">
									<div class="box">
										<div class="box-body">
											<img src="<?php echo lps_get_user_avatar($user_id) ?>" class="img-circle user-avatar" alt="User Image">
										</div>
										<div class="box-footer">
											<button type="button" id="new-crop-btn" class="btn bg-olive btn-flat margin">포토 편집</button>
											<button type="button" id="delete-crop-btn" class="btn bg-maroon btn-flat margin">포토 삭제</button>
										</div>
									</div>
								</div>
							</div>
							<div class="box-footer">
								<button type="submit" class="btn btn-primary">적용합니다</button>
								<button type="reset" class="btn btn-default">초기화</button> &nbsp;
							</div>
						</div><!-- /.box-body -->
					</form>
				</div><!-- /.content -->
			</div><!-- /.content-wrapper -->
			
			<div id="dialog-profile-photo" title="Edit Profile Photo">
				<form id="form-avatar">
					<div id="panel-avatar-img" class="text-center" style="padding-top: 100px;">
						<div class="btn bg-purple btn-flat margin btn-file">
							<i class="fa fa-image"></i> 사진을 선택해 주십시오
							<input type="file" name="avatar_img[]" class="form-control">
						</div>
					</div>
					<input type="hidden" name="user_id" value="<?php echo $user_id ?>">
					<input type="hidden" id="avatar_photo_path" name="avatar_photo_path">
					<input type="hidden" id="x" name="xc">
					<input type="hidden" id="y" name="yc">
					<input type="hidden" id="w" name="wc">
					<input type="hidden" id="h" name="hc">
				</form>
				<div id="wps-avatar"></div>
			</div>
			<!-- /.modal -->
			
			<div class="modal modal-danger" id="modal-user-level-change">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">×</span></button>
							<h4 class="modal-title">회원등급 변경 안내</h4>
						</div>
						<div class="modal-body">
							<h3>Master로 변경합니다.</h3>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Close</button>
						</div>
					</div>
					<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			
			<script src="<?php echo INC_URL ?>/js/jquery-ui.min.js"></script>
			<script src="<?php echo INC_URL ?>/js/ls-util.js"></script>
			<script src="<?php echo INC_URL ?>/js/jquery/jquery.oLoader.min.js"></script>
			<!-- InputMask -->
			<script src="<?php echo ADMIN_URL ?>/js/jquery/input-mask/jquery.inputmask.js"></script>
			<script src="<?php echo ADMIN_URL ?>/js/jquery/input-mask/jquery.inputmask.date.extensions.js"></script>
			<script src="<?php echo ADMIN_URL ?>/js/jquery/input-mask/jquery.inputmask.extensions.js"></script>
			<!-- Numeric hyphen -->
			<script src="<?php echo INC_URL ?>/js/jquery/jquery.numeric.hyphen.min.js"></script>
			<!-- Jcrop -->
			<script src="<?php echo INC_URL ?>/js/jquery/jquery.Jcrop.min.js"></script>
			<!-- Form(file) -->
			<script src="<?php echo INC_URL ?>/js/jquery/jquery.form.min.js"></script>
			
			<script>
			$(function() {

				// 포토편집 버튼 처리
				if ($(".user-avatar").attr("src").indexOf("default") > -1) {
					$("#delete-crop-btn").hide();
				} else {
					$("#new-crop-btn").hide();
				}
				
				$("#form-user-edit").submit(function(e) {
					e.preventDefault();

					if (!$("#quit_reason").hasClass("hide") && $("#quit_reason").val() == "") {
						alert("탈퇴사유를 입력해 주십시오.");
						$("#quit_reason").focus();
						return false;
					}
					
					showLoader();
					$.ajax({
						type : "POST",
						url : "./ajax/user-edit.php",
						data : $(this).serialize(),
						dataType : "json",
						success : function(res) {
							if (res.code == "0") {
								location.href = "./user_index.php?id=" + res.user_id;
							} else {
								hideLoader();
								alert(res.msg);
							}
						}
					});
				});

				// jquery ui calendar
				$(".datepicker").datepicker({
					dateFormat: 'yy-mm-dd',
					monthNames: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
					monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
					dayNamesMin: ['일','월','화','수','목','금','토'],
					changeMonth: true,
					changeYear: true,
					yearRange : "-100:+0",
					showMonthAfterYear: true
				}).inputmask('yyyy-mm-dd');

				$("#mobile").numeric_hyphen().blur(function(e) {
					$(this).val(phoneFormat($(this).val()));
				});

				$("#user_status").change(function() {
					console.log( $(this).val() );
					if ($(this).val() == "4") {
						$("#quit_reason").removeClass("hide");
					} else {
						$("#quit_reason").addClass("hide");
					}
				});

				$("#dialog-profile-photo").dialog({
					autoOpen: false
				});
				// 포토 등록
				$("#new-crop-btn").click(function() {
					$("#dialog-profile-photo").dialog({
						autoOpen: true,
						height: 600,
						width: 920,
						modal: true,
						buttons: {
							"적용합니다" : cropImage,
							"닫기": function() {
								$(this).dialog( "close" );
							}
						},
						close: function() {
							$("#wps-avatar").html("");
							$("#avatar_photo_path").val("");
							$("#panel-avatar-img").show();
						}
					});

					$(".ui-dialog-buttonset button:first").removeClass("ui-button ui-corner-all ui-widget").addClass("btn bg-orange btn-flat");
					
				});

				// 포토삭제
				$(document).on("click", "#delete-crop-btn", function() {
					$.ajax({
						type : "POST",
						url : "./ajax/delete-photo.php",
						data : {
							"user_id" : $("#user_id").val()
						},
						dataType : "json",
						success : function(res) {
							if (res.code == "0") {
								$(".user-avatar").attr("src", res.altimg);
								$("#delete-crop-btn").hide();
								$("#new-crop-btn").show();
							} else {
								alert(res.msg);
							}
						}
					});
				});

				// JCrop
				var jcrop_api, xsize, ysize, boundx, boundy;
				
				function initJcrop() {
					// Create variables (in this scope) to hold the API and image size
					var $preview = $('#preview-pane'),
						$pcnt = $('#preview-pane .preview-container');
					
					$('#cropbox').Jcrop({
						minSize: [30, 30],
						maxSize: [200, 200],
						setSelect: [10, 10, 200, 200],
						onChange: updatePreview,
						onSelect: updatePreview,
						aspectRatio: 1
					},function(){
						// Use the API to get the real image size
						var bounds = this.getBounds();

						boundx = bounds[0];
						boundy = bounds[1];

						xsize = $pcnt.width();
						ysize = $pcnt.height();
						
						// Store the API in the jcrop_api variable
						jcrop_api = this;
	
						// Move the preview into the jcrop container for css positioning
						$preview.appendTo(jcrop_api.ui.holder);
					});
				}
				    
				function updatePreview(c) {
					$pimg = $('#preview-pane .preview-container img');
					
					if ( parseInt(c.w) > 0 ) {
						var rx = xsize / c.w;
						var ry = ysize / c.h;

						$pimg.css({
							width: Math.round(rx * boundx) + 'px',
							height: Math.round(ry * boundy) + 'px',
							marginLeft: '-' + Math.round(rx * c.x) + 'px',
							marginTop: '-' + Math.round(ry * c.y) + 'px'
						});
					}

					$('#x').val(c.x);
				    $('#y').val(c.y);
				    $('#w').val(c.w);
				    $('#h').val(c.h);
				};

				// Crop 적용
				function cropImage() {
					var avatar = $("#avatar_photo_path").val();

					if (avatar == "") {
						alert("사진을 선택해 주십시오.");
						return;
					}
					
					$.ajax({
						type : "POST",
						url : "./ajax/crop-photo.php",
						data : $("#form-avatar").serialize(),
						dataType : "json",
						success : function(res) {
							if (res.code == "0") {
								if (res.result) {
									$("#delete-crop-btn").show();
									$("#new-crop-btn").hide();
									$(".user-avatar").attr("src", res.result);
									$("#wps-avatar").html("");
									$("#avatar_photo_path").val("");
									$("#panel-avatar-img").fadeIn();
									$("#dialog-profile-photo").dialog("close");
								}
							} else {
								alert(res.msg);
							}
						}
					});
				}

				// 사진 임시 등록
				$(document).on('change', 'input[name="avatar_img[]"]', function() {
					var size = this.files[0].size;
					var fext = this.files[0].name.split('.').pop().toLowerCase();
// 					var fname = this.files[0].name;

					if ( fext !=  'gif' && fext != 'jpg' && fext != 'jpeg' && fext != 'png' ) {
						alert('확장자가 gif, jpg, png인 이미지 파일만 첨부해 주십시오.');
						$(this).val("");
						return;
					}

					$("#form-avatar").ajaxSubmit({
						type : "POST",
						url : "<?php echo INC_URL ?>/lib/upload-attachment.php",
						data : {
							"eleName" : $(this).attr("name"),
							"doThumb" : 1,
							"twidth" : 600,
							"theight" : 0
						},
						dataType : "json",
						success: function(xhr) {
							if ( xhr.code == "0" ) {
								for ( var i = 0; i < xhr.file_url.length; i++ ) {
									uploadedFiles =  '<div>' +
														'<input type="hidden" name="tmp_file" value="' + xhr.file_path[i] + '">' +
														'<button type="button" class="btn bg-maroon btn-sm margin delete-tmp">사진 삭제</button>' +
														'<img id="cropbox" src="' + xhr.thumb_url[i] + '">' +
														'<div id="preview-pane">' +
															'<div class="preview-container">' +
																'<img src="' + xhr.thumb_url[i] + '" class="jcrop-preview">' +
															'</div>' +
														'</div>' +
													'</div>';
									$("#avatar_photo_path").val(xhr.thumb_path[i]);
								}
								
								$("#wps-avatar").html( uploadedFiles );
								$("#panel-avatar-img").fadeOut();

								initJcrop();
								
							} else {
								alert( xhr.msg );
							}
						}
					});
					$('input[name="avatar_img[]"]').val("");
				});

				// 임시 사진 삭제
				$(document).on("click", "#wps-avatar .delete-tmp", function() {
					var file = $(this).parent().find('input[name="tmp_file"]').val();

					$.ajax({
						type : "POST",
						url : "<?php echo INC_URL ?>/lib/delete-attachment.php",
						data : {
							"filePath" : file
						},
						dataType : "json",
						success : function(res) {
							$("#wps-avatar").html("");
							$("#avatar_photo_path").val("");
							$("#panel-avatar-img").fadeIn();
						}
					});
				});

				// 회원등급 변경 : Master 체크
				$("#user_level").change(function() {
					if ($(this).val() == "10") {
						$("#modal-user-level-change").modal("show");
					} 
				});
				
			}); //$
			</script>

<?php 
require_once ADMIN_PATH . '/admin-footer.php';
?>