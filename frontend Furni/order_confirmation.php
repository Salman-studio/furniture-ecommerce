<?php
session_start();
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/middleware/auth.php';
require_once __DIR__ . '/includes/header.php';

$pageTitle = 'Order Confirmation';
$order_id = (int)($_GET['order_id'] ?? 0);
$conn = include __DIR__ . '/includes/db_connection.php';
$order = null;
$order_items = [];

if ($conn instanceof mysqli && $order_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ? AND customer_id = ?");
    if ($stmt) {
        $stmt->bind_param('ii', $order_id, $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $order = $result->fetch_assoc();
        $stmt->close();

        if ($order) {
            $stmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
            if ($stmt) {
                $stmt->bind_param('i', $order_id);
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    $order_items[] = $row;
                }
                $stmt->close();
            }
        }
    }
    $conn->close();
}
?>

<div class="my-4 container">
    <h3>Order Confirmation</h3>

    <?php if (!$order): ?>
        <div class="alert alert-danger">Invalid or unauthorized order.</div>
        <a href="products.php" class="btn btn-primary">Continue Shopping</a>
    <?php else: ?>
        <div class="alert alert-success">Thank you for your order!</div>

        <h5>Order #<?php echo e($order['order_number']); ?></h5>
        <p><strong>Placed on:</strong> <?php echo e(date('F j, Y, g:i a', strtotime($order['created_at']))); ?></p>
        <p><strong>Customer:</strong> <?php echo e($order['customer_name']); ?> (<?php echo e($order['customer_email']); ?>)</p>
        <p><strong>Phone:</strong> <?php echo e($order['customer_phone']); ?></p>
        <p><strong>Shipping Address:</strong> <?php echo e($order['shipping_address']); ?></p>
        <p><strong>Billing Address:</strong> <?php echo e($order['billing_address']); ?></p>

        <table class="table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Unit Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order_items as $item): ?>
                    <tr>
                        <td><?php echo e($item['product_name']); ?></td>
                        <td>₹<?php echo number_format($item['unit_price'], 2); ?></td>
                        <td><?php echo e($item['quantity']); ?></td>
                        <td>₹<?php echo number_format($item['total_price'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                    <td>₹<?php echo number_format($order['total_amount'], 2); ?></td>
                </tr>
                <tr>
                    <td colspan="3" class="text-end"><strong>Shipping:</strong></td>
                    <td>₹<?php echo number_format($order['shipping_cost'], 2); ?></td>
                </tr>
                <tr>
                    <td colspan="3" class="text-end"><strong>Total:</strong></td>
                    <td>₹<?php echo number_format($order['final_amount'], 2); ?></td>
                </tr>
            </tfoot>
        </table>

        <p><strong>Payment Method:</strong> <?php echo e($order['payment_method']); ?></p>
        <p><strong>Payment Status:</strong> <?php echo e($order['payment_status']); ?></p>
        <p><strong>Order Status:</strong> <?php echo e(ucfirst($order['order_status'])); ?></p>
        <?php if (!empty($order['notes'])): ?>
            <p><strong>Notes:</strong> <?php echo e($order['notes']); ?></p>
        <?php endif; ?>

        <div class="text-end">
            <a href="products.php" class="btn btn-primary">Continue Shopping</a>
            <a href="order_history.php" class="btn btn-outline-secondary">View Order History</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>