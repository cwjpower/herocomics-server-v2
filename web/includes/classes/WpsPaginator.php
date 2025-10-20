<?php
/**
 * Name : Pagination class
 * Usage: 
 * $paginator = new Paginator( $db, $page_no, $rows_count );
 * $result = $paginator->ls_init_pagination( $query, $sparam );
 * 
 */

class WpsPaginator 
{
	public $page_id;
	public $rows_per_page;
	public $group_per_page;
	public $total_records;
	public $total_rows;
	public $total_page;
	public $offset;
    public $db;


	public function __construct( $db, $page, $rows_count = null, $group_count = null ) {
		$this->db = $db;
		$this->page_id = empty($page) ? 1 : intval( $page );
		$this->rows_per_page = empty($rows_count) ? 10 : intval( $rows_count );
		$this->group_per_page = empty($group_count) ? 10 : intval( $group_count );
	}
	
	/*
	 * @params : array($type, $pam1, $pam2,...);
	 * 
	*/
	public function ls_init_pagination( $sql, $params = null ) {

        // get offset of LIMIT clause
		$this->offset = $this->_ls_get_offset();
		
		/*
		 * Get Total Records
		 *  
		*/
		$tquery = preg_replace( '/where[\s\w\W]+/i', '', $sql);
		$tquery = preg_replace( '/select(.*)from/is', 'SELECT COUNT(*) FROM', $tquery);
		$stmt = $this->db->prepare($tquery);

		$stmt->execute();
		$this->total_records = $this->db->get_var($stmt);
		
		$query = preg_replace('/^select/i', 'SELECT SQL_CALC_FOUND_ROWS', trim($sql));
		$auto_limit = ' LIMIT ' . $this->offset . ', ' . $this->rows_per_page;
		
		if ( stristr($query, 'LIMIT') === false ) {	// Attach LIMIT clause if a query does not have LIMIT clause.
			$query .= $auto_limit;
		} else {	// Replace LIMIT clause if a query has LIMIT clause.
			$query = preg_replace( '/LIMIT .*/i', $auto_limit, $query );
		}
		
		if ( $stmt = $this->db->prepare($query) ) {
			if ( $params != null ) {
				$refs = array();
				foreach ( $params as $key => $val ) {
					$refs[$key] = &$params[$key];
				}
				call_user_func_array( array($stmt, 'bind_param'), $refs );
			}
			$stmt->execute();
			$rows = $this->db->get_results($stmt);	// result set
		} else {
			$errmsg = '[Query error >>> Failed to prepared the statment. ' . date('Y-m-d H:i:s') . "]\n";
			$errmsg .= 'Query: ' . $query . "\n";
 			error_log($errmsg);

			exit('<h4>Error : Cannot execute pagination query</h4>');
		}
		
		$query = "SELECT FOUND_ROWS() AS total_rows";	// get the count of total total_rows
		$res = $this->db->query($query);
		$row = $res->fetch_assoc();
		$this->total_rows = $row['total_rows'];
		
		// get total pages from total total_rows
		$this->total_page = ceil($this->total_rows / $this->rows_per_page);
		
		return $rows;
	}
	
	public function ls_get_total_page() {
		$total_page = $this->total_page == 0 ? 1 : $this->total_page;
		return $total_page;
	}
	
	public function ls_get_total_records() {
		return $this->total_records;
	}
	
	public function ls_get_total_rows() {
		return $this->total_rows;
	}
	
	private function _ls_get_offset() {
		$offset = ($this->page_id - 1) * $this->rows_per_page;
		return $offset;
	}

	public function ls_pagination_link() {
		echo $this->ls_bootstrap_pagination_link();
// 		echo $this->ls_get_pagination_link();
	}
	
	/*
	 * Desc : bootstrap css용 Pagination, 관리자
	 */
	public function ls_bootstrap_pagination_link() {
		$total_group = ceil($this->total_page / $this->group_per_page);
		$cur_group = ceil($this->page_id / $this->group_per_page);
		
		$first_page_offset = ($cur_group - 1) * $this->group_per_page + 1;
		$last_page_offset = $cur_group * $this->group_per_page;
		if ($cur_group == $total_group) {
			$last_page_offset = $this->total_page;
		}
		
		// start
		$output = '<ul class="pagination">';
		$parameter = preg_replace('/page=[0-9]{0,}&*/', '', $_SERVER['QUERY_STRING']);
		if ( !empty($parameter) ) {
			$parameter = '&' . $parameter;
		}
		
		if ( $this->page_id > 1 ) {
			if ( $cur_group > 1 ) {
				$output .= '<li class="paginate_button previous"><a href="?page=' . ($first_page_offset - 1) . $parameter . '"  aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';
			}
		}
		
		for ( $i = $first_page_offset; $i <= $last_page_offset; $i++ ) {
			
			if ( $i == $this->page_id ) {
				$output .= '<li class="active"><a href="?page=' . $i . $parameter . '">' . $i . '</a></li>';
			} else {
				$output .= '<li><a href="?page=' . $i . $parameter . '">' . $i . '</a></li>';
			}
		}
		
		if ( $this->page_id != $this->total_page ) {
			if ( $cur_group < $total_group ) {
				$output .= '<li class="paginate_button next"><a href="?page=' . ($last_page_offset + 1) . $parameter . '" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>';
			}
		}
		
		if ( $this->total_page == 0 ) {
			$output = '';
		} else {
			$output .= '</ul>';
		}
		
		return $output;
	}
	
	/*
	 * Default Pagination
	 *
	 */
	public function ls_get_pagination_link( $class = '' ) {
		$total_group = ceil($this->total_page / $this->group_per_page);
		$cur_group = ceil($this->page_id / $this->group_per_page);
		
		$first_page_offset = ($cur_group - 1) * $this->group_per_page + 1;
		$last_page_offset = $cur_group * $this->group_per_page;
		if ($cur_group == $total_group) {
			$last_page_offset = $this->total_page;
		}
		
		// start
		$output = '<ul class="pagination ' . $class . '">';
		$parameter = preg_replace('/page=[0-9]{0,}&*/', '', $_SERVER['QUERY_STRING']);
		if ( !empty($parameter) ) {
			$parameter = '&' . $parameter;
		}
		
		if ( $this->page_id > 1 ) {
			if ( $cur_group > 1 ) {
				$output .= '<li><a href="?page=' . ($first_page_offset - 1) . $parameter . '"  aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';
			}
		}
		
		for ( $i = $first_page_offset; $i <= $last_page_offset; $i++ ) {
				
			if ( $i == $this->page_id ) {
				$output .= '<li class="active"><a href="?page=' . $i . $parameter . '">' . $i . '</a></li>';
			} else {
				$output .= '<li><a href="?page=' . $i . $parameter . '">' . $i . '</a></li>';
			}
		}
		
		if ( $this->page_id != $this->total_page ) {
			if ( $cur_group < $total_group ) {
				$output .= '<li><a href="?page=' . ($last_page_offset + 1) . $parameter . '" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>';
			}
		}
		
		if ( $this->total_page == 0 ) {
			$output = '';
		} else {
			$output .= '</ul>';
		}
		
		return $output;
	}
	
	/*
	 * 회원조회용 Ajax Paginator
	 *
	 */
	public function ls_get_ajax_pagination( $class = '' ) {
		$total_group = ceil($this->total_page / $this->group_per_page);
		$cur_group = ceil($this->page_id / $this->group_per_page);
		
		$first_page_offset = ($cur_group - 1) * $this->group_per_page + 1;
		$last_page_offset = $cur_group * $this->group_per_page;
		if ($cur_group == $total_group) {
			$last_page_offset = $this->total_page;
		}
		
		// start
		$output = '<ul class="pagination ' . $class . '">';
		
		if ( $this->page_id > 1 ) {
			if ( $cur_group > 1 ) {
				$output .= '<li><a title="' . ($first_page_offset - 1) . '"  aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';
			}
		}
		
		for ( $i = $first_page_offset; $i <= $last_page_offset; $i++ ) {
				
			if ( $i == $this->page_id ) {
				$output .= '<li class="active"><a title="' . $i . '">' . $i . '</a></li>';
			} else {
				$output .= '<li><a title="' . $i . '">' . $i . '</a></li>';
			}
		}
		
		if ( $this->page_id != $this->total_page ) {
			if ( $cur_group < $total_group ) {
				$output .= '<li><a title="' . ($last_page_offset + 1) . '" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>';
			}
		}
		
		if ( $this->total_page == 0 ) {
			$output = '';
		} else {
			$output .= '</ul>';
		}
		
		return $output;
	}
	
	/*
	 * Mobile Web Pagination
	 *
	 */
	public function ls_mobile_pagination_link( $class = '' ) {
		$total_group = ceil($this->total_page / $this->group_per_page);
		$cur_group = ceil($this->page_id / $this->group_per_page);
	
		$first_page_offset = ($cur_group - 1) * $this->group_per_page + 1;
		$last_page_offset = $cur_group * $this->group_per_page;
		if ($cur_group == $total_group) {
			$last_page_offset = $this->total_page;
		}
	
		// start
		$output = '<ul class="pagination ' . $class . '">';
		$parameter = preg_replace('/page=[0-9]{0,}&*/', '', $_SERVER['QUERY_STRING']);
		if ( !empty($parameter) ) {
			$parameter = '&' . $parameter;
		}
	
		if ( $this->page_id > 1 ) {
			if ( $cur_group > 1 ) {
				$output .= '<li class="prev"><a href="?page=' . ($first_page_offset - 1) . $parameter . '"  aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';
			}
		}
	
		for ( $i = $first_page_offset; $i <= $last_page_offset; $i++ ) {
	
			if ( $i == $this->page_id ) {
				$output .= '<li class="paging-on"><a href="?page=' . $i . $parameter . '">' . $i . '</a></li>';
			} else {
				$output .= '<li><a href="?page=' . $i . $parameter . '">' . $i . '</a></li>';
			}
		}
	
		if ( $this->page_id != $this->total_page ) {
			if ( $cur_group < $total_group ) {
				$output .= '<li class="next"><a href="?page=' . ($last_page_offset + 1) . $parameter . '" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>';
			}
		}
	
		if ( $this->total_page == 0 ) {
			$output = '';
		} else {
			$output .= '</ul>';
		}
	
		return $output;
	}
	
}
?>
