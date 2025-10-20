<?php
/**
 * Name : Comment Class
 * Desciption : Comment Manage
 *
 *
 */
class WpsComment
{
	public $comment_ID;
	public $comment_post_ID;
	public $comment_author = '';
	public $comment_author_email = '';
	public $comment_author_url = '';
	public $comment_author_IP = '';
	public $comment_date = '0000-00-00 00:00:00';
	public $comment_date_gmt = '0000-00-00 00:00:00';
	public $comment_content = '';
	public $comment_karma = 0;
	public $comment_approved = '1';
	public $comment_agent = '';
	public $comment_type = '';
	public $comment_parent = 0;
	public $user_id = 0;

	public function __construct() {}

	public function wps_insert_comment( $postarr ) {
		global $wdb;

		$this->comment_post_ID = $postarr['post_id'];
		$this->comment_author = empty( $postarr['author'] ) ? '' : $postarr['author'];
		$this->comment_author_email = empty( $postarr['comment_author_email'] ) ? '' : $postarr['comment_author_email'];
		$this->comment_author_url = empty( $postarr['comment_author_url'] ) ? '' : $postarr['comment_author_url'];
		$this->comment_author_IP = $_SERVER['REMOTE_ADDR'];
		$this->comment_agent = $_SERVER['HTTP_USER_AGENT'];
		$this->comment_content = empty( $postarr['comment_content'] ) ? '' : $postarr['comment_content'];
		$this->user_id = wps_get_current_user_id();

		$query = "
				INSERT INTO
					wps_comments
					(
						comment_ID,
						comment_post_ID,
						comment_author,
						comment_author_email,
						comment_author_url,
						comment_author_IP,
						comment_date,
						comment_date_gmt,
						comment_content,
						comment_karma,
						comment_approved,
						comment_agent,
						comment_type,
						comment_parent,
						user_id
					)
				VALUES
					(
						NULL, ?, ?, ?, ?,
						?, NOW(), UTC_TIMESTAMP(), ?, ?,
						?, ?, ?, ?, ?
					)
		";
		$stmt = $wdb->prepare( $query );

		if ( !empty($stmt) ) {
			$stmt->bind_param( 'isssssisssii',
					$this->comment_post_ID,
					$this->comment_author,
					$this->comment_author_email,
					$this->comment_author_url,
					$this->comment_author_IP,
					$this->comment_content,
					$this->comment_karma,
					$this->comment_approved,
					$this->comment_agent,
					$this->comment_type,
					$this->comment_parent,
					$this->user_id
			);
		}
		if ( !$stmt->execute() ) {
			return false;
		}

		$this->comment_ID = $wdb->insert_id;

		return $this->comment_ID;
	}
}

?>