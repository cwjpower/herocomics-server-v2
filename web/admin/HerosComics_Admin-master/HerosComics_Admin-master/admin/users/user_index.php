<?php 
require_once '../../wps-config.php';
require_once FUNC_PATH . '/functions-admin-logs.php';
require_once FUNC_PATH . '/functions-book.php';
require_once FUNC_PATH . '/functions-coupon.php';
require_once FUNC_PATH . '/functions-payment.php';

$user_id = empty($_GET['id']) ? wps_get_current_user_id() : $_GET['id'];

if ( empty($user_id) ) {
	lps_js_back( '사용자 아이디가 존재하지 않습니다.' );
}

$user_row = wps_get_user_by( 'ID', $user_id );

$user_login = $user_row['user_login'];
$user_name = $user_row['user_name'];
$user_pass = $user_row['user_pass'];
$user_email = $user_row['user_email'];
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

$um_user_point = empty($user_meta['lps_user_total_point']) ? 0 : $user_meta['lps_user_total_point'];
$um_user_cash = empty($user_meta['lps_user_total_cash']) ? 0 : $user_meta['lps_user_total_cash'];

$coupon_rows = lps_get_valid_coupons( $user_id );
$coupon_number = count($coupon_rows);

$user_total_payment = lps_get_total_user_payment( $user_id );

/*
 * Desc : 구매 내역
 */
$order_array = lps_get_user_order_by_page($user_id, 1, 5);
$order_rows = $order_array['order_rows'];
$order_total_count = $order_array['total_row']['total_count'];

/*
 * Desc : 문의 내역
 */
$qna_array = lps_get_user_qna_by_page($user_id, 1, 5);
$qna_rows = $qna_array['qna_rows'];
$qna_total_count = $qna_array['total_row']['total_count'];

/*
 * Desc : 메모 내역
 */
$memo_array = lps_get_memo_logs_by_page($user_id, 1, 5);
$memo_rows = $memo_array['memo_rows'];
$memo_total_count = $memo_array['total_row']['total_count'];

/*
 * Desc : 게시글
 */
$post_array = lps_get_user_post_by_page($user_id, 1, 5);
$post_rows = $post_array['post_rows'];
$post_total_count = $post_array['total_row']['total_count'];

/*
 * Desc : 읽을거에요
 */
$wish_array = lps_get_user_wish_by_page($user_id, 1, 5);
$wish_rows = $wish_array['wish_rows'];
$wish_total_count = $wish_array['total_row'];

require_once ADMIN_PATH . '/admin-header.php';

require_once './users-lnb.php';
?>

			<script src="<?php echo INC_URL ?>/js/ls-util.js"></script>
			
			<!-- Content Wrapper. Contains page content -->
			<div class="content-wrapper">
				<!-- Content Header (Page header) -->
				<div class="content-header">
					<h1>
						회원 상세정보
					</h1>
					<ol class="breadcrumb">
						<li><a href="<?php echo ADMIN_URL ?>/admin.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li><a href="<?php echo ADMIN_URL ?>/users/">회원관리</a></li>
						<li><a href="<?php echo ADMIN_URL ?>/users/">회원조회</a></li>
						<li class="active"><b><?php echo $user_name ?></b> 님</li>
					</ol>
				</div>

				<!-- Main content -->
				<div class="content body">
					<div class="box box-info">
						<input type="hidden" id="user-id" value="<?php echo $user_id ?>">
						<div class="box-header">
							<div class="pull-left">
					<?php 
					if ($user_level < 10 ) { 
					?>
								<button type="button" id="detail-profile" class="btn btn-info btn-flat">프로필 보기</button>
								<button type="button" id="detail-wishlist" class="btn btn-info btn-flat">보유책 보기</button>
					<?php 
					}
					?>
							</div>
							<div class="pull-right">
					<?php 
					if ($user_level < 10 ) {
					?>
								<button type="button" id="btn-block-user" class="btn bg-maroon btn-flat <?php echo $user_status == '0' ? '' : 'hide'; ?>">블랙리스트 추가</button>
								<button type="button" id="btn-unlock-user" class="btn bg-green btn-flat <?php echo $user_status == '1' ? '' : 'hide'; ?>">블랙리스트 해제</button>
								<button type="button" id="btn-new-memo" class="btn btn-info btn-flat">메모 추가</button>
					<?php 
					}
					?>
					
					<?php 
					if ($user_status != 4) {
					?>
								<a href="user_edit.php?id=<?php echo $user_id ?>" class="btn btn-info btn-flat">정보수정</a>
					<?php 
					}
					?>
							</div>
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
										<td class="item-label">계정(이메일)</td>
										<td><?php echo $user_login ?></td>
										<td class="item-label">이름</td>
										<td><?php echo $user_name ?></td>
									</tr>
									<tr>
										<td class="item-label">닉네임</td>
										<td><?php echo $display_name ?></td>
										<td class="item-label">비밀번호</td>
										<td><?php echo $user_pass ?></td>
									</tr>
									<tr>
										<td class="item-label">생년월일</td>
										<td><?php echo $birthday ?></td>
										<td class="item-label">성별</td>
										<td><?php echo $gender_label ?></td>
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
										<td><?php echo $join_path_label ?></td>
										<td class="item-label">거주지</td>
										<td><?php echo $residence_label ?></td>
									</tr>
									<tr>
										<td class="item-label">학력</td>
										<td><?php echo $last_school_label ?></td>
										<td class="item-label">연락처</td>
										<td><?php echo $mobile ?></td>
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
										<td class="item-label">계정연동</td>
										<td colspan="3"></td>
									</tr>
									<tr>
										<td class="item-label">회원상태</td>
										<td id="user-status-label">
											<?php echo $user_status_label ?>
											<div><?php echo nl2br($um_block_reason) ?></div>
										</td>
										<td class="item-label">회원등급</td>
										<td><?php echo $user_level_label ?></td>
									</tr>
									<tr>
										<td class="item-label">캐시</td>
										<td><?php echo number_format($um_user_cash) ?></td>
										<td class="item-label">적립금</td>
										<td><?php echo number_format($um_user_point) ?></td>
									</tr>									
									<tr>
										<td class="item-label">총 구매액</td>
										<td><?php echo number_format($user_total_payment) ?></td>
										<td class="item-label">보유 쿠폰수</td>
										<td><?php echo number_format($coupon_number) ?></td>
									</tr>									
								</tbody>
							</table>
						</div>
					</div><!-- /.box-body -->
					
					<div class="box box-primary">
						<div class="box-header">
							<div class="pull-left">
								<h4>최근 구매내역</h4>
							</div>
							<div class="pull-right">
								<button type="button" id="more-order-logs" class="btn btn-primary btn-sm">전체보기</button>
							</div>
						</div>
						<div class="box-body">
							<table id="list-order-logs" class="table table-striped table-hover">
								<thead>
									<tr>
										<th>#</th>
										<th>주문번호</th>
										<th>구매상품</th>
										<th>주문일시</th>
										<th>총 금액</th>
										<th>실결제금액</th>
										<th>상태</th>
									</tr>
								</thead>
								<tbody>
						<?php 
						if (!empty($order_rows)) {
							foreach ($order_rows as $key => $val) {
								$list_no = $order_total_count - $key;
								
								$order_id = $val['order_id'];
								$order_status = $val['order_status'];
								$book_title = $val['book_title'];
								$total_amount = $val['total_amount'];
								$cybercash_paid = $val['cybercash_paid'];
								$created_dt = $val['created_dt'];
								$updated_dt = $val['updated_dt'];
								$user_name = $val['user_name'];
								$count_order = $val['count_order'];
								if ($count_order > 1) {
									$book_title .= ' 외 ' . ($count_order - 1) . '권';
								}
						?>
									<tr>
										<td><?php echo $list_no ?></td>
										<td><?php echo $order_id ?></td>
										<td><?php echo $book_title ?></td>
										<td><?php echo substr($created_dt, 0, 10) ?></td>
										<td><?php echo number_format($total_amount) ?></td>
										<td><?php echo number_format($cybercash_paid) ?></td>
										<td><?php echo $wps_order_status[$order_status] ?></td>
									</tr>
						<?php 
							}
						}
						?>
								</tbody>
								<tfoot>
									<tr>
										<td colspan="7" class="box-footer text-center">
										</td>
									</tr>
								</tfoot>
							</table>
						</div>
					</div><!-- /.box -->
					
					<!-- 문의내역 -->
					<div class="box box-warning">
						<div class="box-header">
							<div class="pull-left">
								<h4>문의 내역</h4>
							</div>
							<div class="pull-right">
								<button type="button" id="more-qna-logs" class="btn btn-warning btn-sm">전체보기</button>
							</div>
						</div>
						<div class="box-body">
							<table id="list-qna-logs" class="table table-striped table-hover">
								<thead>
									<tr>
										<th>#</th>
										<th>등록일</th>
										<th>문의제목</th>
										<th>문의결과</th>
										<th>상담자</th>
										<th>상담날짜</th>
									</tr>
								</thead>
								<tbody>
						<?php 
						if (!empty($qna_rows)) {
							foreach ($qna_rows as $key => $val) {
								$list_no = $qna_total_count - $key;
								
								$post_title = htmlspecialchars($val['post_title']);
								$post_date = $val['post_date'];
								$post_date_label = substr($post_date, 0, 10);
								$post_status = $val['post_status'];
								$post_ans_user_id = $val['post_ans_user_id'];
								$post_ans_date = $val['post_ans_date'];
								
								$answer_row = wps_get_user($post_ans_user_id);
								$post_ans_user_name = @$answer_row['user_name'];
								
								if ( !strcmp($post_status, 'waiting') ) {
									$reply_icon = '대기중';
								} else {
									$reply_icon = '답변완료';
								}
						?>
									<tr>
										<td><?php echo $list_no ?></td>
										<td><?php echo $post_date_label ?></td>
										<td><?php echo $post_title ?></td>
										<td><?php echo $reply_icon ?></td>
										<td><?php echo $post_ans_user_name ?></td>
										<td><?php echo $post_ans_date ?></td>
									</tr>
						<?php 
							}
						}
						?>
								</tbody>
								<tfoot>
									<tr>
										<td colspan="6" class="box-footer text-center">
										</td>
									</tr>
								</tfoot>
							</table>
						</div>
					</div><!-- /.box -->
					
					<div class="box box-default ls-user-logs">
						<div class="box-header">
							<div class="pull-left">
								<h4>메모</h4>
							</div>
							<div class="pull-right">
								<button type="button" id="del-memo-logs" class="btn btn-danger btn-sm">삭제</button> &nbsp;
								<button type="button" id="more-memo-logs" class="btn btn-default btn-sm">전체보기</button>
							</div>
						</div>
						<div class="box-body">
							<table id="list-memo-logs" class="table table-striped table-hover">
								<colgroup>
									<col style="width: 5%;">
									<col style="width: 5%;">
									<col style="width: 10%;">
									<col style="width: 10%;">
									<col>
								</colgroup>
								<thead>
									<tr>
										<th><input type="checkbox" class="check-all" title="memo_id"></th>
										<th>#</th>
										<th>작성일</th>
										<th>작성자</th>
										<th>내용</th>
									</tr>
								</thead>
								<tbody>
						<?php 
						if (!empty($memo_rows)) {
							foreach ($memo_rows as $key => $val) {
								$memo_id = $val['ID'];
								$memo = $val['memo'];
								$created_by = $val['created_by'];
								$created_dt = $val['created_dt'];
								$list_no = $memo_total_count - $key;
						?>
									<tr>
										<td><input type="checkbox" name="memo_id[]" value="<?php echo $memo_id ?>"></td>
										<td><?php echo $list_no ?></td>
										<td><?php echo $created_dt ?></td>
										<td><?php echo $created_by ?></td>
										<td><?php echo nl2br($memo) ?></td>
									</tr>
						<?php 
							}
						}
						?>
								</tbody>
								<tfoot>
									<tr>
										<td colspan="5" class="box-footer text-center">
										</td>
									</tr>
								</tfoot>
							</table>
						</div>
					</div><!-- /.box -->
					
					<div class="box box-success">
						<div class="box-header">
							<div class="pull-left">
								<h4>게시글</h4>
							</div>
							<div class="pull-right">
								<button type="button" id="more-post-logs" class="btn btn-success btn-sm">전체보기</button>
							</div>
						</div>
						<div class="box-body">
							<table id="list-post-logs" class="table table-striped table-hover">
								<thead>
									<tr>
										<th>#</th>
										<th>게시판</th>
										<th>작성일</th>
										<th>제목</th>
										<th>추천수</th>
									</tr>
								</thead>
								<tbody>
						<?php 
						if (!empty($post_rows)) {
							foreach ($post_rows as $key => $val) {
								$list_no = $post_total_count - $key;
								$activity_id = $val['id']; 
								$book_title = $val['book_title'];
								$post_title = htmlspecialchars($val['subject']);
								$post_date = $val['created_dt'];
								$post_date_label = substr($post_date, 0, 10);
								$count_like = $val['count_like'];
						?>
									<tr>
										<td><?php echo $list_no ?></td>
										<td><a href="<?php echo ADMIN_URL ?>/community/activity_view.php?aid=<?php echo $activity_id ?>"><?php echo $book_title ?></a></td>
										<td><?php echo $post_date_label ?></td>
										<td><?php echo $post_title ?></td>
										<td><?php echo number_format($count_like) ?></td>
									</tr>
						<?php 
							}
						}
						?>
								</tbody>
								<tfoot>
									<tr>
										<td colspan="5" class="box-footer text-center">
										</td>
									</tr>
								</tfoot>
							</table>
						</div>
					</div><!-- /.box -->
					
					<!-- 읽을거에요 -->
					<div class="box box-danger">
						<div class="box-header">
							<div class="pull-left">
								<h4>찜(읽을 거에요) 목록</h4>
							</div>
							<div class="pull-right">
								<button type="button" id="more-wish-logs" class="btn btn-danger btn-sm">전체보기</button>
							</div>
						</div>
						<div class="box-body">
							<table id="list-wish-logs" class="table table-striped table-hover">
								<thead>
									<tr>
										<th>#</th>
										<th>제목</th>
										<th>저자</th>
										<th>출판사</th>
									</tr>
								</thead>
								<tbody>
						<?php 
						if (!empty($wish_rows)) {
							foreach ($wish_rows as $key => $val) {
								$list_no = $wish_total_count - $key;
								$book_rows = lps_get_book($val);
								$book_id = $book_rows['ID'];
								$book_title = $book_rows['book_title'];
// 								$cover_img = $book_rows['cover_img'];
								$author = $book_rows['author'];
								$publisher = $book_rows['publisher'];
						?>
									<tr>
										<td><?php echo $list_no ?></td>
										<td><?php echo $book_title ?></td>
										<td><?php echo $author ?></td>
										<td><?php echo $publisher ?></td>
									</tr>
						<?php 
							}
						}
						?>
								</tbody>
								<tfoot>
									<tr>
										<td colspan="5" class="box-footer text-center">
										</td>
									</tr>
								</tfoot>
							</table>
						</div>
					</div><!-- /.box -->
					
					<!-- 독서현황 
					<div class="box box-info">
						<div class="box-header">
							<div class="pull-left">
								<h4>독서현황</h4>
							</div>
							<div class="pull-right">
								<button type="button" class="btn btn-info btn-sm">전체보기</button>
							</div>
						</div>
						<div class="box-body">
							<table class="table table-striped table-hover">
								<thead>
									<tr>
										<th>#</th>
										<th>총 독서시간</th>
										<th>완독  한 책</th>
										<th>한번도 읽지 않을 책</th>
										<th>읽는 중인 책</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>1</td>
										<td>프랭크</td>
										<td>일반</td>
										<td>남</td>
										<td>관리자</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div><!-- /.box -->
					<!-- 
					<div class="box box-primary">
						<div class="box-header">
							<div class="pull-left">
								<h4>쿠폰 내역</h4>
							</div>
							<div class="pull-right">
								<button type="button" class="btn btn-primary btn-sm">전체보기</button>
							</div>
						</div>
						<div class="box-body">
							<table class="table table-striped table-hover">
								<thead>
									<tr>
										<th>#</th>
										<th>쿠폰번호</th>
										<th>쿠폰명</th>
										<th>발급일</th>
										<th>만료일</th>
										<th>상태</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>1</td>
										<td>프랭크</td>
										<td>일반</td>
										<td>일반</td>
										<td>남</td>
										<td>관리자</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div><!-- /.box -->
					
					<!-- 
					<div class="box box-warning">
						<div class="box-header">
							<div class="pull-left">
								<h4>SNS 내보내기 내역</h4>
							</div>
							<div class="pull-right">
								<button type="button" class="btn btn-warning btn-sm">전체보기</button>
							</div>
						</div>
						<div class="box-body">
							<table class="table table-striped table-hover">
								<thead>
									<tr>
										<th>#</th>
										<th>책 제목</th>
										<th>SNS 분류</th>
										<th>대여권 보상</th>
										<th>대여권 번호</th>
										<th>발급일</th>
										<th>만료일</th>
										<th>상태</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>1</td>
										<td>프랭크</td>
										<td>일반</td>
										<td>남</td>
										<td>관리자</td>
										<td>관리자</td>
										<td>남</td>
										<td>관리자</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div><!-- /.box -->

				</div><!-- /.content -->
			</div><!-- /.content-wrapper -->
			
			<div class="modal modal-default" id="modal-block-user">
				<form id="form-block-user">
					<input type="hidden" name="userID" value="<?php echo $user_id ?>">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">×</span></button>
								<h4 class="modal-title">블랙리스트 추가</h4>
							</div>
							<div class="modal-body">
								<div>
									<div class="pull-right">(by <?php echo wps_get_current_user_name() ?>)</div>
									<label>사유를 입력해 주십시오. - <?php echo $user_name ?>님</label>
									<textarea id="reason" name="reason" class="form-control" style="height: 100px;"></textarea>
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default pull-left" data-dismiss="modal">닫기</button>
								<button type="submit" class="btn btn-primary">적용합니다</button>
							</div>
						</div>
						<!-- /.modal-content -->
					</div>
					<!-- /.modal-dialog -->
				</form>
			</div>
			<!-- /.modal -->
			
			<div class="modal modal-default" id="modal-unlock-user">
				<form id="form-unlock-user">
					<input type="hidden" name="userID" value="<?php echo $user_id ?>">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">×</span></button>
								<h4 class="modal-title">블랙리스트 해제</h4>
							</div>
							<div class="modal-body">
								<div class="pull-right">(by <?php echo wps_get_current_user_name() ?>)</div>
								<h4><?php echo $user_name ?>님을 블랙리스트에서 해제합니다.</h4>
								<div class="alert alert-danger block_reason">
									<?php echo nl2br($um_block_reason) ?>
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default pull-left" data-dismiss="modal">닫기</button>
								<button type="submit" class="btn btn-primary">확인</button>
							</div>
						</div>
						<!-- /.modal-content -->
					</div>
					<!-- /.modal-dialog -->
				</form>
			</div>
			<!-- /.modal -->
			
			<div class="modal modal-default" id="modal-new-memo">
				<form id="form-new-memo">
					<input type="hidden" name="userID" value="<?php echo $user_id ?>">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">×</span></button>
								<h4 class="modal-title">메모</h4>
							</div>
							<div class="modal-body">
								<div>
									<div class="pull-right">(by <?php echo wps_get_current_user_name() ?>)</div>
									<label>내용을 입력해 주십시오. - <?php echo $user_name ?>님</label>
									<textarea id="memo" name="memo" class="form-control" style="height: 100px;"></textarea>
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default pull-left" data-dismiss="modal">닫기</button>
								<button type="submit" class="btn btn-primary">적용합니다</button>
							</div>
						</div>
						<!-- /.modal-content -->
					</div>
					<!-- /.modal-dialog -->
				</form>
			</div>
			<!-- /.modal -->

			<script>
			$(function() {
				// 블랙리스트 추가
				$("#btn-block-user").click(function() {
					$("#modal-block-user").modal("show");
				});

				// 블랙리스트 해제
				$("#btn-unlock-user").click(function() {
					// 블랙 등록 사유 가져오기
					$.ajax({
						type : "POST",
						url : "./ajax/get-user-meta.php",
						data : {
							"user_id" : $("#user-id").val(),
							"meta_key" : "wps_user_block_log"
						},
						dataType : "json",
						success : function(res) {
							$(".block_reason").html(res.user_meta.reason.replace(/\n/g, "<br>"));
						}
					});
					$("#modal-unlock-user").modal("show");
				});

				// 메모 추가
				$("#btn-new-memo").click(function() {
					$("#modal-new-memo").modal("show");
				});

				// 보유책 보기
				$("#detail-wishlist").click(function() {
					popupCenter("", "detailWin", 900, 600);
				});

				// 블랙리스트 등록
				$("#form-block-user").submit(function(e) {
					e.preventDefault();

					if ( $("#reason").val() == "" ) {
						alert("사유를 입력해 주십시오.");
						$("#reason").focus();
						return false;
					} 
					
					$.ajax({
						type : "POST",
						url : "./ajax/block-user.php",
						data : $(this).serialize(),
						dataType : "json",
						success : function(res) {
							if ( res.code == "0" ) {
								$("#reason").val("");
								$("#modal-block-user").modal("hide");
								$("#user-status-label").html('<span class="badge bg-maroon">차단</span><div>' + nl2br(res.reason) + '</div>');
								$("#btn-unlock-user").removeClass("hide");
								$("#btn-block-user").addClass("hide");
							} else {
								alert( res.msg );
							}
						}
					});
				});

				// 블랙리스트 해제
				$("#form-unlock-user").submit(function(e) {
					e.preventDefault();
					
					$.ajax({
						type : "POST",
						url : "./ajax/unlock-user.php",
						data : $(this).serialize(),
						dataType : "json",
						success : function(res) {
							if ( res.code == "0" ) {
								$("#modal-unlock-user").modal("hide");
								$("#user-status-label").html('<span class="badge bg-green">정상</span>');
								$("#btn-block-user").removeClass("hide");
								$("#btn-unlock-user").addClass("hide");
								$(".block_reason").html("");
							} else {
								alert( res.msg );
							}
						}
					});
				});

				// 코멘트, 메모 Modal 닫기 시  textarea 초기화
				$(".modal").on('hide.bs.modal', function () {
					$(".modal-body textarea").val("");
				});

				<!-- 구매내역 -->
				// 구매내역 전체보기
				$(document).on("click", "#more-order-logs", function() {
					$.ajax({
						type : "POST",
						url : "./ajax/get-more-user-order.php",
						data : {
							"user_id" : $("#user-id").val()
						},
						dataType : "json",
						success : function(res) {
							if ( res.code == "0" ) {
								$("#list-order-logs tbody").html( res.body );
								$("#list-order-logs tfoot td").html( res.foot );
								$("#more-order-logs").text("전체보기 닫기");
								$("#more-order-logs").attr("id", "close-order-logs");
							} else {
								alert( res.msg );
							}
						}
					});
				});

				// 구매내역 전체보기 닫기
				$(document).on("click", "#close-order-logs", function() {
					$("#list-order-logs tbody tr:gt(4)").remove();
					$("#list-order-logs tfoot td").html("");

					$("#close-order-logs").text("전체보기");
					$("#close-order-logs").attr("id", "more-order-logs");
				});

				// 구매내역 Pagination Link
				$(document).on("click", "ul.order_log li a", function(e) {
// 					e.preventDefault();

					$(".order_log li").removeClass("active");
					$(this).closest("li").addClass("active");
					var page = $(this).attr("title");

					$.ajax({
						type : "POST",
						url : "./ajax/get-more-user-order.php",
						data : {
							"user_id" : $("#user-id").val(),
							"page" : page 
						},
						dataType : "json",
						success : function(res) {
							if ( res.code == "0" ) {
								$("#list-order-logs tbody").html( res.body );
								$("#list-order-logs tfoot td").html( res.foot );
							} else {
								alert( res.msg );
							}
						}
					});
				});
				
				<!-- 문의내역 -->
				// 문의내역 전체보기
				$(document).on("click", "#more-qna-logs", function() {
					$.ajax({
						type : "POST",
						url : "./ajax/get-more-user-qna.php",
						data : {
							"user_id" : $("#user-id").val()
						},
						dataType : "json",
						success : function(res) {
							if ( res.code == "0" ) {
								$("#list-qna-logs tbody").html( res.body );
								$("#list-qna-logs tfoot td").html( res.foot );
								$("#more-qna-logs").text("전체보기 닫기");
								$("#more-qna-logs").attr("id", "close-qna-logs");
							} else {
								alert( res.msg );
							}
						}
					});
				});

				// 문의내역 전체보기 닫기
				$(document).on("click", "#close-qna-logs", function() {
					$("#list-qna-logs tbody tr:gt(4)").remove();
					$("#list-qna-logs tfoot td").html("");

					$("#close-qna-logs").text("전체보기");
					$("#close-qna-logs").attr("id", "more-qna-logs");
				});

				// 문의내역 Pagination Link
				$(document).on("click", "ul.qna_log li a", function(e) {
// 					e.preventDefault();

					$(".qna_log li").removeClass("active");
					$(this).closest("li").addClass("active");
					var page = $(this).attr("title");

					$.ajax({
						type : "POST",
						url : "./ajax/get-more-user-qna.php",
						data : {
							"user_id" : $("#user-id").val(),
							"page" : page 
						},
						dataType : "json",
						success : function(res) {
							if ( res.code == "0" ) {
								$("#list-qna-logs tbody").html( res.body );
								$("#list-qna-logs tfoot td").html( res.foot );
							} else {
								alert( res.msg );
							}
						}
					});
				});

				<!-- 메모 -->
				// 메모 등록
				$("#form-new-memo").submit(function(e) {
					e.preventDefault();
					
					$.ajax({
						type : "POST",
						url : "./ajax/add-memo-logs.php",
						data : $(this).serialize(),
						dataType : "json",
						success : function(res) {
							if ( res.code == "0" ) {
								if ( res.ID > 0 ) {
// 									$("#memo").val("");
// 									$("#modal-new-memo").modal("hide");
									location.reload();
								} else {
									alert("등록하지 못했습니다.");
								}
							} else {
								alert( res.msg );
							}
						}
					});
				});

				// 메모 전체보기
				$(document).on("click", "#more-memo-logs", function() {
					$.ajax({
						type : "POST",
						url : "./ajax/get-more-memo-logs.php",
						data : {
							"user_id" : $("#user-id").val()
						},
						dataType : "json",
						success : function(res) {
							if ( res.code == "0" ) {
								$("#list-memo-logs tbody").html( res.body );
								$("#list-memo-logs tfoot td").html( res.foot );
								$("#more-memo-logs").text("전체보기 닫기");
								$("#more-memo-logs").attr("id", "close-memo-logs");
							} else {
								alert( res.msg );
							}
						}
					});
				});

				// 메모 전체보기 닫기
				$(document).on("click", "#close-memo-logs", function() {
					$("#list-memo-logs tbody tr:gt(4)").remove();
					$("#list-memo-logs tfoot td").html("");

					$("#close-memo-logs").text("전체보기");
					$("#close-memo-logs").attr("id", "more-memo-logs");
				});

				// 메모 Pagination Link
				$(document).on("click", "ul.memo_log li a", function(e) {
// 					e.preventDefault();

					$(".memo_log li").removeClass("active");
					$(this).closest("li").addClass("active");
					var page = $(this).attr("title");

					$.ajax({
						type : "POST",
						url : "./ajax/get-more-memo-logs.php",
						data : {
							"user_id" : $("#user-id").val(),
							"page" : page 
						},
						dataType : "json",
						success : function(res) {
							if ( res.code == "0" ) {
								$("#list-memo-logs tbody").html( res.body );
								$("#list-memo-logs tfoot td").html( res.foot );
							} else {
								alert( res.msg );
							}
						}
					});
				});

				<!-- 게시글내역 -->
				// 게시글내역 전체보기
				$(document).on("click", "#more-post-logs", function() {
					$.ajax({
						type : "POST",
						url : "./ajax/get-more-user-post.php",
						data : {
							"user_id" : $("#user-id").val()
						},
						dataType : "json",
						success : function(res) {
							if ( res.code == "0" ) {
								$("#list-post-logs tbody").html( res.body );
								$("#list-post-logs tfoot td").html( res.foot );
								$("#more-post-logs").text("전체보기 닫기");
								$("#more-post-logs").attr("id", "close-wish-logs");
							} else {
								alert( res.msg );
							}
						}
					});
				});

				// 게시글내역 전체보기 닫기
				$(document).on("click", "#close-wish-logs", function() {
					$("#list-post-logs tbody tr:gt(4)").remove();
					$("#list-post-logs tfoot td").html("");

					$("#close-wish-logs").text("전체보기");
					$("#close-wish-logs").attr("id", "more-post-logs");
				});

				// 게시글내역 Pagination Link
				$(document).on("click", "ul.post_log li a", function(e) {
// 					e.preventDefault();

					$(".post_log li").removeClass("active");
					$(this).closest("li").addClass("active");
					var page = $(this).attr("title");

					$.ajax({
						type : "POST",
						url : "./ajax/get-more-user-post.php",
						data : {
							"user_id" : $("#user-id").val(),
							"page" : page 
						},
						dataType : "json",
						success : function(res) {
							if ( res.code == "0" ) {
								$("#list-post-logs tbody").html( res.body );
								$("#list-post-logs tfoot td").html( res.foot );
							} else {
								alert( res.msg );
							}
						}
					});
				});

				<!-- 찜목록내역 -->
				// 찜목록내역 전체보기
				$(document).on("click", "#more-wish-logs", function() {
					$.ajax({
						type : "POST",
						url : "./ajax/get-more-user-wish.php",
						data : {
							"user_id" : $("#user-id").val()
						},
						dataType : "json",
						success : function(res) {
							if ( res.code == "0" ) {
								$("#list-wish-logs tbody").html( res.body );
								$("#list-wish-logs tfoot td").html( res.foot );
								$("#more-wish-logs").text("전체보기 닫기");
								$("#more-wish-logs").attr("id", "close-post-logs");
							} else {
								alert( res.msg );
							}
						}
					});
				});

				// 찜목록내역 전체보기 닫기
				$(document).on("click", "#close-post-logs", function() {
					$("#list-wish-logs tbody tr:gt(4)").remove();
					$("#list-wish-logs tfoot td").html("");

					$("#close-post-logs").text("전체보기");
					$("#close-post-logs").attr("id", "more-wish-logs");
				});

				// 찜목록내역 Pagination Link
				$(document).on("click", "ul.post_log li a", function(e) {
// 					e.preventDefault();

					$(".post_log li").removeClass("active");
					$(this).closest("li").addClass("active");
					var page = $(this).attr("title");

					$.ajax({
						type : "POST",
						url : "./ajax/get-more-user-wish.php",
						data : {
							"user_id" : $("#user-id").val(),
							"page" : page 
						},
						dataType : "json",
						success : function(res) {
							if ( res.code == "0" ) {
								$("#list-wish-logs tbody").html( res.body );
								$("#list-wish-logs tfoot td").html( res.foot );
							} else {
								alert( res.msg );
							}
						}
					});
				});


				

				// Check All memo logs
				$(document).on("click", ".check-all", function() {
					$('input[name="memo_id[]"]').prop("checked", $(this).prop("checked"));
				});

				// Delete memo logs
				$(document).on("click", "#del-memo-logs", function() {
					var vals = [];
					$('input[name="memo_id[]"]:checked').each(function() {
						vals.push( $(this).val() );
					});
					if ( vals.length == 0 ) {
						alert("삭제할 메모를 선택해 주십시오.");
						return;
					}
					$.ajax({
						type : "POST",
						url : "./ajax/delete-memo-logs.php",
						data : {
							"user_id" : $("#user-id").val(),
							"memo_id" : vals 
						},
						dataType : "json",
						success : function(res) {
							if ( res.code == "0" ) {
								// remove
								if ( res.affected_rows > 0 ) {
									$('input[name="memo_id[]"]:checked').closest("tr").fadeOut();
								} else {
									alert("메모를 삭제할 수 없습니다.");
								}
							} else {
								alert( res.msg );
							}
						}
					});
				});
				<!-- /.메모 -->

				
			}); //$
			</script>

<?php 
require_once ADMIN_PATH . '/admin-footer.php';
?>