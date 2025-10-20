<?php
/*
 * 2015.12.28		softsyw
 * Desc : 로그아웃
 */
require_once '../wps-config.php';

unset( $_SESSION['login'] );

wps_redirect( ADMIN_URL . '/login.php' );

?>