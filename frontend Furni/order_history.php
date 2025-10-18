<?php
session_start();
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/middleware/auth.php';
require_once __DIR__ . '/includes/header.php';

$pageTitle = 'Order History';
$conn = include __DIR__ . '/includes/db_connection.php';
$orders = [];

$customer_id = $_SESSION['user_id'] ?? 0;

if ($conn instanceof mysqli && $customer_id > 0) {
    $stmt = $conn->prepare("
        SELECT order_id, order_number, final_amount, order_status, created_at
        FROM orders
        WHERE customer_id = ?
        ORDER BY created_at DESC
    ");
    if ($stmt) {
        $stmt->bind_param('i', $customer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
        $stmt->close();
    }
    $conn->close();
}
?>

<div class="my-4 container">
    <h3>Order History</h3>

    <?php if (empty($orders)): ?>
        <div class="alert alert-info">You have not placed any orders yet.</div>
        <a href="products.php" class="btn btn-primary">Shop Now</a>
    <?php else: ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Date Placed</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo e($order['order_number']); ?></td>
                        <td><?php echo e(date('F j, Y, g:i a', strtotime($order['created_at']))); ?></td>
                        <td>₹<?php echo number_format($order['final_amount'], 2); ?></td>
                        <td><?php echo e(ucfirst($order['order_status'])); ?></td>
                        <td>
                            <a href="order_confirmation.php?order_id=<?php echo $order['order_id']; ?>" class="btn btn-sm btn-primary">View</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>