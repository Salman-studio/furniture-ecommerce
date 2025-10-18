<?php
session_start();
$pageTitle = 'Product Details';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/db_connection.php';

// Get product ID from URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id <= 0) {
    echo "<div class='alert alert-danger'>Invalid Product ID.</div>";
    require_once __DIR__ . '/includes/footer.php';
    exit();
}

// Fetch product + images
$conn = require __DIR__ . '/includes/db_connection.php';
$product = null;
$images = [];

if ($conn instanceof mysqli) {
    // Product
    $stmt = $conn->prepare("SELECT product_id, product_name, price, description, image, quantity 
                            FROM products 
                            WHERE product_id = ? AND status = 'active' LIMIT 1");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();

    if ($product) {
        // Images
        $stmt = $conn->prepare("SELECT image_path FROM product_images WHERE product_id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $images[] = $row['image_path'];
        }
        $stmt->close();
    }
    $conn->close();
}

if (!$product) {
    echo "<div class='alert alert-warning'>Product not found.</div>";
    require_once __DIR__ . '/includes/footer.php';
    exit();
}
?>

<div class="my-4 row">
    <div class="col-md-6">
        <!-- Main Image -->
        <div class="mb-3">
            <img id="mainImage"
                 src="<?php echo htmlspecialchars($product['image'] ?? 'assets/images/product-placeholder.jpg'); ?>" 
                 alt="<?php echo htmlspecialchars($product['product_name']); ?>" 
                 class="img-fluid rounded border">
        </div>

        <!-- Thumbnails -->
        <?php if (!empty($images)): ?>
        <div class="d-flex flex-wrap gap-2">
            <?php foreach ($images as $img): ?>
                <img src="<?php echo htmlspecialchars($img); ?>"
                     alt="Additional image"
                     class="img-thumbnail"
                     style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;"
                     onclick="document.getElementById('mainImage').src=this.src;">
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <div class="col-md-6">
        <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
        <p class="price fs-4 text-primary">₹<?php echo number_format($product['price'], 2); ?></p>
        <p class="small-muted"><?php echo htmlspecialchars($product['description'] ?? 'High-quality materials, handcrafted.'); ?></p>

        <div class="mb-3">
            <label>Quantity</label>
            <input type="number" id="qty" value="1" min="1" max="<?php echo (int)$product['quantity']; ?>" 
                   class="form-control" style="width:120px;">
        </div>

        <button class="btn btn-primary-custom"
            onclick="ajaxCart.addToCart(<?php echo (int)$product['product_id']; ?>, document.getElementById('qty').value)"
            <?php echo ((int)$product['quantity'] <= 0) ? 'disabled' : ''; ?>>
            <?php echo ((int)$product['quantity'] <= 0) ? 'Out of Stock' : 'Add to Cart'; ?>
        </button>
    </div>
</div>

<script>
const CSRF_TOKEN = '<?php echo csrf_token(); ?>';

const ajaxCart = {
    addToCart: function(product_id, qty) {
        fetch('add_to_cart.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `product_id=${encodeURIComponent(product_id)}&qty=${encodeURIComponent(qty)}&csrf_token=${encodeURIComponent(CSRF_TOKEN)}`
        })
        .then(res => res.json())
        .then(result => {
            if (result.success) {
                alert(result.message);
            } else {
                alert('Failed: ' + result.message);
            }
        })
        .catch(err => {
            console.error('Error:', err);
            alert('An error occurred while adding the product to cart.');
        });
    }
};
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
