<?php
/**
 * Name : Session Class
 * @author softsyw
 *
 */
class WpsSession
{
    private $_sesslife;
    private $_db;

    public function __construct($db) {
        $this->_sesslife = 60 * 60 * 24;	// 1 day
        $this->_db = $db;

        session_set_save_handler(array(&$this, 'sessOpen'),
            array(&$this, 'sessClose'),
            array(&$this, 'sessRead'),
            array(&$this, 'sessWrite'),
            array(&$this, 'sessDestroy'),
            array(&$this, 'sessGC'));
        register_shutdown_function('session_write_close');
        // 세션 에러 방지 ✅
        @session_start();
    }

    public function sessOpen($save_path, $session_name) {
        return true;
    }

    public function sessClose() {
        $this->sessGC($this->_sesslife);
        return true;
    }

    public function sessRead($sess_id) {
        // $time 변수 먼저 선언 ✅
        $time = time();

        $query	= "
			SELECT
				sess_data
			FROM
				bt_session
			WHERE
				sess_id = ? AND
				sess_expiry >= ?
		";
        $stmt = $this->_db->prepare($query);
        $stmt->bind_param('si', $sess_id, $time);
        $stmt->execute();
        $result = $this->_db->get_var($stmt);
        return $result;
    }

    public function sessWrite($sess_id, $sess_data) {
        $expiry = $this->_sesslife + time();

        $query  = "
			SELECT
				sess_id
			FROM
				bt_session
			WHERE
				sess_id = ?
		";
        $stmt = $this->_db->prepare($query);
        $stmt->bind_param('s', $sess_id);
        $stmt->execute();
        $stmt->store_result();	// required to fetch $stmt->num_rows

        // UPDATE sess_expiry if the sess_id exists.
        if ( $stmt->num_rows > 0 ) {
            $query = "
				UPDATE
					bt_session
				SET
					sess_expiry = ?,
					sess_data = ?
				WHERE
					sess_id = ?
		   ";
            $stmt = $this->_db->prepare($query);
            $stmt->bind_param('iss', $expiry, $sess_data, $sess_id);
            // create session if a sess_id does not exists.
        } else {
            // $time 변수 먼저 선언 ✅
            $time = time();

            $query  = "
				INSERT INTO
					bt_session
					(
						sess_id,
						sess_expiry,
						sess_data,
						sess_created
					)
				VALUES
					(?, ?, ?, ?)
		   	";
            $stmt = $this->_db->prepare($query);
            $stmt->bind_param('siis', $sess_id, $expiry, $sess_data, $time);
        }
        $stmt->execute();
        $stmt->free_result();
        $stmt->close();

        return true;
    }

    public function sessDestroy($sess_id) {
        $query  = "
			DELETE FROM
				bt_session
			WHERE
				sess_id = ?
		";
        $stmt = $this->_db->prepare($query);
        $stmt->bind_param('s', $sess_id);
        $stmt->execute();
        $stmt->free_result();
        $stmt->close();
        return true;
    }

    public function sessGC($sess_maxlifetime) {
        // $time 변수 먼저 선언 ✅
        $time = time();

        $query  = "
			DELETE FROM
				bt_session
			WHERE
				sess_expiry < ?
		";
        $stmt = $this->_db->prepare($query);
        $stmt->bind_param('i', $time);
        $stmt->execute();
        $stmt->free_result();
        $stmt->close();
        return true;
    }
}
?>
