<?php
header('Content-Type: text/html; charset=UTF-8');

require_once ABS_PATH . '/wps-vars.php';
require_once INC_PATH . '/classes/WpsDatabase.php';
require_once INC_PATH . '/classes/WpsSession.php';
require_once FUNC_PATH . '/functions.php';
require_once FUNC_PATH . '/functions-scripts.php';
require_once FUNC_PATH . '/functions-user.php';
require_once FUNC_PATH . '/functions-post.php';

$wdb = new WpsDatabase();
// // $wsession = new WpsSession($wdb);


?>