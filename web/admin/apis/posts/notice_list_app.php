<?php
/*
 * Desc : API 북톡 공지사항 for App
 * 	method : GET
 */
require_once '../../wps-config.php';

$code = 0;
$msg = '';

// community notice
$LIST = lps_get_app_notice();

$json = compact( 'code', 'msg', 'LIST' );
echo json_encode( $json );

?>