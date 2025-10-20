<?php
/*
 * Desc : API 통계
 * 	method : GET
 */
require_once '../../wps-config.php';

$code = 0;
$msg = '';

if ( empty($_GET['uid']) ) {
	$code = 401;
	$msg = '회원의 UID가 필요합니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$user_id = $_GET['uid'];

$total_book = '32권';	// 총 독서 권수
$total_time =  '21시간 03분';	// 총 독서 시간

$most_genre = '소설';		// 가장 많이 읽은 장르
$most_author = '김유정';	// 가장 많이 읽은 작가

$most_time = '오후 8:00';		// 많이 읽은 시간대
$most_day = '수요일';			// 많이 읽은 요일

$this_month = '3권';			// 이 달에 읽은 책
$this_year = '15권';			// 올해 읽은 책

$json = compact( 'code', 'msg', 'total_book', 'total_time' ,'most_genre', 'most_author', 'most_time', 'most_day', 'this_month', 'this_year' );
echo json_encode( $json );

?>