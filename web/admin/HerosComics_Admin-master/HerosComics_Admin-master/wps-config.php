<?php
/*
 * 2016.07.26	softsyw
 * Desc : Main Configuration
 */
//define( 'DB_HOST', '114.31.116.199' );
define( 'DB_HOST', 'mariadb' );
define( 'DB_USER', 'booktalk' );
define( 'DB_PASS', 'booktalk' );
define( 'DB_NAME', 'heros_comics' );
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
define( 'ADMIN_URL', HOME_URL . '/admin' );
define( 'CONTENT_URL', HOME_URL . '/content' );
define( 'INC_URL', HOME_URL . '/includes' );

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
define( 'UPLOAD_PATH', ABS_PATH . '/upload' );
//define( 'UPLOAD_PATH',  '/upload' );


require_once ABS_PATH . '/wps-settings.php';

?>
