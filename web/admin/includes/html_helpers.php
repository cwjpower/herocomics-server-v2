<?php
/**
 * PHP 8.1+ null safe htmlspecialchars wrapper
 */
function h($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
?>
