<?php
// furniture/track_order.php
$pageTitle = 'Track Order';
require_once __DIR__ . '/includes/header.php';
?>
<div class="my-4">
  <h3>Track your order</h3>
  <form method="get" action="track_order.php" class="row g-2">
    <div class="col-md-6"><input class="form-control" name="order_id" placeholder="Order ID"></div>
    <div class="col-md-3"><button class="btn btn-primary-custom">Track</button></div>
  </form>
  <?php if(!empty($_GET['order_id'])): ?>
    <div class="mt-3">Status for <?php echo e($_GET['order_id']); ?>: <strong>Processing</strong></div>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
