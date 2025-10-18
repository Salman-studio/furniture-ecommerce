<?php
// furniture/payment_failure.php
$pageTitle = 'Payment Failure';
require_once __DIR__ . '/includes/header.php';
?>
<div class="my-4">
  <h3>Payment Failed</h3>
  <p>There was an issue processing your payment. Please try again or contact support.</p>
  <a href="checkout.php" class="btn btn-primary-custom">Try Again</a>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
