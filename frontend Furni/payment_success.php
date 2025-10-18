<?php
session_start();
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/middleware/auth.php';
require_once __DIR__ . '/includes/header.php';

$pageTitle = 'Payment Success';
$order_id = (int)($_GET['order_id'] ?? 0);
?>

<div class="my-4 container">
    <h3>Payment Successful</h3>
    <p>Thank you for your purchase. Your order is being processed.</p>
    <a href="order_confirmation.php?order_id=<?php echo $order_id; ?>" class="btn btn-primary">View Order</a>
    <a href="order_history.php" class="btn btn-outline-secondary">View Orders</a>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>