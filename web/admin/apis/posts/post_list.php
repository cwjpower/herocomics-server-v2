<?php
/*
 * Desc : API 담벼락 > 게시글
 * 	method : GET
 */
require_once '../../wps-config.php';
require_once FUNC_PATH . '/functions-book.php';
require_once INC_PATH . '/classes/WpsPaginator.php';

$code = 0;
$msg = '';

if ( empty($_GET['book_id']) ) {
	$code = 401;
	$msg = '책에 대한 정보가 필요합니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_GET['uid']) ) {
	$code = 400;
	$msg = '로그인 후 이용해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$book_id = $_GET['book_id'];
$user_id = $_GET['uid'];

$result = lps_is_book_owner($book_id, $user_id);

if (empty($result)) {
	$is_book_owner = 'N';
} else {
	$is_book_owner = 'Y';
}

// page number
$page = empty($_GET['page']) ? 1 : $_GET['page'];

// search
$qa = empty($_GET['qa']) ? '' : trim($_GET['qa']);
$q = !isset($_GET['q']) ? '' : trim($_GET['q']);
$sql = '';
$pph = '';
$sparam = [];

// Simple search
if ( !empty($q) && !empty($qa) ) {
	if ( !strcmp($qa, 'user_name') ) {
		$sql = " AND u.display_name LIKE ?";
		array_push( $sparam, '%' . $q . '%' );
	} else if ( !strcmp($qa, 'subject') ) {
		$sql = " AND a.subject LIKE ?";
		array_push( $sparam, '%' . $q . '%' );
	} else {	// all
		$sql = " AND a.subject LIKE ? OR a.content LIKE ? ";
		array_push( $sparam, '%' . $q . '%', '%' . $q . '%' );
	}
}

// Positional placeholder ?
if ( !empty($sql) ) {
	$pph_count = substr_count($sql, '?');
	for ( $i = 0; $i < $pph_count; $i++ ) {
		$pph .= 's';
	}
}

if (!empty($pph)) {
	array_unshift($sparam, $pph);
}

// 내가 차단한 회원의 글은 감춘다.
$banned_user_ids = implode(',', lps_get_banned_user_ids( $user_id ));
// filter sql
if (empty($banned_user_ids)) {
	$filter_sql = '';
} else {
	$filter_sql = " AND a.user_id NOT IN ($banned_user_ids) ";
}

$query = "
		SELECT
			a.id AS ID,
			a.subject,
			a.content,
			a.count_hit,
			a.count_like,
			a.count_comment,
			a.created_dt,
			u.display_name AS user_name,
			u.user_level,
			m.meta_value AS attach,CASE WHEN IFNULL(m.meta_value, 1) < 1 THEN '1' ELSE '0' END AS attach
		FROM
			bt_activity AS a
		LEFT JOIN
			bt_activity_meta AS m
		ON
			a.id = m.activity_id AND
			m.meta_key = 'wps-community-attachment'
		LEFT JOIN
			bt_users AS u
		ON
			a.user_id = u.ID
		WHERE
			a.component = 'activity' AND
			a.type = 'activity_update' AND
			a.book_id = '$book_id' AND
			a.is_deleted = '0'
			$filter_sql
			$sql
		ORDER BY
			a.id DESC
";
$paginator = new WpsPaginator($wdb, $page, 100);
$LIST = $paginator->ls_init_pagination( $query, $sparam );
// $total_count = $paginator->ls_get_total_rows();

$json = compact( 'code', 'msg', 'is_book_owner', 'LIST' );
echo json_encode( $json );

?>