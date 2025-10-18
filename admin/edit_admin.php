<?php
  // Start session only if not already active
  if (session_status() === PHP_SESSION_NONE) {
      session_start();
      var_dump($_SESSION['admin_id']); // Debug: Check session ID
  }

  require_once 'includes/header.php';
  require_once 'includes/sidebar.php';
  require_once 'includes/db_connection.php';

  // Check if admin is logged in
  if (!isset($_SESSION['admin_id'])) {
      header('Location: login.php');
      exit();
  }

  $admin_id = intval($_SESSION['admin_id']);
  $errors = [];
  $success = '';

  // Handle form submission
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $email = trim($_POST['email'] ?? '');
      $password = $_POST['password'] ?? '';
      $username = trim($_POST['username'] ?? '');

      // Validate inputs
      if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
          $errors[] = 'Please enter a valid email address.';
      }
      if (!empty($password) && strlen($password) < 6) {
          $errors[] = 'Password must be at least 6 characters long.';
      }
      if (empty($username)) {
          $errors[] = 'Please enter a username.';
      }

      // Check if email is already in use (excluding current admin)
      if (empty($errors)) {
          $sql_check = "SELECT id FROM admin_users WHERE email = ? AND id != ? AND role = 'admin'";
          $stmt_check = mysqli_prepare($conn, $sql_check);
          mysqli_stmt_bind_param($stmt_check, 'si', $email, $admin_id);
          mysqli_stmt_execute($stmt_check);
          mysqli_stmt_store_result($stmt_check);
          if (mysqli_stmt_num_rows($stmt_check) > 0) {
              $errors[] = 'This email is already in use by another admin.';
          }
          mysqli_stmt_close($stmt_check);
      }

      // Update admin details if no errors
      if (empty($errors)) {
          $sql = "UPDATE admin_users SET email = ?, username = ?";
          $params = [$email, $username];
          $types = 'ss';

          if (!empty($password)) {
              $hashed_password = password_hash($password, PASSWORD_DEFAULT);
              $sql .= ", password = ?";
              $params[] = $hashed_password;
              $types .= 's';
          }

          $sql .= " WHERE id = ? AND role = 'admin'";
          $params[] = $admin_id;
          $types .= 'i';

          $stmt = mysqli_prepare($conn, $sql);
          mysqli_stmt_bind_param($stmt, $types, ...$params);
          if (mysqli_stmt_execute($stmt)) {
              $success = 'Admin details updated successfully.';
              $_SESSION['admin_name'] = $username; // Update session
             
          } else {
              $errors[] = 'Failed to update admin details: ' . mysqli_error($conn);
          }
          mysqli_stmt_close($stmt);
      }
  }

  // Fetch current admin details
  $sql = "SELECT id, email, username FROM admin_users WHERE id = ? AND role = 'admin'";
  $stmt = mysqli_prepare($conn, $sql);
  mysqli_stmt_bind_param($stmt, 'i', $admin_id);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  $admin = mysqli_fetch_assoc($result);

  if (!$admin) {
      echo "<div class='main-content container mt-4'>
              <div class='alert alert-danger fade-in animated-card'>Admin not found.</div>
            </div>";
      require_once 'includes/footer.php';
      exit();
  }
  ?>

  <!DOCTYPE html>
  <html lang="en">
  <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Edit Admin: <?= htmlspecialchars($admin['username']) ?></title>
      <!-- Bootstrap CSS -->
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
      <!-- Font Awesome -->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
      <style>
          .animated-card {
              transition: all 0.3s ease;
              border: none;
              box-shadow: 0 4px 6px rgba(0,0,0,0.1);
              border-radius: 10px;
              overflow: hidden;
          }
          .animated-card:hover {
              transform: translateY(-5px);
              box-shadow: 0 10px 20px rgba(0,0,0,0.15);
          }
          .fade-in {
              animation: fadeIn 0.5s ease-in;
          }
          @keyframes fadeIn {
              from { opacity: 0; transform: translateY(10px); }
              to { opacity: 1; transform: translateY(0); }
          }
          .btn-animated {
              transition: all 0.3s ease;
              border-radius: 6px;
          }
          .btn-animated:hover {
              transform: translateY(-2px);
              box-shadow: 0 4px 8px rgba(0,0,0,0.15);
          }
          .action-btn {
              transition: all 0.2s ease;
              border-radius: 5px;
              margin: 0 2px;
          }
          .action-btn:hover {
              transform: scale(1.1);
          }
          .main-content {
              padding: 20px;
              margin-left: 250px;
              transition: all 0.3s;
          }
          @media (max-width: 768px) {
              .main-content {
                  margin-left: 0;
              }
          }
      </style>
  </head>
  <body>
  <div class="main-content container mt-4">
      <div class="d-flex justify-content-between align-items-center mb-4 fade-in">
          <h2>Edit Admin Profile</h2>
          <a href="dashboard.php" class="btn btn-secondary btn-animated"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
      </div>

      <?php if (!empty($errors)): ?>
          <div class="alert alert-danger fade-in animated-card">
              <?php foreach ($errors as $error): ?>
                  <p class="mb-0"><?= htmlspecialchars($error) ?></p>
              <?php endforeach; ?>
          </div>
      <?php endif; ?>
      <?php if (!empty($success)): ?>
          <div class="alert alert-success fade-in animated-card">
              <p class="mb-0"><?= htmlspecialchars($success) ?></p>
          </div>
      <?php endif; ?>

      <div class="card mb-4 animated-card">
          <div class="card-body">
              <form method="POST">
                  <div class="form-group mb-3">
                      <label for="email"><strong>Email</strong></label>
                      <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($admin['email']) ?>" required>
                  </div>
                  <div class="form-group mb-3">
                      <label for="username"><strong>Username</strong></label>
                      <input type="text" name="username" id="username" class="form-control" value="<?= htmlspecialchars($admin['username']) ?>" required>
                  </div>
                  <div class="form-group mb-3">
                      <label for="password"><strong>New Password</strong> <small>(Leave blank to keep current password)</small></label>
                      <input type="password" name="password" id="password" class="form-control" placeholder="Enter new password">
                  </div>
                  <button type="submit" class="btn btn-primary btn-animated action-btn"><i class="fas fa-save"></i> Save Changes</button>
                  <a href="dashboard.php" class="btn btn-secondary btn-animated action-btn"><i class="fas fa-arrow-left"></i> Cancel</a>
              </form>
          </div>
      </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <?php require_once 'includes/footer.php'; ?>
  </body>
  </html>