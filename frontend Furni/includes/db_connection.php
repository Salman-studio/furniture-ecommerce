<?php
$hostName = "localhost";
$dbuser = "root";
$dbPassword = "";
$dbName = "ecommerce_db";

$conn = mysqli_connect($hostName, $dbuser, $dbPassword, $dbName);
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

return $conn;
?>