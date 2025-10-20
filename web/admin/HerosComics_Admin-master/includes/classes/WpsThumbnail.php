<?php
/**
 * Name: Thumbnail
 * Description: Image Thumbnail
 *
 * Usage:
 *  bool $thumbnail->resize_image( $soure_file_path, '-suffix', 150, 150 );
 *
 */
class WpsThumbnail
{
	public $src_path = '';
	public $src_width = '';
	public $src_height = '';
	public $src_type = 0;
	public $src_attr = '';

	public function __construct()	{
		if ( !extension_loaded('gd') ) {
			exit( 'Error : GD library is not installed.' );
		}
	}

	public function resize_image( $source, $suffix, $thumb_width, $thumb_height = NULL) {

		//error_log("====" .$source);
		list( $this->src_width, $this->src_height, $this->src_type, $this->src_attr ) = getimagesize($source);

		if ( $this->src_type > 0 ) {
			$this->src_path = $source;
			$resource = null;
			$result = false;
			$nwidth = 0;
			$nheight = 0;

			$file_info = pathinfo($source);
			$source_file_dir = $file_info['dirname'];
			$source_file_name = $file_info['filename'];
			$img_exts = array( 1 => 'gif', 2 => 'jpg', 3 => 'png' );
			$source_file_ext = $img_exts[$this->src_type];

			if ( !is_writable($source_file_dir) ) {
				return false;
			}
			if ( empty($thumb_width) ) {
				return false;
			}
			if ( is_file($source) ) {
				do { // 임의의 중복되지 않는 화일명을 구한다.
				    $full_filename = $source_file_name . $suffix . '.'  . $source_file_ext;
				    $source = $source_file_dir . '/' . $full_filename;
				    if ( !is_file($source) ) {
				        break;
				    }
				} while(1);
			}

			/*
			 * 2015.04.05		softsyw
			 * Desc : 썸네일보다 원본이 작을 경우에는 썸네일을 만들지 않는다.
			 */
			if ( $this->src_width < $thumb_width ) {
				return $source_file_name . '.' . $source_file_ext;
			}

			if ( strpos($thumb_width, '%') !== false ) {	// ratio
				$nwidth = $this->src_width * intval($thumb_width) / 100;
				$nheight = $this->src_height * intval($thumb_width) / 100;
			} else {	// fixed
				$nwidth = $thumb_width;
				if ( $thumb_height ) {
					$nheight = $thumb_height;
				} else {
					$nheight = $nwidth / $this->src_width * $this->src_height;
				}
			}

		    if ( $this->src_type == 1 ) {
		    	$resource = imagecreatefromgif( $this->src_path );
		    } else if ( $this->src_type == 2 ) {
		    	$resource = imagecreatefromjpeg( $this->src_path );
		    } else if ( $this->src_type == 3 ) {
		    	$resource = imagecreatefrompng( $this->src_path );
		    }
		    $canvas = imagecreatetruecolor( $nwidth, $nheight );
		    imagecopyresampled( $canvas, $resource, 0, 0, 0, 0, $nwidth, $nheight, $this->src_width, $this->src_height );

			if ( $this->src_type == 1 ) {
				$result = imagegif( $canvas, $source );
		    } else if ( $this->src_type == 2 ) {
		    	$result = imagejpeg( $canvas, $source );
		    } else if ( $this->src_type == 3 ) {
		    	$result = imagepng( $canvas, $source );
		    }

		    imagedestroy( $resource );
		    imagedestroy( $canvas );

		    if ( $result ) {
		    	return $full_filename;
		    } else {
		    	return false;
		    }
		} else {
			return false;
		}
	}

}

?>
