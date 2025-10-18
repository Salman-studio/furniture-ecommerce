<?php
// furniture/forgot_password.php
$pageTitle = 'Forgot Password';
require_once __DIR__ . '/includes/header.php';
?>
<div class="col-md-6 mx-auto my-4">
  <form method="post" action="forgot_password.php" class="card p-4 payment-panel">
    <?php echo csrf_field(); ?>
    <h4>Reset Password</h4>
    <p class="small-muted">Enter your registered email and we will send reset instructions.</p>
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control" required>
    </div>
    <button class="btn btn-primary-custom" type="submit">Send Reset Link</button>
  </form>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
