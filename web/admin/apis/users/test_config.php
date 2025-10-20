<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing config load...\n";
echo "Current dir: " . getcwd() . "\n";
echo "Config file: " . realpath('../../wps-config.php') . "\n\n";

require_once '../../wps-config.php';

echo "DB_HOST: " . DB_HOST . "\n";
echo "DB_USER: " . DB_USER . "\n";
echo "DB_NAME: " . DB_NAME . "\n";
echo "DB_PORT: " . DB_PORT . "\n";
?>
