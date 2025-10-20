<?php
/*
 * 2016.07.27		softsyw
 * Desc : 관리자
 */
require_once '../wps-config.php';

// 등록된 관리자가 없으면 관리자 등록 화면으로 보냅니다.
if ( !wps_exist_admin() ) {
//	wps_redirect( ADMIN_URL . '/register.php' );
} else {
	wps_redirect( ADMIN_URL . '/login.php' );
}

if ( wps_is_admin() ) {
//	wps_redirect( ADMIN_URL . '/admin.php' );
}

?>
