<?php
// furniture/includes/middleware/auth.php
// Simple authentication middleware: redirect to login if user not logged in.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['user_id'])) {
    // store desired URL for redirect after login if needed
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? '/';
    header('Location: /login.php');
    exit;
}

