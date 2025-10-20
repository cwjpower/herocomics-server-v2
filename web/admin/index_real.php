<?php
$candidates = ['login.php','main.php','dashboard.php','home.php','admin.php','index_real.php'];
foreach ($candidates as $f) {
  $p = __DIR__ . DIRECTORY_SEPARATOR . $f;
  if (is_file($p)) { require_once $p; exit; }
}
http_response_code(500);
echo "HeroComics Admin: entry not found in /web/admin (tried: ".implode(', ', $candidates).")";
