<?php
declare(strict_types=1);
/**
 * HeroComics CSRF helper (drop-in)
 * - Stable per-session token
 * - Hidden input helper
 * - Strict validation with hash_equals
 * - Optional DEV bypass via /tmp/disable_csrf (remove for production)
 */

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Fallback HTML escape helper if the project doesn't define h()
if (!function_exists('h')) {
    function h(string $s): string {
        return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

/**
 * Return the stable per-session CSRF token.
 */
function csrf_token(): string {
    if (!isset($_SESSION['csrf']) || !is_string($_SESSION['csrf']) || strlen($_SESSION['csrf']) !== 64) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32)); // 32 bytes = 64 hex chars
    }
    return $_SESSION['csrf'];
}

/**
 * Render a hidden CSRF field for HTML forms.
 */
function csrf_field(): string {
    return '<input type="hidden" name="_csrf" value="'.h(csrf_token()).'">';
}

/**
 * Validate an incoming CSRF token (dies with 400 on failure).
 * For development convenience, if /tmp/disable_csrf exists, validation is skipped.
 * REMOVE THE BYPASS FOR PRODUCTION.
 */
function csrf_check(?string $token): void {
    // DEV bypass (comment out or remove in production)
    if (is_file('/tmp/disable_csrf')) {
        return;
    }

    $token = (string)$token;
    $session = $_SESSION['csrf'] ?? '';
    if (!is_string($session) || $session === '' || !hash_equals($session, $token)) {
        http_response_code(400);
        die('잘못된 요청 (CSRF)');
    }
}
