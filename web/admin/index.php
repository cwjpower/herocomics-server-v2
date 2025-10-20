<?php
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors','1');

$vars = dirname(__DIR__, 1) . DIRECTORY_SEPARATOR . 'wps-vars.php';
if (is_file($vars)) { require_once $vars; }

$candidates = ['login.php','main.php','dashboard.php','home.php','admin.php','index_real.php'];
foreach ($candidates as $f) {
    $p = __DIR__ . DIRECTORY_SEPARATOR . $f;
    if (is_file($p)) { require $p; exit; }
}
http_response_code(500);
header('Content-Type: text/plain; charset=utf-8');
echo "HeroComics Admin: entry not found in /web/admin\n";
echo "Tried: " . implode(', ', $candidates) . "\n";
