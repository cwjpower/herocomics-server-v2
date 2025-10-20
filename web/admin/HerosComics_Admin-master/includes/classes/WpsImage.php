<?php
/**
 * Name: Image manipulation
 * Description: Crop Image, etc
 *
 */
class WpsImage
{
	public $src_path = '';
	public $src_width = '';
	public $src_height = '';
	public $src_type = 0;
	public $src_attr = '';

	public function __construct($source_img)	{
		if (is_file($source_img)) {
			list( $this->src_width, $this->src_height, $this->src_type, $this->src_attr ) = getimagesize($source_img);
			
			if ( $this->src_type > 0 && $this->src_type < 4 ) {
				$this->src_path = $source_img;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function crop_image($src_x, $src_y, $src_w, $src_h, $dir) {
		
		if ( $this->src_type == 1 ) {
			$resource = imagecreatefromgif( $this->src_path );
		} else if ( $this->src_type == 2 ) {
			$resource = imagecreatefromjpeg( $this->src_path );
		} else if ( $this->src_type == 3 ) {
			$resource = imagecreatefrompng( $this->src_path );
		}
		
		$canvas = imagecreatetruecolor( $src_w, $src_h );
		
		imagecopyresampled( $canvas, $resource, 0, 0, $src_x, $src_y, $src_w, $src_h, $src_w, $src_w );
		
		$source_file_dir = UPLOAD_PATH . '/' . $dir;
		if ( !is_dir($source_file_dir) ) {
			mkdir($source_file_dir, 0777, true);
		}
		$file_ext = strtolower(pathinfo($this->src_path, PATHINFO_EXTENSION));
		$new_file_name = wps_make_rand() . '.' . $file_ext;
		
		$source = $source_file_dir . '/' . $new_file_name;
		
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
			$file_name = UPLOAD_URL . '/' . $dir . '/' . $new_file_name;
			return $file_name;
		} else {
			return false;
		}
	}
}

?>