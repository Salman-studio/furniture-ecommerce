
<?php
ob_start(); // Start output buffering
require_once 'includes/header.php';
require_once 'includes/sidebar.php';
require_once 'includes/db_connection.php';

// Get user ID safely
$id = intval($_GET['id'] ?? 0);

// Fetch user data
$sql = "SELECT * FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    header('Location: users.php');
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $username = strtolower(explode('@', $email)[0]);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);
    $status = trim($_POST['status']);
    $updated_at = date('Y-m-d H:i:s');

    // Validate inputs
    $valid_statuses = ['active', 'inactive', 'discontinued'];
    if (empty($first_name) || empty($last_name) || empty($email) || empty($role) || empty($status)) {
        $error = "Error: All required fields must be filled.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Error: Invalid email format.";
    } elseif (!in_array($status, $valid_statuses)) {
        $error = "Error: Invalid status selected.";
    } else {
        // Check for duplicate email or username (excluding current user)
        $check_sql = "SELECT user_id FROM users WHERE (email = ? OR username = ?) AND user_id != ?";
        $check_stmt = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($check_stmt, 'ssi', $email, $username, $id);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);

        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            $error = "Error: Email or username already exists.";
        } else {
            if (!empty($password)) {
                // Update with password
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                $sql = "UPDATE users SET username=?, password=?, email=?, first_name=?, last_name=?, role=?, status=?, updated_at=? WHERE user_id=?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, 'ssssssssi', $username, $hashed_password, $email, $first_name, $last_name, $role, $status, $updated_at, $id);
            } else {
                // Update without password
                $sql = "UPDATE users SET username=?, email=?, first_name=?, last_name=?, role=?, status=?, updated_at=? WHERE user_id=?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, 'sssssssi', $username, $email, $first_name, $last_name, $role, $status, $updated_at, $id);
            }

            if (mysqli_stmt_execute($stmt)) {
                header('Location: users.php');
                exit();
            } else {
                $error = "Error updating user: " . mysqli_stmt_error($stmt);
            }
            mysqli_stmt_close($stmt);
        }
        mysqli_stmt_close($check_stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        .main-content {
            padding: 30px;
            max-width: 600px;
            margin: 0 auto;
        }
        .form-control, .form-select {
            border-radius: 10px;
            border: 1px solid #ced4da;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 8px rgba(13, 110, 253, 0.2);
        }
        .btn-success {
            background: linear-gradient(135deg, #28a745, #20c997);
            border: none;
            border-radius: 10px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .btn-success:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        .btn-secondary {
            background: linear-gradient(135deg, #6c757d, #adb5bd);
            border: none;
            border-radius: 10px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .btn-secondary:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        .alert {
            border-radius: 10px;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        label {
            font-weight: 600;
            color: #333;
        }
        .form-text {
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
<div class="main-content">
    <h2 class="animate__animated animate__fadeInDown">Edit User</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-danger animate__animated animate__fadeIn"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="POST" class="animate__animated animate__fadeInUp">
        <div class="form-group">
            <label for="first_name">First Name *</label>
            <input name="first_name" id="first_name" value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>" required class="form-control" />
        </div>
        <div class="form-group">
            <label for="last_name">Last Name *</label>
            <input name="last_name" id="last_name" value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>" required class="form-control" />
        </div>
        <div class="form-group">
            <label for="email">Email *</label>
            <input name="email" id="email" type="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required class="form-control" />
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input name="password" id="password" type="password" class="form-control" />
            <small class="form-text text-muted">Leave blank to keep current password</small>
        </div>
        <div class="form-group">
            <label for="role">Role *</label>
            <select name="role" id="role" class="form-select" required>
                <option value="" <?php echo !isset($user['role']) ? 'selected' : ''; ?>>Select Role</option>
                <option value="admin" <?php echo ($user['role'] ?? '') === 'admin' ? 'selected' : ''; ?>>Admin</option>
                <option value="staff" <?php echo ($user['role'] ?? '') === 'staff' ? 'selected' : ''; ?>>Staff</option>
                <option value="customer" <?php echo ($user['role'] ?? '') === 'customer' ? 'selected' : ''; ?>>Customer</option>
            </select>
        </div>
        <div class="form-group">
            <label for="status">Status *</label>
            <select name="status" id="status" class="form-select" required>
                <option value="active" <?php echo ($user['status'] ?? '') === 'active' ? 'selected' : ''; ?>>Active</option>
                <option value="inactive" <?php echo ($user['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                <option value="discontinued" <?php echo ($user['status'] ?? '') === 'discontinued' ? 'selected' : ''; ?>>Discontinued</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Update</button>
        <a href="users.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
<?php ob_end_flush(); // Flush output buffer ?>
</body>
</html>
<?php require_once 'includes/footer.php'; ?>
