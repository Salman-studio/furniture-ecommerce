<?php
session_start(); // For CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Register</h2>

    <?php
    if (isset($_POST["submit"])) {
        // Verify CSRF
        if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            die("CSRF token mismatch");
        }

        $username = $_POST["username"] ?? '';
        $fullName = $_POST["fullName"] ?? '';
        $email = $_POST["email"] ?? '';
        $password = $_POST["password"] ?? '';
        $passwordRepeat = $_POST["repeat_password"] ?? '';
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $errors = array();

        if (empty($username) || empty($fullName) || empty($email) || empty($password) || empty($passwordRepeat)) {
            array_push($errors, "All fields are required");
        }

        if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
            array_push($errors, "Username must be 3-20 chars (letters, numbers, underscore)");
        }

        // Split fullName (simple: first word = first_name, rest = last_name)
        $nameParts = explode(' ', trim($fullName), 2);
        $firstName = $nameParts[0] ?? '';
        $lastName = $nameParts[1] ?? '';
        if (empty($firstName) || empty($lastName)) {
            array_push($errors, "Full Name must include first and last name");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            array_push($errors, "Email is not valid");
        }

        if (strlen($password) < 8) {
            array_push($errors, "Password must be at least 8 characters long");
        }

        if ($password !== $passwordRepeat) {
            array_push($errors, "Passwords do not match");
        }

        if (count($errors) === 0) {
            include 'includes/db_connection.php';

            // Check username exists
            $sqlCheckUser = "SELECT * FROM users WHERE username = ?";
            $stmtCheckUser = mysqli_stmt_init($conn);
            mysqli_stmt_prepare($stmtCheckUser, $sqlCheckUser);
            mysqli_stmt_bind_param($stmtCheckUser, "s", $username);
            mysqli_stmt_execute($stmtCheckUser);
            mysqli_stmt_store_result($stmtCheckUser);
            if (mysqli_stmt_num_rows($stmtCheckUser) > 0) {
                array_push($errors, "Username already exists");
            }
            mysqli_stmt_close($stmtCheckUser);

            // Check email exists
            $sqlCheckEmail = "SELECT * FROM users WHERE email = ?";
            $stmtCheckEmail = mysqli_stmt_init($conn);
            mysqli_stmt_prepare($stmtCheckEmail, $sqlCheckEmail);
            mysqli_stmt_bind_param($stmtCheckEmail, "s", $email);
            mysqli_stmt_execute($stmtCheckEmail);
            mysqli_stmt_store_result($stmtCheckEmail);
            if (mysqli_stmt_num_rows($stmtCheckEmail) > 0) {
                array_push($errors, "Email already exists");
            }
            mysqli_stmt_close($stmtCheckEmail);

            mysqli_close($conn);
        }

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                echo "<div class='alert alert-danger'>$error</div>";
            }
        } else {
            $conn = include 'includes/db_connection.php';
               if ($conn === false) {
                die("Database connection failed. Please try again later.");
               }
            // Insert with defaults
            $sqlInsert = "INSERT INTO users (username, first_name, last_name, email, password, role, status) VALUES (?, ?, ?, ?, ?, 'user', 'active')";
            $stmtInsert = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmtInsert, $sqlInsert)) {
                die("SQL error");
            }
            mysqli_stmt_bind_param($stmtInsert, "sssss", $username, $firstName, $lastName, $email, $passwordHash);
            if (mysqli_stmt_execute($stmtInsert)) {
                echo "<div class='alert alert-success'>You are registered successfully.</div>";
                // Regenerate CSRF
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            } else {
                echo "<div class='alert alert-danger'>Something went wrong.</div>";
            }
            mysqli_stmt_close($stmtInsert);
            mysqli_close($conn);
        }
    }
    $registrationSuccess = false; // or null, depending on your logic
     if ($registrationSuccess) {
        header("Location: login.php");
        exit();
     }
    ?>

    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" id="username" name="username" class="form-control" placeholder="Username" required>
        </div>
        <div class="mb-3">
            <label for="fullName" class="form-label">Full Name</label>
            <input type="text" id="fullName" name="fullName" class="form-control" placeholder="First Last Name" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" class="form-control" placeholder="Email" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
        </div>
        <div class="mb-3">
            <label for="repeat_password" class="form-label">Repeat Password</label>
            <input type="password" id="repeat_password" name="repeat_password" class="form-control" placeholder="Repeat Password" required>
        </div>
        <input type="submit" name="submit" value="Register" class="btn btn-primary">
    </form>
</div>
</body>
</html>