<?php
/*
 * 2017.1.10		softsyw
 * Desc : 쿠폰 편집
 */
require_once '../../../wps-config.php';
require_once FUNC_PATH . '/functions-coupon.php';

$code = 0;
$msg = '';

if ( !wps_is_admin() ) {
	$code = 510;
	$msg = '관리자만 사용할 수 있습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['coupon_id']) ) {
	$code = 411;
	$msg = '쿠폰을 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}
// if ( empty($_POST['coupon_type']) ) {
// 	$code = 401;
// 	$msg = '쿠폰 종류를 선택해 주십시오.';
// 	$json = compact('code', 'msg');
// 	exit( json_encode($json) );
// }
if ( empty($_POST['coupon_name']) ) {
	$code = 402;
	$msg = '쿠폰 이름을 입력해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}
if ( empty($_POST['discount_type']) ) {
	$code = 403;
	$msg = '할인종류를 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$dt_type = $_POST['discount_type'];

if ( !strcmp($dt_type, 'amount') ) {
	if (empty($_POST['discount_amount'])) {
		$code = 405;
		$msg = '할인금액을 입력해 주십시오.';
		$json = compact('code', 'msg');
		exit( json_encode($json) );
	}
} else {
	if (empty($_POST['discount_rate'])) {
		$code = 406;
		$msg = '할인율을 입력해 주십시오.';
		$json = compact('code', 'msg');
		exit( json_encode($json) );
	}
}

$result = lps_edit_coupon();

if (empty($result)) {
	$code = 503;
	$msg = '책을 등록하지 못했습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$json = compact('code', 'msg', 'result');
echo json_encode( $json );

?>