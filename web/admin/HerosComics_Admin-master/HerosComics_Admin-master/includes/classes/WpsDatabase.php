<?php
/**
 * Name: Database Connect, manage
 * Description: MySQLi, MySQLi STMT, MySQLi Result
 *
 * Usage:
 *  $db = new Database();
 *  $rs = $db->query($query);	or $stmt = $db->prepare($query);
 *  $result = $db->get_results($rs || $stmt);		// results with one or more records
 *  $result = $db->get_row($rs || $stmt);			// a record with one or more fields
 *  $result = $db->get_var($rs || $stmt);			// a record with one field
 *
 */
class WpsDatabase extends mysqli
{
	public $mysqli;

	public function __construct() {
		$this->mysqli = parent::__construct(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT, DB_SOCK);
		parent::set_charset( DB_CHARSET );

		return $this->mysqli;
	}

	public function get_results( $obj ) {
		$result = array();

		if ( $obj instanceof mysqli_stmt ) {
			$obj->store_result();

			$variables = array();
			$data = array();
			$meta = $obj->result_metadata();

			while ( $field = $meta->fetch_field() ) {
				$variables[] = &$data[$field->name];
			}

			call_user_func_array( array($obj, 'bind_result'), $variables );

			$i = 0;
			while ( $row = $obj->fetch() ) {
				foreach ( $data as $key => $val ) {
					$result[$i][$key] = $val;
				}
				$i++;
			}
			$obj->free_result();

		} else if ( $obj instanceof mysqli_result ) {
			while ( $row = $obj->fetch_assoc() ) {
				$result[] = $row;
			}
			$obj->free();
		}
		return $result;
	}

	public function get_row($obj) {
		$result = array();

		if ( $obj instanceof mysqli_stmt ) {
			$obj->store_result();

			$variables = array();
			$data = array();
			$meta = $obj->result_metadata();

			while ( $field = $meta->fetch_field() ) {
				$variables[] = &$data[$field->name];
			}

			call_user_func_array( array($obj, 'bind_result'), $variables );

			while ( $row = $obj->fetch() ) {
				foreach ( $data as $key => $val ) {
					$result[$key] = $val;
				}
			}
			$obj->free_result();

		} else if ( $obj instanceof mysqli_result ) {
			$result = $obj->fetch_assoc();
			$obj->free();
		}
		return $result;
	}

	public function get_var($obj) {
		$result = '';

		if ( $obj instanceof mysqli_stmt ) {
			$obj->store_result();

			$variables = array();
			$data = array();
			$meta = $obj->result_metadata();

			while ( $field = $meta->fetch_field() ) {
				$variables[] = &$data[$field->name];
			}

			call_user_func_array( array($obj, 'bind_result'), $variables );

			while ( $row = $obj->fetch() ) {
				foreach ( $data as $key => $val ) {
					$result = $val;
				}
			}
			$obj->free_result();

		} else if ( $obj instanceof mysqli_result ) {
			$result = $obj->fetch_row();
			$obj->free();
		}
		return $result;
	}

	public function transact() {
		return $this->autocommit(FALSE);
	}

	public function wps_commit() {
		return $this->commit();
	}

	public function wps_rollback() {
		return $this->rollback();
	}

	public function __destruct() {
		if (isset($this->mysqli)) {
			@$this->mysqli->close();
			unset($this->mysqli);
			if (!empty($this->mysqli)) {
				echo '<h4>Connection is not yet closed.</h4>';
			}
		}
	}
}
?>
