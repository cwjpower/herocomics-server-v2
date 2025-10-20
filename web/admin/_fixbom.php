<?php
/**
 * _fixbom.php - Strip UTF-8 BOM and leading whitespace from target files.
 * Usage (inside container):
 *   php /var/www/html/web/admin/_fixbom.php /var/www/html/web/admin/_preload.php
 *   php /var/www/html/web/admin/_fixbom.php --default  (fix common targets)
 */

$targets = [];

if ($argc > 1) {
  if ($argv[1] === '--default') {
    $targets = [
      '/var/www/html/web/admin/_preload.php',
      '/var/www/html/wps-config.php',
      '/var/www/html/wps-vars.php',
    ];
  } else {
    $targets = array_slice($argv, 1);
  }
} else {
  fwrite(STDERR, "Usage: php _fixbom.php <file1> [file2 ...] | --default\n");
  exit(1);
}

foreach ($targets as $f) {
  if (!file_exists($f)) {
    echo "[skip] $f (not found)\n";
    continue;
  }
  $s = file_get_contents($f);
  // Strip BOM
  if (substr($s, 0, 3) === "\xEF\xBB\xBF") {
    $s = substr($s, 3);
    $bom = 'removed';
  } else {
    $bom = 'none';
  }
  // Strip leading whitespace before opening tag
  $s = preg_replace('/^\s+<\?php/s', '<?php', $s, 1);
  file_put_contents($f, $s);
  echo "[ok] $f (BOM: $bom)\n";
}
