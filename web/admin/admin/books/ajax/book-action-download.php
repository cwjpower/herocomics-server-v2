<?php
/**
 * Created by PhpStorm.
 * User: endstern
 * Date: 18. 3. 15.
 * Time: 오후 2:25
 */

require_once '../../../wps-config.php';

$book_id = $_GET['book_id'];

$filepath = UPLOAD_URL.'/books/'. $book_id . '/'.$book_id . '.svf';
$filesize = filesize($filepath);
$path_parts = pathinfo($filepath);
$filename = $path_parts['basename'];
$extension = $path_parts['extension'];

header("Pragma: public");
header("Expires: 0");
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Content-Transfer-Encoding: binary");
header("Content-Length: $filesize");

ob_clean();
flush();
readfile($filepath);