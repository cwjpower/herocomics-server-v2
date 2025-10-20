<?php
/*
 * Desc : 충전하기 > 결제정보 등록
 */
function lps_add_user_payment_list( $wps_payment_amount_rate ) {
	global $wdb;

	$user_id = wps_get_current_user_id();

	$payment_amount = intval($_POST['pay_amount']);
	$payment_method = $_POST['pay_method'];
	$payment_state = 'done';	// 즉시 완료
	
	$point_amount = $payment_amount * $wps_payment_amount_rate[$payment_amount];

	$meta_val['ip_addr'] = $_SERVER['REMOTE_ADDR'];
	$meta_val['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
	$meta_value = serialize($meta_val);

	$query = "
			INSERT INTO
				bt_user_payment_list
				(
					ID,
					user_id,
					payment_amount,
					payment_method,
					payment_state,
					point_amount,
					created_dt,
					payment_dt,
					meta_value
				)
			VALUES
				(
					NULL, ?, ?, ?, ?,
					?, NOW(), NOW(), ?
				)
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'iissis', $user_id, $payment_amount, $payment_method, $payment_state, $point_amount, $meta_value );
	$stmt->execute();
	$payment_id = $wdb->insert_id;
	
	if (!empty($payment_id)) {
		$payment_table = 'bt_user_payment_list';	// Table명
		$comment = compact( 'payment_table', 'payment_id', 'payment_method' );
		$cash_comment = serialize($comment); 
		lps_add_user_cash_logs( $user_id, $payment_amount, $point_amount, $cash_comment );
	}
}

/*
 * Desc : 회원 cash logs 정보 등록
 */
function lps_add_user_cash_logs( $user_id, $cash_amount, $point_amount, $comment ) {
	global $wdb;

	$current_user_cash = wps_get_user_meta( $user_id, 'lps_user_total_cash' );
	if ( empty($current_user_cash) ) {
		$current_user_cash = 0;
	}
	
	$current_user_point = wps_get_user_meta( $user_id, 'lps_user_total_point' );
	if ( empty($current_user_point) ) {
		$current_user_point = 0;
	}

	$cash_sum = $current_user_cash + $cash_amount;
	$point_sum = $current_user_point + $point_amount;

	$query = "
			INSERT INTO
				bt_user_cash_logs
				(
					ID,
					user_id,
					cash_used,
					cash_total,
					cash_comment,
					point_used,
					point_total,
					created_dt
				)
			VALUES
				(
					NULL, ?, ?, ?, ?,
					?, ?, NOW()
				)
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'iiisii', $user_id, $cash_amount, $cash_sum, $comment, $point_amount, $point_sum );
	$stmt->execute();

	$log_id = $wdb->insert_id;

	if ( !empty($log_id) ) {
		wps_update_user_meta( $user_id, 'lps_user_total_cash', $cash_sum );
		wps_update_user_meta( $user_id, 'lps_user_total_point', $point_sum );
	}
	return $log_id;
}

/*
 * Desc : cash 충전 내역
 */
function lps_get_cash_charged_logs( $month_ago = 1 ) {
	global $wdb;
	
	$user_id = wps_get_current_user_id();
	
	$from = date('Y-m-d H:i:s', time() - 86400 * 30 * $month_ago);
	$to = date('Y-m-d H:i:s');
	
	$query = "
			SELECT
				*
			FROM
				bt_user_cash_logs
			WHERE
				user_id = ? AND
				cash_used > 0 AND
				created_dt BETWEEN ? AND ?
			ORDER BY
				ID DESC
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'iss', $user_id, $from, $to );
	$stmt->execute();
	
	return $wdb->get_results($stmt);
}

/*
 * Desc : cash 사용 내역
 */
function lps_get_cash_used_logs( $month_ago = 1 ) {
	global $wdb;
	
	$user_id = wps_get_current_user_id();
	
	$from = date('Y-m-d H:i:s', time() - 86400 * 30 * $month_ago);
	$to = date('Y-m-d H:i:s');
	
	$query = "
			SELECT
				*
			FROM
				bt_user_cash_logs
			WHERE
				user_id = ? AND
				cash_used < 0 AND
				created_dt BETWEEN ? AND ?
			ORDER BY
				ID DESC
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'iss', $user_id, $from, $to );
	$stmt->execute();
	return $wdb->get_results($stmt);
}

/*
 * Desc : Point 충전 내역
 */
function lps_get_point_charged_logs( $month_ago = 1 ) {
	global $wdb;

	$user_id = wps_get_current_user_id();

	$from = date('Y-m-d H:i:s', time() - 86400 * 30 * $month_ago);
	$to = date('Y-m-d H:i:s');

	$query = "
			SELECT
				*
			FROM
				bt_user_cash_logs
			WHERE
				user_id = ? AND
				cash_used > 0 AND
				created_dt BETWEEN ? AND ?
			ORDER BY
				ID DESC
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'iss', $user_id, $from, $to );
	$stmt->execute();

	return $wdb->get_results($stmt);
}

/*
 * Desc : 회원의 모든 현금성 자산 합계  캐시 + Point
 */
function lps_get_total_user_money( $user_id ) {
	$usermeta = wps_get_user_meta( $user_id );
	$current_user_cash = @$usermeta['lps_user_total_cash'];
	$current_user_point = @$usermeta['lps_user_total_point'];
	
	$total_money = $current_user_cash + $current_user_point; 
	return $total_money; 
}

/*
 * Desc : 회원이 결제할 책들의 가격 합계
 * 		할인쿠폰이 있을 경우 계산한다.
 */
function lps_get_total_item_cost( $user_id ) {
	$book_pay = wps_get_user_meta( $user_id, 'lps_user_book_pay' );
	$paying_books = unserialize($book_pay);
	$total_cost = 0;
	
	if (!empty($paying_books)) {
		foreach ($paying_books as $key => $val) {
			$book_rows = lps_get_book($val);
			$sale_price = $book_rows['sale_price'];
			$total_cost += $sale_price;
		}
	}
	return $total_cost;
}

/*
 * Desc : 회원의 결제금액 총 합계
 */
function lps_get_total_user_payment( $user_id = NULL ) {
	global $wdb;
	
	$user_id = empty($user_id) ? wps_get_current_user_id() : $user_id;
	
	$query = "
			SELECT
				SUM( payment_amount )
			FROM
				bt_user_payment_list
			WHERE
				user_id = ? AND
				payment_state = 'done'
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'i',  $user_id );
	$stmt->execute();
	return $wdb->get_var($stmt);
}

/*
 * Desc : 주문 완료
 */
function lps_add_order( $uid = NULL ) {
	global $wdb;
	
	$user_id = empty($uid) ? wps_get_current_user_id() : $uid;
	
	// 쿠폰
	$coupon_id = empty($_POST['coupon_to_use']) ? 0 : $_POST['coupon_to_use'];
	$coupon_dc = lps_get_total_coupon_discount( $user_id, $coupon_id );
	
	$order_status = 9;
	$total_amount = lps_get_total_item_cost( $user_id );
	$coupon_discount = $coupon_dc;	// 쿠폰할인금액
	$discount_amount = 0;	// 기타할인금액
	$cyberpoint_paid = empty($_POST['using_point']) ? 0 : $_POST['using_point'];	// 사용한 포인트
	
	if (!empty($coupon_id) ) {
		$cp_row = lps_get_coupon_by_id( $coupon_id );
		$coupon_name = '[할인율] ' . $cp_row['discount_rate'] . "% \n" . 
						'[최소 사용 가능 금액] ' . $cp_row['item_price_min'] . "\n" . 
						'[최대 할인 가능 금액] ' . $cp_row['item_price_max'] . "\n" . 
						'[쿠폰 이름] ' . $cp_row['coupon_name'] . "\n" . 
						'[쿠폰 설명] ' . $cp_row['coupon_desc'];
	} else {
		$coupon_name = '';
	}
	
	$cybercash_paid = $total_amount - $coupon_discount - $discount_amount - $cyberpoint_paid;	// 사용한 현금
	
	$total_paid = $cybercash_paid + $cyberpoint_paid + $coupon_discount;
	$remote_ip = $_SERVER['REMOTE_ADDR'];
	$coupon_code = $coupon_id;
	
	$query = "
			INSERT INTO
				bt_order
				(
					order_id,
					order_status,
					user_id,
					total_amount,
					coupon_discount,
					discount_amount,
					cybercash_paid,
					cyberpoint_paid,
					total_paid,
					created_dt,
					updated_dt,
					remote_ip,
					coupon_code,
					coupon_name
				)
			VALUES
				(
					NULL, ?, ?, ?, ?,
					?, ?, ?, ?, NOW(),
					NOW(), ?, ?, ?
				)
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'iiiiiiiisss',
						$order_status, $user_id, $total_amount, $coupon_discount, $discount_amount,
						$cybercash_paid, $cyberpoint_paid, $total_paid, $remote_ip, $coupon_code, $coupon_name
	);
	$stmt->execute();
	$order_id = $wdb->insert_id;
	
	if ($order_id > 0) {
		lps_add_order_item( $user_id, $order_id );
		
		wps_update_user_meta( $user_id, 'lps_user_book_pay', '' );	// 주문할 책 초기화
		
		// cash 차감
		$meta = serialize(compact('order_id'));
		lps_add_user_cash_logs( $user_id, -$cybercash_paid, -$cyberpoint_paid, $meta );
	}
	
	return $order_id;
}

/*
 * Desc : 개별 주문 내역 DB 저장
 */
function lps_add_order_item( $user_id, $order_id ) {
	global $wdb;
	
	$book_pay = wps_get_user_meta( $user_id, 'lps_user_book_pay' );
	$paying_books = unserialize($book_pay);
	
	$wishlist = wps_get_user_meta($user_id, 'lps_user_wishlist');	// 찜리스트
	$wishlist_books = unserialize($wishlist);
	
	$query = "
			INSERT INTO
				bt_order_item
				(
					item_id,
					order_id,
					book_id,
					original_price,
					sale_price,
					book_title,
					epub_url,
					book_dc_rate
				)
			VALUES
				(
					NULL, ?, ?, ?, ?,
					?, ?, ?
				)
	";
	$stmt = $wdb->prepare($query);

	foreach ($paying_books as $key => $val) {
		$book_rows = lps_get_book($val);
		
		$book_id = $book_rows['ID'];
		$normal_price = $book_rows['normal_price'];
		$sale_price = $book_rows['sale_price'];
		$book_title = $book_rows['book_title'];
		$book_dc_rate = $book_rows['discount_rate'];
		
		$epub_url = '';
// 		$epub_url = lps_api_drm( $user_id, $book_id );		// 2017.06.08	pdf 파일 테스트
		
		$stmt->bind_param( 'iiiissi', $order_id, $book_id, $normal_price, $sale_price, $book_title, $epub_url, $book_dc_rate );
		$stmt->execute();
	}
	
	// 찜리스트에서 해당 book 삭제
	if (!empty($wishlist_books)) {
		$diff_books = array_diff($wishlist_books, $paying_books);
		$serialized = serialize($diff_books);
		wps_update_user_meta($user_id, 'lps_user_wishlist', $serialized);
	}
	
	lps_delete_item_from_cart($user_id, $paying_books);	// 주문완료건 카트에서 삭제
}

/*
 * Desc : 장바구니에서 주문완료된 책 삭제
 */
function lps_delete_item_from_cart( $user_id, $book_ids ) {
	$meta = wps_get_user_meta( $user_id, 'lps_user_cart' );
	$unserial = unserialize($meta);

	if (is_array($unserial)) {
		$result = array_diff($unserial, $book_ids);
		$serialized = serialize($result);
		wps_update_user_meta($user_id, 'lps_user_cart', $serialized);
	}
}

function lps_get_order_by_id( $order_id ) {
	global $wdb;
	
	$query = "
		SELECT
			*
		FROM
			bt_order
		WHERE
			order_id = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param('i', $order_id);
	$stmt->execute();
	return $wdb->get_row($stmt);
}

/*
 * Desc : Web 사용 내역, order_id 로 주문한 책 권수와 대표 서적의 제목
 */

function lps_get_order_summary( $order_id ) {
	global $wdb;

	$query = "
		SELECT
			b.book_title,
		    COUNT(*) AS total_count
		FROM
			bt_order_item AS i
		LEFT JOIN
			bt_books AS b
		ON
			i.book_id = b.ID
		WHERE
			i.order_id = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param('i', $order_id);
	$stmt->execute();
	return $wdb->get_row($stmt);
}

/*
 * Desc : 회원이 구입한 책의 private epub_url 찾기
 */
function lps_get_order_epub_url( $user_id, $book_id ) {
	global $wdb;
	
	if (empty($user_id) || empty($book_id)) {
		return '';
	}
	
	$query = "
		SELECT
			i.epub_url
		FROM
			bt_order AS o
		INNER JOIN
			bt_order_item AS i
		WHERE
			o.user_id = ? AND
			i.book_id = ? AND
			o.order_id = i.order_id
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param('ii', $user_id, $book_id);
	$stmt->execute();
	return $wdb->get_var($stmt);
}

// Dashboard : 누적 판매 부수
function lps_get_total_sale_book() {
	global $wdb;

	$user_id = wps_get_current_user_id();
	$user_level = wps_get_user_level();
	
	if ($user_level < 10) {		// 출판사, 작가
		$query = "
				SELECT
					COUNT(*)
				FROM
					bt_order_item AS i
				INNER JOIN
					bt_books AS b
				WHERE
					i.book_id = b.ID AND
				    b.publisher_id = '$user_id'
		";
	} else {
		$query = "
				SELECT
					COUNT(*)
				FROM
					bt_order_item
		";
	}
	$stmt = $wdb->prepare( $query );
	$stmt->execute();
	return $wdb->get_var($stmt);
}

// Dashboard : 오늘 판매 부수
function lps_get_today_sale_book() {
	global $wdb;
	
	$user_id = wps_get_current_user_id();
	$user_level = wps_get_user_level();
	
	if ($user_level < 10) {		// 출판사, 작가
		$query = "
				SELECT
					COUNT(*)
				FROM
					bt_order AS o
				INNER JOIN
					bt_order_item AS i
				INNER JOIN
					bt_books AS b
				WHERE
					o.order_id = i.order_id AND
				    b.publisher_id = '$user_id' AND
					o.created_dt BETWEEN CURDATE() AND CURDATE() + 1
		";
	} else {
		$query = "
				SELECT
					COUNT(*)
				FROM
					bt_order AS o
				INNER JOIN
					bt_order_item AS i
				WHERE
					o.order_id = i.order_id AND
					o.created_dt BETWEEN CURDATE() AND CURDATE() + 1
		";
	}
	$stmt = $wdb->prepare( $query );
	$stmt->execute();
	return $wdb->get_var($stmt);
}

/*
 * Desc : DRM
 */
function lps_api_drm( $user_id, $book_id, $device_id = NULL ) {

	$provider_id = 'devtest';
	$passwd = '1234qwer';
	$provider_key = hash('sha256', $passwd);

	// DRM Inside API로 프로바이더 아이디와 키값을 전달 하고 결과 값을 받음
	$url = 'https://test.opensdrm.net/issuelicense/providers/' . $provider_id . '/licenses';
	
	$headers = array(
			'provider-key: ' . $provider_key,
			'Content-Type: application/json;charset=utf-8'
	);
	
	$user_row = wps_get_user($user_id);
	$user_login = $user_row['user_login'];
	$device_id = $user_login;  // 디바이스 아이디는 사용자 이메일로 했음 - 기존 로직
	
	$book_row = lps_get_book($book_id);
	
	$ymd = date('Y') . '/' . date('md');

	// API 통신시 필요한 값들 ( API 문서 target 부분 참조 )
	$target['type'] = 'device';
	$target['user_id'] = $user_login;
	$target['device_id']['type'] = 'external';
	$target['device_id']['id'] = $device_id;	// 디바이스 아이디
	$rights['start'] = '';// '2016-01-01T00:00:00+09:00';
	$rights['end'] = ''; // '2018-01-01T00:00:00+09:00';
	$isbn = $book_row['isbn'];
	$price['priceValue'] = $book_row['sale_price'];
	$price['priceUnit'] = 'KRW';
	
	$drm_url = false;

	// Start zip
	$epub_master_file = $book_row['epub_path'];
// 	$epub_master_file = UPLOAD_PATH . '/books/20161252/1482737930-41165100_553656126';
	
	$zipfile = new PclZip( $epub_master_file );
	
	$zip_lists = $zipfile->listContent();
	
	if ( !empty($zip_lists) ) {
		foreach ( $zip_lists as $key => $val ) {
			if ( stripos($val['filename'], 'license.lcpl') !== false ) {
				// META-INF/license.lcpl 파일 압축 해제 - 퍼블릭 라이센스
				$license_lcpl = $zipfile->extractByIndex( $key, '../../upload/tmp/' . date('Ymd') . '/' . $user_id . '/' . $book_id );	// Real
				break;
			}
		}
		
		if (empty($license_lcpl[0]['filename'])) {
			return $drm_url;
		}
		
		$license_file = $license_lcpl[0]['filename'];
		$master_license = trim(file_get_contents($license_file), "\xEF\xBB\xBF");	// UTF-8 BOM	-> UTF-8
		unlink($license_file);
		
		// Request private DRM
		$provider_license = json_decode( $master_license, TRUE );
		
		$json = compact( 'target', 'rights', 'isbn', 'price', 'provider_license' );
		$post_data = json_encode($json);
		
		// Start cURL
		$ch = curl_init();
		
		$options = array(
				CURLOPT_URL				=> $url,
				CURLOPT_SSL_VERIFYPEER	=> false,
// 				CURLOPT_HEADER			=> false,
				CURLOPT_HTTPHEADER		=> $headers,
				CURLOPT_POST			=> true,
				CURLOPT_POSTFIELDS		=> $post_data,
				CURLOPT_TIMEOUT			=> 300,
				CURLOPT_RETURNTRANSFER	=> true
		);
		curl_setopt_array($ch, $options);
		
		$result = curl_exec($ch);	// private license.lcpl
		
		if ( $result === false ) {
// 			echo 'Curl error : ' . curl_errno($ch) . ' : ' . curl_error($ch);
			return $drm_url;
		} else {
			
			$json_array = json_decode($result, true);
			if (json_last_error() != JSON_ERROR_NONE ) {
				return $drm_url;
			}
			
			$path_to_extact = UPLOAD_PATH . '/drm/' . $ymd . '/' . $user_id . '/' . $book_id;

			// 압축 해제
			// 퍼블릭 라이센스를 프라이빗 라이센스로 대체해서 사용자 한 사람만을 위한 DRM 파일을 생성함
			$extract = $zipfile->extract( PCLZIP_OPT_PATH,  $path_to_extact );
			if ( !empty($extract) ) {
				foreach ( $extract as $key => $val ) {
					$filename = $val['filename'];
					if ( stripos($filename, 'license.lcpl') !== false ) {
						// META-INF/license.lcpl 삭제 후 생성 -> private epub 파일 생성
						unlink($filename);	// master license 삭제
						file_put_contents($filename, $result);	// private license 추가
					}
				}
			}
			// 압축
			$zip_archive = $user_id . '_' . $book_id . '_private_epub.zip';
			$drm_url = UPLOAD_URL . '/drm/' . $ymd . '/' . $user_id . '/' . $book_id . '/' . $zip_archive;
			
			$result = exec( 'cd ' . $path_to_extact . '; zip -r ' . $zip_archive . ' * ' );
			$drm_path = $path_to_extact . '/' . $zip_archive;
			
			if (!is_file($drm_path)) {
				$drm_url = false;
			}
		}
		
		curl_close($ch);
	}
	
	return $drm_url;
}

?>