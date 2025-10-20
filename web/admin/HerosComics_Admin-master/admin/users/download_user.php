<?php
require_once '../../wps-config.php';

if ( !strcmp(PHP_SAPI, 'cli' ) ) {
	exit('This example should only be run from a Web Browser');
}

if ( !wps_is_admin() ) {
	wps_redirect( ADMIN_URL . '/login.php' );
}

/** Include PHPExcel */
require_once INC_PATH . '/classes/PHPExcel.php';


$filename = '회원 내역.xlsx';
$filename = rawurlencode($filename);

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("Softsyw")
							 ->setLastModifiedBy("Softsyw")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Lampsoft");


$active_sheet = $objPHPExcel->setActiveSheetIndex(0);

/*
 * ID,
			user_name,
			mobile,
			display_name,
			birthday,
			user_registered,
			last_login_dt
			
 */
$column_array = array(
		'아이디',	'이름', '휴대전화번호', '닉네임', '생년월일', '가입날짜', '최종 로그인 날짜'
);
$active_sheet->fromArray( $column_array );

// 검색 조건

// Advanced search
$sql = '';
$disp = empty($_GET['disp']) ? 'hide' : '';	// 상세검색 노출 여부
$today = date('Y-m-d');
$from_logged = empty($_GET['from_logged']) ? '' : $_GET['from_logged'];
$to_logged = empty($_GET['to_logged']) ? $today: $_GET['to_logged'];
$from_registered = empty($_GET['from_registered']) ? '' : $_GET['from_registered'];
$to_registered = empty($_GET['to_registered']) ? $today : $_GET['to_registered'];
$from_age = empty($_GET['from_age']) ? '' : $_GET['from_age'];
$to_age = empty($_GET['to_age']) ? '' : $_GET['to_age'];

$fulevel = empty($_GET['ulevel']) ? array(): $_GET['ulevel'];
$fgender = empty($_GET['gender']) ? array(): $_GET['gender'];
$fjpath = empty($_GET['jpath']) ? array(): $_GET['jpath'];

if ( !empty($from_logged) && !empty($to_logged) ) {
	$from_logged_deep = $from_logged . ' 00:00:00';
	$to_logged_deep = $to_logged . ' 23:59:59';
	$sql .= " AND last_login_dt >= '$from_logged_deep' AND last_login_dt <= '$to_logged_deep' ";
}

if ( !empty($from_registered) && !empty($to_registered) ) {
	$from_registered_deep = $from_registered . ' 00:00:00';
	$to_registered_deep = $to_registered . ' 23:59:59';
	$sql .= " AND user_registered >= '$from_registered_deep' AND user_registered <= '$to_registered_deep' ";
}

if ( !empty($from_age) && !empty($to_age) ) {
	$from_age_deep = date('Y') - $from_age . '-01-01';
	$to_age_deep = date('Y') - $to_age . '-12-31';
	$sql .= " AND birthday BETWEEN '$to_age_deep' AND '$from_age_deep' ";
}

if ( !empty($fulevel) ) {
	$impsql = '';

	foreach ( $fulevel as $key => $val ) {
		$impsql .= "OR user_level = '$val' ";
	}
	$sql .= ' AND (' . substr($impsql, 3) . ')';
}

if ( !empty($fgender) ) {
	$impsql = '';

	foreach ( $fgender as $key => $val ) {
		$impsql .= "OR gender = '$val' ";
	}
	$sql .= ' AND (' . substr($impsql, 3) . ')';
}

if ( !empty($fjpath) ) {
	$impsql = '';

	foreach ( $fjpath as $key => $val ) {
		$impsql .= "OR join_path = '$val' ";
	}
	$sql .= ' AND (' . substr($impsql, 3) . ')';
}

$query = "
		SELECT
			user_login,
			user_name,
			mobile,
			display_name,
			birthday,
			user_registered,
			last_login_dt
		FROM
			bt_users
		WHERE
			user_status <> '4'
			$sql
		ORDER BY
			ID DESC
";
$stmt = $wdb->prepare( $query );
$stmt->execute();
$user_results = $wdb->get_results($stmt);

$active_sheet->fromArray( $user_results, null, "A2" );

$active_sheet->getDefaultColumnDimension()->setWidth( 20 );

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('회원 내역');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;

?>