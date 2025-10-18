<?php
// furniture/includes/functions.php
// Common helper functions: sanitize, escape, CSRF token, auth helpers.
// Include this file from header.php before output starts.

declare(strict_types=1);

// Ensure session is started only once
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_lifetime' => 86400, // 24 hours
        'cookie_httponly' => true,
        'use_strict_mode' => true,
    ]);
}

/**
 * Simple input sanitizer (use more strict validation per field).
 */
function sanitize_input($data) {
    if (is_array($data)) {
        return array_map('sanitize_input', $data);
    }
    return trim((string)$data);
}

/**
 * HTML-escape helper to prevent XSS in templates.
 */
function e($str) {
    return htmlspecialchars((string)$str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * CSRF token generation and validation (store token in session).
 */
function csrf_token() : string {
    if (empty($_SESSION['_csrf_token']) || !is_string($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        error_log('Generated new CSRF token: ' . $_SESSION['_csrf_token']);
    }
    return $_SESSION['_csrf_token'];
}

function csrf_field() : string {
    $token = csrf_token();
    return '<input type="hidden" name="csrf_token" value="'.e($token).'">';
}

function verify_csrf_token($token) : bool {
    if (empty($token) || empty($_SESSION['_csrf_token'])) {
        error_log('CSRF verification failed: Token or session token is empty. Received: ' . ($token ?? 'none') . ', Session: ' . ($_SESSION['_csrf_token'] ?? 'none'));
        return false;
    }
    $result = hash_equals($_SESSION['_csrf_token'], (string)$token);
    if (!$result) {
        error_log('CSRF verification failed: Token mismatch. Received: ' . $token . ', Session: ' . $_SESSION['_csrf_token']);
    }
    return $result;
}

/**
 * Basic auth check (session-based).
 */
function is_logged_in() : bool {
    return !empty($_SESSION['user_id']);
}

/**
 * Flash message helper
 */
function flash_set(string $key, $message) {
    $_SESSION['flash'][$key] = $message;
}

function flash_get(string $key) {
    if (!empty($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
    return null;
}




/**
 * Safe redirect
 */
function redirect(string $url) {
    header('Location: ' . $url);
    exit;
}

