<?php
/*
 * 2016.08.11		softsyw
 * Desc : 휴면회원에게 메일 발송
 */
require_once '../../../wps-config.php';

$code = 0;
$msg = '';

if ( !wps_is_admin() ) {
	$code = 510;
	$msg = '관리자만 사용할 수 있습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['to']) ) {
	$code = 410;
	$msg = '메일을 수신할 회원을 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['from']) ) {
	$code = 411;
	$msg = '보내는 사람 이메일을 입력해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['subject']) ) {
	$code = 414;
	$msg = '메일 제목을 입력해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['ckeditor_content']) ) {
	$code = 412;
	$msg = '메일 본문 내용을 입력해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$to = $_POST['to'];
$from = $_POST['from'];
$subject = $_POST['subject'];
$body = $_POST['ckeditor_content'];

$count = lps_send_mail_to_ids($to, $subject, $body, $from);

$json = compact('code', 'msg', 'count');
echo json_encode( $json );
?>