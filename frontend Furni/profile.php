<?php
require_once __DIR__ . '/includes/functions.php';

// Check if user is logged in
if (!is_logged_in()) {
    redirect('login.php');
}

require_once __DIR__ . '/includes/db_connection.php';

// Fetch user details
$sql = "SELECT username, first_name, last_name, email FROM users WHERE user_id = ?";
$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
    die("SQL error: " . mysqli_error($conn));
}
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
mysqli_close($conn);

if (!$user) {
    flash_set('error', 'User not found.');
    redirect('logout.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">My Profile</h2>
    <?php if ($msg = flash_get('success')): ?>
        <div class="alert alert-success"><?php echo e($msg); ?></div>
    <?php endif; ?>
    <?php if ($msg = flash_get('error')): ?>
        <div class="alert alert-danger"><?php echo e($msg); ?></div>
    <?php endif; ?>
    <div class="card p-4">
        <p><strong>Username:</strong> <?php echo e($user['username']); ?></p>
        <p><strong>First Name:</strong> <?php echo e($user['first_name']); ?></p>
        <p><strong>Last Name:</strong> <?php echo e($user['last_name']); ?></p>
        <p><strong>Email:</strong> <?php echo e($user['email']); ?></p>
        <a href="edit_profile.php" class="btn btn-outline-primary">Edit Profile</a>
        <a href="logout.php" class="btn btn-outline-danger">Logout</a>
    </div>
</div>
</body>
</html>