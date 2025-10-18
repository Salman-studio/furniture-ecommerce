<?php
session_start();
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Login</h2>

    <?php
    $loginSuccess = false;
    if (isset($_POST['login'])) {
        // Verify CSRF
        if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            echo "<div class='alert alert-danger'>Invalid request.</div>";
        } else {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $errors = [];

            if (empty($email) || empty($password)) {
                array_push($errors, "Both email and password are required.");
            }

            if (count($errors) === 0) {
               $conn = include 'includes/db_connection.php';
                 if ($conn === false) {
                    die("Database connection failed. Please try again later.");
                 }

                $sql = "SELECT * FROM users WHERE email = ?";
                $stmt = mysqli_stmt_init($conn);

                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    die("SQL error");
                }

                mysqli_stmt_bind_param($stmt, "s", $email);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $user = mysqli_fetch_assoc($result);

                if ($user) {
                    if (password_verify($password, $user['password'])) {
                        $_SESSION['user_id'] = $user['user_id']; // Use actual ID column
                        $_SESSION['username'] = $user['username'];
                        echo "<div class='alert alert-success'>Login successful!</div>";
                        $loginSuccess = true;
                        // Regenerate CSRF
                        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                    } else {
                        echo "<div class='alert alert-danger'>Invalid email or password.</div>";
                    }
                } else {
                    echo "<div class='alert alert-danger'>Invalid email or password.</div>";
                }

                mysqli_stmt_close($stmt);
                mysqli_close($conn);
            } else {
                foreach ($errors as $error) {
                    echo "<div class='alert alert-danger'>$error</div>";
                }
            }
        }
    }

    if ($loginSuccess) {
        header("Location: index.php");
        exit();
    }
    ?>

    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" placeholder="Enter Email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" id="password" placeholder="Enter Password" name="password" class="form-control" required>
        </div>
        <input type="submit" value="Login" name="login" class="btn btn-primary">
    </form>
</div>
</body>
</html>