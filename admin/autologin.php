<?php
session_start();

// Directly set admin session
$_SESSION['admin_id'] = 1;
$_SESSION['admin_name'] = 'Admin';

// Redirect to dashboard
header('Location: dashboard.php');
exit();
