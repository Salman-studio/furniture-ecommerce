<?php
// db_connection.php
require_once 'config.php';

// Create a global connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("❌ Database connection failed: " . $conn->connect_error);
}

// Set charset
$conn->set_charset(DB_CHARSET);
?>
