<?php
/*
 * Desc : 코드 및 공통변수
 */
ini_set('display_errors', '1');

// 업로드할 때 이미지파일은 확장자를 유지합니다.
define ( 'WPS_IMAGE_EXT', serialize( array( 'jpg', 'jpeg', 'gif', 'png' ) ) );

define( 'SITE_FROM_EMAIL', 'softsyw@gmail.com' );	// 발신전용 이메일

// 북톡출판사 ID
define( 'BOOKTALK_PUBLISHER_ID', 7);

/*
 * Thumbnail
 */
define( 'THUMB_WIDTH', 168 );
define( 'THUMB_HEIGHT', 100 );

define( 'DEFAULT_LMS_RATE', 3 );
define( 'DEFAULT_MMS_RATE', 15 );

define( 'SENDER_NAME', '북톡' );
define( 'SENDER_EMAIL', 'admin@softsyw.com' );

// facebook APP ID
$wps_fb_app_id = '119408608529355';

/*
 * Desc : 회원 상태
 */
$wps_user_status = array(
	'0'		=> '<span class="badge bg-green">정상</span>',
	'1'		=> '<span class="badge bg-maroon">차단</span>',
	'8'		=> '<span class="badge bg-purple">휴면</span>',
	'4'		=> '<span class="badge bg-danger fa fa-times-circle">탈퇴</span>'
);
$wps_user_state = array(
	'0'		=> '<span class="badge bg-green">신규</span>',
	'1'		=> '<span class="badge bg-maroon">차단</span>',
	'4'		=> '<span class="badge bg-danger fa fa-times-circle">탈퇴</span>'
);
/*
 * Desc : 회원 등급
*/
$wps_user_level = array(
	'1'		=> '<span class="badge bg-aqua">독자</span>',
	'3'		=> '<span class="badge bg-light-blue">일반 작가</span>',
	'6'		=> '<span class="badge bg-yellow">1인 작가</span>',
	'7'		=> '<span class="badge bg-red">출판사</span>',
	'10'	=> '<span class="badge bg-default">MASTER</span>'
);
/*
 * Desc : 성별
 */
$wps_user_gender = array(
	''	=> '',
	'1'	=> '남자',
	'2'	=> '여자'
);
/*
 * Desc : 가입경로
 */
$wps_user_join_path = array(
	'mobile'	=> 'Web',
	'app'		=> 'App',
	'cms'		=> 'CMS'
);
/*
 * Desc : 학력
 */
$wps_user_last_school = array(
	'10'	=> '미취학',
	'11'	=> '초등학교 중퇴',
	'12'	=> '초등학교 졸업',
	'21'	=> '중학교 중퇴',
	'22'	=> '중학교 졸업',
	'31'	=> '고등학교 중퇴',
	'32'	=> '고등학교 졸업',
	'41'	=> '전문대학 중퇴',
	'42'	=> '전문대학 졸업(전문학사)',
	'43'	=> '대학 중퇴',
	'44'	=> '대학 졸업(학사)',
	'51'	=> '석사',
	'52'	=> '박사'
);
$wps_user_residence_area = array(
	'10' => '서울특별시',
	'11' => '부산광역시',
	'12' => '대구광역시',
	'13' => '인천광역시',
	'14' => '광주광역시',
	'15' => '대전광역시',
	'16' => '울산광역시',
	'17' => '세종특별자치시',
	'18' => '경기도',
	'19' => '경기남부',
	'20' => '경기북부',
	'21' => '강원도',
	'22' => '충청북도',
	'23' => '충청남도',
	'24' => '전라북도',
	'25' => '전라남도',
	'26' => '경상북도',
	'27' => '경상남도',
	'28' => '제주특별자치도'
);

$wps_pay_status = array(
	'waiting'		=> '입금대기',
	'processing'	=> '결제진행중',
	'complete'		=> '결제완료',
	'split'			=> '분할결제'
);

// 게시판 종류 설정
$wps_board_type = array(
	'list'		=> '리스트형-표준',
	'webzine'	=> '웹진형-표준',
	'grid'		=> '앨범형-표준'
);

/*
 * Desc : Post type
 * 		qna_new : 첨부파일
 */
$wps_post_type = array(
	'post_new'		=> '글',
	'notice_new'	=> '공지사항',
	'page_new'		=> '페이지',
	'faq_new'		=> '자주묻는질문(FAQ)',
	'qna_new'		=> 'Q&A'
);

// 결제방법
$wps_charge_method = array(
	'credit_card'	=> '신용카드',
	'bank_transfer'	=> '무통장입금',
	'mobile'		=> '휴대폰결제'
);
// 관리자 결제방법
$wps_charge_method_admin = array(
	'admin_charge'	=> '관리자 승인'
);
// 결제진행 상태
$wps_payment_state = array(
	'ready'			=> '<span class="label label-default">진행중</span>',
	'bt_ready'		=> '<span class="label label-default">입금 대기중</span>',
	'done'			=> '<span class="label label-success">결제완료</span>'
);
// 결제방법
$wps_payment_method = array(
		'pm_mobile'				=> '핸드폰',
		'pm_card'				=> '신용카드',
		'pm_ars'				=> 'ARS',
		'pm_bank_transfer'		=> '계좌이체',
		'pm_virtual_transfer'	=> '가상계좌'
);
// 결제금액 - 적립률
$wps_payment_amount_rate = array(
		1000	=> 0.01,
		3000	=> 0.03,
		5000	=> 0.05,
		10000	=> 0.05,
		20000	=> 0.05,
		30000	=> 0.07,
		50000	=> 0.07,
		70000	=> 0.07,
		100000	=> 0.09,
		200000	=> 0.09,
		300000	=> 0.09
);

// 책 상태
$wps_book_status = array(
	'1000'	=> '<span class="label label-default">등록 대기중</span>',
	'2001'	=> '<span class="label label-warning">수정 요청</span>',
	'2101'	=> '<span class="label bg-maroon">삭제 요청</span>',
	'3000'	=> '<span class="label label-success">승인완료</span>',
	'4000'	=> '<span class="label bg-purple">등록거절</span>',
	'4001'	=> '<span class="label bg-navy">수정거절</span>',
	'4101'	=> '<span class="label label-danger">삭제거절</span>',
	'8080'	=> '<span class="label label-danger">삭제완료</span>'
);
// 등록형태
$wps_upload_type = array(
	''	=> '',
	'subscription'	=> '입점형',
	'charge'		=> '수수료'
);

// Page
$wps_notice_coverage = array(
		'app'	=> 'App',
		'web'	=> 'Web',
		'post'	=> '모든 담벼락',
		'chat'	=> '모든 채팅'
);

// curation
$wps_curation_status = array(
	'1001'	=> '<span class="label label-default">대기중</span>',
	'3000'	=> '<span class="label label-success">메인에 노출</span>'
);

$wps_best_category = array(
	'1000'	=> '커뮤니케이션',
	'2000'	=> '기간별(일별)',
	'2010'	=> '기간별(주별)',
	'2020'	=> '기간별(월별)',
	'3000'	=> '장르별',
	'4000'	=> '스테디셀러'
);

$wps_order_status = array(
	'1'	=> '장바구니 취소',
	'2' => '바로구매 취소',
	'3' => '캐쉬 충전 준비',
	'9'	=> '완료'
);

// 기간별
$wps_period_coverage = array(
	'day'		=> '일별',	
	'week'		=> '주별',	
	'month'		=> '월별',	
	'time'		=> '시간별',	
	'manual'	=> '기간 설정'
);

$wps_refund_status = array(
	'2'	=> '환불전',
	'9'	=> '환불완료',
	'4'	=> '환불보류'
);

$wps_comics_brand = array(
	'1'	=> 'MARVEL',
	'2'	=> 'DC',
	'3'	=> 'IMAGE'
);

$wps_comics_brand_css = array(
	'1'	=> 'marvel',
	'2'	=> 'dc',
	'3'	=> 'image'
);


// todo: 고객센터 공지사항 분류
// todo : 1:1 문의 분류
// todo : faq 분류




?>