<?php 
require_once '../../wps-config.php';

if ( empty($_GET['pt']) ) {
	lps_js_back('글의 종류에 대한 정보가 필요합니다.');
}
$post_type = $_GET['pt'];
$post_label = $wps_post_type[$post_type];

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
						<?php echo $post_label ?> 글쓰기
					</h1>
					<ol class="breadcrumb">
						<li><a href="<?php echo ADMIN_URL ?>/admin.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li><a href="<?php echo ADMIN_URL ?>/pages/">페이지 관리</a></li>
						<li><a href="<?php echo $referer ?>"><?php echo $post_label ?></a></li>
						<li class="active"><b>글쓰기</b></li>
					</ol>
				</div>

				<!-- Main content -->
				<div class="content body">
					<form id="item-new-form" method="post" enctype="multipart/form-data">
						<input type="hidden" name="post_type" value="<?php echo $post_type ?>">
					
						<div class="box box-primary">
							<div class="box-body">
								<div class="form-group">
									<label>
										<input type="checkbox" id="post_order" name="post_order" value="1"> 게시글을 상단에 노출합니다.
									</label>
								</div>
								<div class="form-group">
									<label>제목</label>
									<input type="text" id="post_title" name="post_title" class="form-control" placeholder="제목" required>
								</div>
								<div class="form-group">
									<label>등록범위</label>
									<div class="checkbox">
										<label class="text-muted"><input type="checkbox" id="switch-all">전체</label> &nbsp;
						<?php 
						foreach ($wps_notice_coverage as $key => $val ) {
						?>
										<label><input type="checkbox" class="post_type_area" name="post_type_area[]" value="<?php echo $key ?>"><?php echo $val ?></label> &nbsp;
						<?php 
						}
						?>
									</div>
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
					if ($(".post_type_area:checked").length == 0) {
						alert("등록범위를 선택해 주십시오.");
						return false;
					}
					if ( $.trim($("#post_content").val()) == "" ) {
						alert("내용을 입력해 주십시오.");
						return false;
					}

					showLoader();

					$("#item-new-form").ajaxSubmit({
						type : "POST",
						url : "./ajax/post-new.php",
// 						data : $(this).serialize(),
						data : $(this).serializeObject(),
						dataType : "json",
						success: function(xhr) {
							hideLoader();
							if ( xhr.code == "0" ) {
								location.href = "./";
							} else {
								alert( xhr.msg );
							}
						}
					});
				});

				$("#post_content").ckeditor({});

				$("#switch-all").click(function() {
					var chk = $(this).prop("checked");
					$(".post_type_area").prop("checked", chk);
				});
				
			}); //$
			</script>

<?php 
require_once ADMIN_PATH . '/admin-footer.php';
?>