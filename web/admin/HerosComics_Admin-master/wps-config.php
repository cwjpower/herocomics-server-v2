<?php
session_start();
/*
 * 2016.07.26	softsyw
 * Desc : Main Configuration
 */
//define( 'DB_HOST', '114.31.116.199' );
define( 'DB_HOST', 'mariadb' );
define( 'DB_USER', 'root' );
define( 'DB_PASS', 'rootpass' );
define( 'DB_NAME', 'herocomics' );
define( 'DB_PORT', 3306);
define( 'DB_SOCK', '' );
define( 'DB_CHARSET', 'utf8' );

$protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
$site_url = $protocol . $_SERVER['HTTP_HOST'];

define( 'SITE_URL', $site_url );
// define( 'SETUP_DIR', '/service' );
define( 'SETUP_DIR', '/old_20180108' );
//define( 'HOME_URL', SITE_URL . SETUP_DIR );
define( 'HOME_URL', SITE_URL);

/*
 * URL
 */
define( 'ADMIN_URL', HOME_URL . '/admin/HerosComics_Admin-master/admin' );
define( 'CONTENT_URL', HOME_URL . '/content' );
define( 'INC_URL', HOME_URL . '/admin/HerosComics_Admin-master/includes' );

define( 'UPLOAD_URL', HOME_URL . '/upload' );
//define( 'UPLOAD_URL', '/upload' );



define( 'IMG_URL', INC_URL . '/images' );
define( 'MOBILE_URL', HOME_URL . '/mobile' );

/*
 * Path
 */
define( 'ABS_PATH', dirname(__FILE__) );
define( 'ADMIN_PATH', ABS_PATH . '/admin' );
define( 'CONTENT_PATH', ABS_PATH . '/content' );
define( 'INC_PATH', ABS_PATH . '/includes' );
define( 'FUNC_PATH', INC_PATH . '/functions' );
define( 'MOBILE_PATH', ABS_PATH . '/mobile' );
define( 'UPLOAD_PATH', dirname(dirname(dirname(__FILE__))) . '/upload' );
//define( 'UPLOAD_PATH',  '/upload' );


require_once ABS_PATH . '/wps-settings.php';

// 코믹스 브랜드 배열 정의
$wps_comics_brand = array(
    'MARVEL' => '마블 코믹스',
    'DC' => 'DC 코믹스',
    'IMAGE' => '이미지 코믹스',
    'DARK_HORSE' => '다크 호스',
    'IDW' => 'IDW',
    '' => '-'
);

// 책 상태 배열 정의
$wps_book_status = array(
    1 => '승인완료',
    1000 => '등록요청',
    2000 => '수정요청',
    2001 => '수정대기',
    2101 => '수정완료',  // ← 추가!
    3000 => '삭제요청',
    4000 => '삭제대기',
    4001 => '삭제완료',
    4101 => '삭제거부',
    9999 => '거부'
);

// 등록 형태 배열 정의
$wps_upload_type = array(
    'publisher' => '출판사 등록',
    'admin' => '관리자 등록',
    'agent' => '에이전트 등록'
);

$wps_comics_brand_css = array(
    1 => 'marvel',
    2 => 'dc',
    3 => 'image'
);


$wps_comics_brand_css = array(
    1 => 'marvel',
    2 => 'dc',
    3 => 'image'
);
