<?php
require_once '../../../wps-config.php';
require_once FUNC_PATH . '/functions-fancytree.php';

$taxonomy = 'wps_category_admin_menu';

$user_level = $_GET['user_level'];

echo wps_fancytree_init_selection($taxonomy, $user_level);

?>