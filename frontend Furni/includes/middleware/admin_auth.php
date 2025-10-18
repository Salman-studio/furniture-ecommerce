<?php
// furniture/includes/middleware/admin_auth.php
// Protect admin pages: checks for 'is_admin' flag in session.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['user_id']) || empty($_SESSION['is_admin'])) {
    header('Location: /login.php');
    exit;
}
