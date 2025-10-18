<?php
session_start();
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';

$pageTitle = 'Cart';
$cart = $_SESSION['cart'] ?? [];
$conn = include __DIR__ . '/includes/db_connection.php';

$total = 0;
$shipping_cost = 50;
$products = [];

if ($conn instanceof mysqli && !empty($cart)) {
    $product_ids = array_keys($cart);
    if (!empty($product_ids)) {
        $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
        $sql = "SELECT product_id, product_name, price, image FROM products WHERE product_id IN ($placeholders) AND status = 'active'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(str_repeat('i', count($product_ids)), ...$product_ids);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $products[$row['product_id']] = $row;
            $total += $row['price'] * ($cart[$row['product_id']]['qty'] ?? 1);
        }
        $stmt->close();
    }
    $conn->close();
}
?>

<div class="my-4 container">
    <h3>Your Cart</h3>
    <?php if (empty($cart)): ?>
        <div class="alert alert-info">Your cart is empty.</div>
        <a href="products.php" class="btn btn-primary">Continue Shopping</a>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th></th>
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
                            <td>
                                <button class="btn btn-sm btn-danger" onclick="ajaxCart.removeFromCart(<?php echo (int)$product_id; ?>)">Remove</button>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                    <td>₹<?php echo number_format($total, 2); ?></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="3" class="text-end"><strong>Shipping:</strong></td>
                    <td>₹<?php echo number_format($shipping_cost, 2); ?></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="3" class="text-end"><strong>Total:</strong></td>
                    <td>₹<?php echo number_format($total + $shipping_cost, 2); ?></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
        <div class="text-end">
            <a href="products.php" class="btn btn-secondary">Continue Shopping</a>
            <?php if (is_logged_in()): ?>
                <a href="order_review.php" class="btn btn-primary">Proceed to Checkout</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-primary">Login to Checkout</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<script>
const CSRF_TOKEN = '<?php echo csrf_token(); ?>';
const ajaxCart = {
    removeFromCart: function(product_id) {
        fetch('remove_item.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `product_id=${encodeURIComponent(product_id)}&csrf_token=${encodeURIComponent(CSRF_TOKEN)}`
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert(result.message);
                window.location.reload();
            } else {
                alert('Failed: ' + result.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred.');
        });
    }
};
</script>