<?php
session_start();
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/middleware/auth.php';
require_once __DIR__ . '/includes/header.php';

$pageTitle = 'Order Review';
$cart = $_SESSION['cart'] ?? [];
$conn = include __DIR__ . '/includes/db_connection.php';

$total = 0;
$shipping_cost = 50; // Fixed shipping cost
$products = [];

if ($conn instanceof mysqli && !empty($cart)) {
    $product_ids = array_keys($cart);
    if (!empty($product_ids)) {
        $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
        $sql = "SELECT product_id, product_name, price, image, quantity FROM products WHERE product_id IN ($placeholders) AND status = 'active'";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param(str_repeat('i', count($product_ids)), ...$product_ids);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                if ($row['quantity'] >= $cart[$row['product_id']]['qty']) {
                    $products[$row['product_id']] = $row;
                    $total += $row['price'] * ($cart[$row['product_id']]['qty'] ?? 1);
                } else {
                    unset($_SESSION['cart'][$row['product_id']]);
                }
            }
            $stmt->close();
        }
    }
    $conn->close();
}
?>

<div class="my-4 container">
    <h3>Order Review</h3>
    <?php if (empty($cart) || empty($products)): ?>
        <div class="alert alert-info">Your cart is empty or contains unavailable items.</div>
        <a href="products.php" class="btn btn-primary">Continue Shopping</a>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart as $product_id => $item): ?>
                    <?php if (isset($products[$product_id])): ?>
                        <tr>
                            <td><?php echo e($products[$product_id]['product_name']); ?></td>
                            <td>₹<?php echo number_format($products[$product_id]['price'], 2); ?></td>
                            <td><?php echo e($item['qty']); ?></td>
                            <td>₹<?php echo number_format($products[$product_id]['price'] * $item['qty'], 2); ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                    <td>₹<?php echo number_format($total, 2); ?></td>
                </tr>
                <tr>
                    <td colspan="3" class="text-end"><strong>Shipping:</strong></td>
                    <td>₹<?php echo number_format($shipping_cost, 2); ?></td>
                </tr>
                <tr>
                    <td colspan="3" class="text-end"><strong>Total:</strong></td>
                    <td>₹<?php echo number_format($total + $shipping_cost, 2); ?></td>
                </tr>
            </tfoot>
        </table>
        <div class="text-end">
            <a href="cart.php" class="btn btn-secondary">Back to Cart</a>
            <a href="checkout.php" class="btn btn-primary">Proceed to Payment</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>