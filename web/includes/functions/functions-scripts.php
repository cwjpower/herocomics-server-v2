<?php
function lps_js_back( $msg ) {
	echo <<<EOD
			<script>
			alert("$msg");
			history.back();
			</script>
EOD;
	exit;
}

function lps_load_scripts( $type = NULL ) {
	if ( !strcmp($type, 'form') ) {
		echo '
			<script src="' . INC_URL . '/js/jquery.form.js"></script>
			<script src="' . INC_URL . '/js/jquery-ui.min.js"></script>
			<script src="' . INC_URL . '/js/jquery.serializeObject.min.js"></script>
			<script src="' . INC_URL . '/js/jquery.oLoader.min.js"></script>
			<script src="' . INC_URL . '/js/ckeditor-4.5.6/ckeditor.js"></script>
			<script src="' . INC_URL . '/js/ckeditor-4.5.6/adapters/jquery.js"></script>
			<script src="' . INC_URL . '/js/jquery.number.min.js"></script>
		';		
	} else if ( !strcmp($type, 'mobile') ) {
		echo '<script src="' . INC_URL . '/js/jquery.numeric.min.js"></script>';
		echo '<script src="' . INC_URL . '/js/lps-custom/lps-common.js"></script>';
	} else if ( !strcmp($type, 'callback') ) {
		echo '<script src="' . INC_URL . '/js/lps-custom/lps-callback.js"></script>';
	}
}

?>