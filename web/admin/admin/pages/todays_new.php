<?php
/*
 * 2016.10.11	softsyw
 * Desc : Web Main에 노출시킬 책을 지정. 
 * 		Default : 오늘의 신간 없으면 이전 하루의 신간
 * 		관리자가 등록한 책 우선  
 * 
 */
require_once '../../wps-config.php';
require_once FUNC_PATH . '/functions-page.php';
require_once FUNC_PATH . '/functions-book.php';

$book_ids = lps_get_approved_books();	// 검색용

$todays_new_book = wps_get_option( 'lps_todays_new_book' );		// 관리자 등록 책

$is_admin_upload = 0;

if (!empty($todays_new_book)) {
	$unserial = unserialize($todays_new_book);
	
	if (empty($unserial)) {		// 관리자 등록 책 없을 경우 Default 책
		$todays_new = lps_get_todays_new();
	} else {
		$todays_new = $unserial;
		$is_admin_upload = 1;
	}
	
} else {
	$todays_new = lps_get_todays_new();
}

$disabled = $is_admin_upload ? '' : 'disabled';		// 삭제용 checkbox

require_once ADMIN_PATH . '/admin-header.php';

require_once './pages-lnb.php';
?>

			<link rel="stylesheet" href="<?php echo INC_URL ?>/css/jquery-ui.css">

			<!-- Content Wrapper. Contains page content -->
			<div class="content-wrapper">
				<!-- Content Header (Page header) -->
				<div class="content-header">
					<h1>
						오늘의 신간
						<small><button type="button" id="btn-new-item" class="btn btn-info">등록</button></small>
					</h1>
					<ol class="breadcrumb">
						<li><a href="<?php echo ADMIN_URL ?>/admin.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li><a href="<?php echo ADMIN_URL ?>/pages/">페이지 관리</a></li>
						<li class="active"><b>오늘의 신간</b></li>
					</ol>
				</div>

				<!-- Main content -->
				<div class="content body">
					<form id="item-new-form" class="hide">
						<div class="row">
							<div class="col col-md-8">
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
										<select id="book-lists" name="today_books[]" class="form-control" multiple style="height: 200px;">
							<?php 
							if ( !empty($book_ids) ) {
								foreach ( $book_ids as $key => $val ) {
									$ID = $val['ID'];
									$author = $val['author'];
									$book_title = $val['book_title'];
// 									$book_status = $wps_book_status[$val['book_status']];
							?>
											<option value="<?php echo $ID ?>"><?php echo $book_title ?> (<?php echo $author ?>)</option>
							<?php 
								}
							}
							?>
										</select>
									</div>
									<div class="box-footer">
										<button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> 추가합니다</button>
									</div>
									
								</div><!-- /.box -->
							</div>
						</div><!-- /.row -->
					</form>
					
					<div class="box box-primary">
						<div class="box-header">
							<button type="button" id="btn-remove-todays" class="btn btn-danger btn-sm">삭제</button>
						</div>
						<div class="box-body">
							<form id="item-list-form">
								<table class="table table-striped table-hover">
									<thead>
										<tr>
											<th><input type="checkbox" id="switch-all" <?php echo $disabled ?>></th>
											<th>No.</th>
											<th>책 제목</th>
											<th>저자</th>
											<th>출판사</th>
											<th>등록일</th>
											<th>표지보기</th>
											<th>상태</th>
										</tr>
									</thead>
									<tbody id="sortable">
						<?php
						if ( !empty($todays_new) ) {
							foreach ( $todays_new as $key => $val ) {
								
								$book_rows = lps_get_book($val);
								
								$book_id = $book_rows['ID'];
								$publisher = $book_rows['publisher'];
								$book_title = $book_rows['book_title'];
								$author = $book_rows['author'];
								$cover_img = $book_rows['cover_img'];
								$created_dt = substr($book_rows['created_dt'], 0, 10);
								
						?>
										<tr>
											<td><input type="checkbox" class="book_id" name="book_id[]" value="<?php echo $book_id ?>" <?php echo $disabled ?>></td>
											<td><?php echo $key + 1 ?></td>
											<td><?php echo $book_title ?></td>
											<td><?php echo $author ?></td>
											<td><?php echo $publisher ?></td>
											<td><?php echo $created_dt ?></td>
											<td>
												<img src="<?php echo $cover_img ?>" style="width: 100px;" title="<?php echo $file_name ?>">
											</td>
											<td>
												게시중
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
					
					showLoader();

					$.ajax({
						type : "POST",
						url : "./ajax/todays-new.php",
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
					$(".book_id").prop("checked", chk);
				});

				// 책 삭제
				$("#btn-remove-todays").click(function() {
					var chkLength = $(".book_id:checked").length;

					if (chkLength == 0) {
						alert("책을 선택해 주십시오.");
						return;
					}

					showLoader();
					
					$.ajax({
						type : "POST",
						url : "./ajax/todays-new-delete.php",
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
				
				$("#sortable").sortable({
			 		stop : function(event, ui) {
			 			reOderBanner();
			 		}
			 	});
				$("#sortable").disableSelection();

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
						url : "../books/ajax/search-approved-book.php",
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

			function reOderBanner() {
				$(".book_id").prop("checked", true);	// checkbox checked
				
				$.ajax({
					type : "POST",
					url : "./ajax/todays-new-reorder.php",
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