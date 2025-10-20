<?php
/*
 * Desc : Redirect
 */
function wps_redirect( $url = NULL ) {
	if ( empty($url) ) {
		$url = SITE_URL;
	}
	header( 'Location: ' . $url );
	exit;
}

/*
 * Desc : IE Version Check
 * Triedent 4 ~ 7 : IE8 ~ IE11
 */
function wps_get_ie_version() {
	preg_match('/Trident\/\d{1,2}.\d{1,2}/', $_SERVER['HTTP_USER_AGENT'], $matches);
	if ( !empty($matches) ) {
		$version = intval(str_replace('trident/', '', strtolower($matches[0])));
	} else {	// IE외 브라우저는 사용 가능
		$version = 9;
	}
	return $version;
}

function get_file_extension( $file ) {
	return pathinfo( $file, PATHINFO_EXTENSION  );
}

/**
 * Options 관련
 */
function wps_get_option( $option_name = NULL ) {
	global $wdb;

	if ( empty($option_name) ) {
		$query = "
				SELECT
					*
				FROM
					bt_options
		";
		$stmt = $wdb->prepare( $query );
		$stmt->execute();
		return $wdb->get_results($stmt);
	} else {
		$query = "
				SELECT
					option_value
				FROM
					bt_options
				WHERE
					option_name = ?
		";
		$stmt = $wdb->prepare( $query );
		$stmt->bind_param( 's', $option_name );
		$stmt->execute();
		return $wdb->get_var($stmt);
	}
}

function wps_update_option_value( $option_name, $option_value ) {
	global $wdb;

	$query = "
			SELECT
				option_id
			FROM
				bt_options
			WHERE
				option_name = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 's', $option_name );
	$stmt->execute();
	$option_id = $wdb->get_var($stmt);

	if ( !empty($option_id) ) {
		$query = "
				UPDATE
					bt_options
				SET
					option_value = ?
				WHERE
					option_name = ?
		";
		$stmt = $wdb->prepare( $query );
		$stmt->bind_param( 'ss', $option_value, $option_name );
	} else {
		$query = "
				INSERT INTO
					bt_options
					(
						option_id,
						option_name,
						option_value
					)
				VALUES
					(
						NULL, ?, ?
					)
		";
		$stmt = $wdb->prepare( $query );
		$stmt->bind_param( 'ss', $option_name, $option_value );

	}
	return $stmt->execute();
}

/*
 * Desc : wps_get_xxx_meta( $id )에 의해 나온 결과값(배열)에서 meta_key로 meta_value를 찾는다.
 * 			wps_get_option() 결과값에서 option_key로 option_value를 찾기 위해 meta_value parameter 추가
 */
function wps_get_meta_value_by_key( $meta_val, $meta_key, $column = 'meta_value' ) {
	$value = false;

	foreach ( $meta_val as $key => $val ) {
		if ( is_array( $val) ) {
			foreach ( $val as $k => $v ) {
				if ( !strcmp( $v, $meta_key ) ) {
					$value = $val[$column];
					break;
				}
			}
		} else {
			if ( !strcmp($meta_key, $key) ) {
				$value = $val;
				break;
			}
		}
	}
	return $value;
}

/*
 * Desc : serailized array의 처음 file_url 값을 반환
 */
function wps_get_first_fileurl( $serialized ) {
	$file_url = false;
	if ( !empty($serialized) ) {
		$unserialized = unserialize( $serialized );
		if ( !empty($unserialized) ) {
			$file_url = $unserialized[0]['file_url'];
		}
	}
	return $file_url;
}

/*
 * Desc : post_type이 wps_board인 post
 */
function wps_get_boards() {
	global $wdb;

	$query = "
			SELECT
				ID,
				post_title
			FROM
				wps_posts
			WHERE
				post_type = 'wps_board'
			ORDER BY
				ID DESC
	";
	$obj = $wdb->query($query);
	return $wdb->get_results($obj);
}

/*
 * Desc : serialize array 추가/수정
 */
function lps_update_serialized_array( $serialized, $array ) {
	if ( is_array($serialized) && is_array($array) ) {
		foreach ( $serialized as $key => $val ) {
			foreach ( $array as $k => $v ) {
				if ( $key == $k ) {		// update
					$serialized[$key] = $v;
					break;
				}
			}
		}
		foreach ( $array as $key => $val ) {
			if ( !array_key_exists($key, $serialized) ) {
				$serialized[$key] = $val;
			}
		}
		return serialize($serialized);
	} else {
		return false;
	}
}

function wps_sanitize_text( $str ) {
	return str_replace('%20', '-', strtolower(rawurlencode($str)));
}

/*
 * Desc : 난수 생성, ex) 1418891543-68353500_488364154
 */
function wps_make_rand() {
	list($usec, $sec) = explode(' ', microtime());
	return $sec . '-' . substr($usec, 2) . '_' . mt_rand();
}

function lps_alert_back( $str ) {
	echo <<<EOD
	<script>
			alert("$str");
			history.back();
	</script>
EOD;
	exit;
}

/*
 * Desc : 파일 path에 suffix 추가 후 반환
 */
function wps_get_thumb_file_name( $path, $suffix ) {
	$path_parts = pathinfo($path);
	$tname = $path_parts['filename'] . $suffix;
	if ( empty($path_parts['extension']) ) {
		$gname = $path_parts['dirname'] . '/' . $tname;
	} else {
		$gname = $path_parts['dirname'] . '/' . $tname . '.' . $path_parts['extension'];
	}
	return $gname;
}

function wps_format_bytes( $size, $precision = 2 ) {
	$base = log($size, 1024);
	$suffixes = array('B', 'KB', 'MB', 'GB', 'TB');
	return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
}

/*
 * Desc : Set value의 key로 값을 반환
 */
function lps_get_value_by_key( $set_value, $haystack ) {
	$array = explode(',', $set_value);
	$val_array = [];
	foreach ($array as $key => $val) {
		array_push($val_array, $haystack[$val]);
	}
	return implode(',', $val_array);
}

function lps_easy_to_read_mobile( $mobile ) {
	$pattern = strlen($mobile) == 10 ?  '/(010|016|017|018|019)(\d{1,3})(\d{1,4})/' : '/(010|016|017|018|019)(\d{1,4})(\d{1,4})/';
	$replacement = '${1}-${2}-${3}';
	return preg_replace( $pattern, $replacement, $mobile );
}

function lps_filter_birth_format( $birth ) {
	$birth = preg_replace('/\D/', '', $birth);
	if (strlen($birth) == 8) {
		$pattern = '/(\d{1,4})(\d{1,2})(\d{1,2})/';
		$replacement = '${1}-${2}-${3}';
		return preg_replace( $pattern, $replacement, $birth );
	} else {
		return false;
	}
}

function lps_error_log ( $log_file, $error_msg ) {
	return file_put_contents($log_file, $error_msg, FILE_APPEND | LOCK_EX);
}

function lps_sanitize_mobile_number( $str ) {
	$pattern = '/\D/';
	return preg_replace( $pattern, '', $str );
}

function lps_check_mobile_number( $str ) {
	$number = lps_sanitize_mobile_number( $str );
	$len = strlen($number);

	if ( substr($number, 0, 2) == "01" && $len > 9 && $len < 12  ) {
		return true;
	} else {
		return false;
	}
}
?>