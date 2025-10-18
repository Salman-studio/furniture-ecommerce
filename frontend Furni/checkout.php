<?php
session_start();
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/middleware/auth.php';
require_once __DIR__ . '/includes/header.php';

$pageTitle = 'Checkout';
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
    <h3>Checkout</h3>
    <?php if (empty($cart) || empty($products)): ?>
        <div class="alert alert-info">Your cart is empty or contains unavailable items.</div>
        <a href="products.php" class="btn btn-primary">Continue Shopping</a>
    <?php else: ?>
        <div class="row">
            <div class="col-md-8">
                <h5>Order Summary</h5>
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
            </div>
            <div class="col-md-4">
                <h5>Shipping & Payment</h5>
                <form id="paymentForm" method="POST" action="payment.php">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full Name</label>
                        <input type="text" id="full_name" name="full_name" class="form-control" value="<?php echo e($_SESSION['checkout_data']['full_name'] ?? ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="text" id="phone" name="phone" class="form-control" value="<?php echo e($_SESSION['checkout_data']['phone'] ?? ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Shipping Address</label>
                        <textarea id="address" name="address" class="form-control" required><?php echo e($_SESSION['checkout_data']['address'] ?? ''); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="delivery_preference" class="form-label">Delivery Preference</label>
                        <select id="delivery_preference" name="delivery_preference" class="form-control" required>
                            <option value="standard" <?php echo ($_SESSION['checkout_data']['delivery_preference'] ?? '') === 'standard' ? 'selected' : ''; ?>>Standard Shipping (3-5 days)</option>
                            <option value="express" <?php echo ($_SESSION['checkout_data']['delivery_preference'] ?? '') === 'express' ? 'selected' : ''; ?>>Express Shipping (1-2 days)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" id="same_as_shipping" name="same_as_shipping" class="form-check-input" checked>
                            <label for="same_as_shipping" class="form-check-label">Use shipping address for billing</label>
                        </div>
                    </div>
                    <div id="billing_fields" style="display: none;">
                        <div class="mb-3">
                            <label for="billing_address" class="form-label">Billing Address</label>
                            <textarea id="billing_address" name="billing_address" class="form-control"><?php echo e($_SESSION['checkout_data']['billing_address'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Payment Method</label>
                        <select id="payment_method" name="payment_method" class="form-control" required>
                            <option value="Card">Card</option>
                            <option value="UPI">UPI</option>
                            <option value="COD">Cash on Delivery</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Order Notes (Optional)</label>
                        <textarea id="notes" name="notes" class="form-control"><?php echo e($_SESSION['checkout_data']['notes'] ?? ''); ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Place Order</button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<script>
document.getElementById('same_as_shipping').addEventListener('change', function() {
    document.getElementById('billing_fields').style.display = this.checked ? 'none' : 'block';
});
</script>