<?php
function lps_add_coupon() {
	global $wdb;

	$coupon_type = 'cart'; //$_POST['coupon_type'];
	$coupon_name = $_POST['coupon_name'];
	$coupon_desc = empty($_POST['coupon_desc']) ? '' : $_POST['coupon_desc'];
	$period_from = empty($_POST['period_from']) ? NULL : $_POST['period_from'];
	$period_to = empty($_POST['period_to']) ? NULL : $_POST['period_to'];
	$discount_type = $_POST['discount_type'];
	$discount_amount = empty($_POST['discount_amount']) ? 0: $_POST['discount_amount'];
	$discount_rate = empty($_POST['discount_rate']) ? 0 : $_POST['discount_rate'];
	$item_price_min = empty($_POST['item_price_min']) ? 0 : $_POST['item_price_min'];
	$item_price_max = empty($_POST['item_price_max']) ? 0 : $_POST['item_price_max'];
	$related_publisher = empty($_POST['related_publisher']) ? 0 : $_POST['related_publisher'];

	if ($discount_rate > 100) {	// 할인율
		$discount_rate = 100;
	}
	
	if ($item_price_min > 0 && $discount_amount > $item_price_min) {
		$item_price_min = $discount_amount;
	}
	
	$query = "
			INSERT INTO
				bt_coupon
				(
					ID,
					coupon_name,
					coupon_type,
					coupon_desc,
					period_from,
					period_to,
					discount_type,
					discount_amount,
					discount_rate,
					item_price_min,
					item_price_max,
					related_publisher,
					created_dt
				)
			VALUES
				(
					NULL, ?, ?, ?, ?,
					?, ?, ?, ?, ?,
					?, ?, NOW()
				)
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'ssssssiiiii', 
						$coupon_name, $coupon_type, $coupon_desc, $period_from, $period_to,
						$discount_type, $discount_amount, $discount_rate, $item_price_min,
						$item_price_max, $related_publisher);

	$stmt->execute();
	$coupon_id = $wdb->insert_id;

	return $coupon_id;
}

/*
 * Desc : related_publisher 는 출판사를 지정하기 위해 사용하였으나 현재는 사용하지 않음. 따라서 bt_coupon 테이블만 사용해도 무방함.
 */
function lps_get_coupon_by_id( $id ) {
	global $wdb;
	
	$query = "
		SELECT
			c.*,
			u.display_name
		FROM
			bt_coupon AS c
		LEFT JOIN
			bt_users AS u
		ON
			u.ID = c.related_publisher
		WHERE 
			c.ID = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'i', $id);
	$stmt->execute();
	return $wdb->get_row($stmt);
}

function lps_edit_coupon() {
	global $wdb;

	$coupon_id = $_POST['coupon_id'];
	$coupon_type = 'cart'; //$_POST['coupon_type'];
	$coupon_name = $_POST['coupon_name'];
	$coupon_desc = empty($_POST['coupon_desc']) ? '' : $_POST['coupon_desc'];
	$period_from = empty($_POST['period_from']) ? NULL : $_POST['period_from'];
	$period_to = empty($_POST['period_to']) ? NULL : $_POST['period_to'];
	$discount_type = $_POST['discount_type'];
	$discount_amount = empty($_POST['discount_amount']) ? 0: $_POST['discount_amount'];
	$discount_rate = empty($_POST['discount_rate']) ? 0 : $_POST['discount_rate'];
	$item_price_min = empty($_POST['item_price_min']) ? 0 : $_POST['item_price_min'];
	$item_price_max = empty($_POST['item_price_max']) ? 0 : $_POST['item_price_max'];
	$related_publisher = empty($_POST['related_publisher']) ? 0 : $_POST['related_publisher'];

	if ($discount_rate > 100) {	// 할인율
		$discount_rate = 100;
	}

	if ($item_price_min > 0 && $discount_amount > $item_price_min) {
		$item_price_min = $discount_amount;
	}
	
	if (!strcmp($discount_type, 'amount')) {
		$discount_rate = 0;
		$item_price_max = 0;
	} else {
		$discount_amount = 0;
	}

	$query = "
			UPDATE
				bt_coupon
			SET
				coupon_name = ?,
				coupon_type = ?,
				coupon_desc = ?,
				period_from = ?,
				period_to = ?,
				discount_type = ?,
				discount_amount = ?,
				discount_rate = ?,
				item_price_min = ?,
				item_price_max = ?,
				related_publisher = ?
			WHERE
				ID = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'ssssssiiiiii',
			$coupon_name, $coupon_type, $coupon_desc, $period_from, $period_to,
			$discount_type, $discount_amount, $discount_rate, $item_price_min,
			$item_price_max, $related_publisher, $coupon_id);

	return $stmt->execute();
}

/*
 * Desc : 사용가능한 쿠폰 리스트, 결제에서 사용
 */
function lps_get_valid_coupons( $user_id = NULL ) {
	global $wdb;
	
	if ( empty($user_id) ) {
		$user_id = wps_get_current_user_id();
	}
	
	// 기 사용한 쿠폰 리스트
	$coupon_ids = lps_get_spent_coupons( $user_id );
	
	if ( !empty($coupon_ids) ) {
		$no_cp = implode(',', $coupon_ids);
	
		$query = "
			SELECT
				*
			FROM
				bt_coupon AS c
			WHERE
				CURDATE() BETWEEN period_from AND period_to AND
				ID NOT IN ( $no_cp )
			ORDER BY
				ID DESC
		";
	} else {
		$query = "
			SELECT
				*
			FROM
				bt_coupon AS c
			WHERE
				CURDATE() BETWEEN period_from AND period_to
			ORDER BY
				ID DESC
		";
	}
	
	$stmt = $wdb->prepare( $query );
	$stmt->execute();
	return $wdb->get_results($stmt);
}

/*
 * Desc : 쿠폰 적용 시 할인 금액 총액 구하기, 이미 할인된 책은 쿠폰 할인에서 제외한다.
 */
function lps_get_total_coupon_discount( $user_id, $coupon_id ) {
	$book_pay = wps_get_user_meta( $user_id, 'lps_user_book_pay' );
	$paying_books = unserialize($book_pay);
	$total_cost = 0;
	$coupon_dc = 0;
	
	if ( !empty($coupon_id) ) {
		$cp_row = lps_get_coupon_by_id($coupon_id);
		
		$discount_rate = $cp_row['discount_rate'];
		$item_price_min = $cp_row['item_price_min'];
		$item_price_max = $cp_row['item_price_max'];

		if (!empty($paying_books)) {
			foreach ($paying_books as $key => $val) {
				$book_rows = lps_get_book($val);
					
				$dc_rate = $book_rows['discount_rate'];	// 이미 할인 혜택
				$sale_price =  $book_rows['sale_price'];
				$total_cost += $sale_price;	// 지불할 총 금액
					
				if ($dc_rate > 0) {		// 할인 혜택받은 책은 쿠폰 혜택이 없다.
					$coupon_dc += 0;
				} else {
					$coupon_dc += $sale_price * $discount_rate / 100;
				}
			}
		
			if ( $total_cost > $item_price_min ) {	// 최소 사용 가능 금액 비교
				if ( $coupon_dc > $item_price_max ) {
					$coupon_dc = $item_price_max;
				}
			}
		}
	}
	return $coupon_dc;
}

/*
 * Desc : 사용한 쿠폰 ID 
 */
function lps_get_spent_coupons( $user_id ) {
	global $wdb;
	
	$coupons = [];
	
	$query = "
			SELECT
				o.coupon_code
			FROM
				bt_order AS  o
			INNER JOIN
				bt_order_item AS i
			WHERE
				o.user_id = ? AND
				o.order_id = i.order_id
			GROUP BY
				1
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param('i', $user_id);
	$stmt->execute();
	$results = $wdb->get_results($stmt);
	
	if ( !empty($results) ) {
		foreach ($results as $key => $val) {
			if ( !empty($val['coupon_code']) ) {
				array_push($coupons, $val['coupon_code']);
			}
		}
	}
	
	return $coupons;
}

?>