<?php
session_start(); // start session on every page

require_once 'db_connection.php';

/**
 * Check if admin is logged in
 * @return bool
 */
function is_logged_in() {
    return isset($_SESSION['admin_id']);
}

/**
 * Redirect to a given URL
 * @param string $url
 */
function redirect($url) {
    header("Location: $url");
    exit();
}

/**
 * Get admin details from session
 * @return array|null
 */
function get_admin_details() {
    if (!is_logged_in()) return null;

    global $conn;
    $id = $_SESSION['admin_id'];

    $stmt = $conn->prepare("SELECT id, username, email FROM users WHERE id = ? AND role = 'admin'");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    $stmt->close();

    return $admin ?: null;
}

/**
 * Login admin
 * @param string $email
 * @param string $password
 * @return bool|string
 */
function admin_login($email, $password) {
    global $conn;

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin'");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['admin_id']   = $user['id'];
        $_SESSION['admin_name'] = $user['username'];
        return true;
    }

    return "Invalid credentials or not an admin!";
}

/**
 * Logout admin
 */
function admin_logout() {
    session_unset();
    session_destroy();
    redirect(BASE_URL . "login.php");
}
?>
