<?php
require_once '../../../wps-config.php';
require_once FUNC_PATH . '/functions-fancytree.php';

$taxonomy = empty($_GET['tkey']) ? 'wps_category' : $_GET['tkey'];

echo wps_fancytree_init( $taxonomy );

?>