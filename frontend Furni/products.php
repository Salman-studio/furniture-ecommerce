<?php
session_start();
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/db_connection.php';
require_once __DIR__ . '/includes/header.php';

$pageTitle = 'Products';
$products = [];

// Get category_id from URL if available
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

if ($conn instanceof mysqli) {
    // Base query
   $sql = "SELECT product_id, product_name, price, quantity, image 
        FROM products 
        WHERE status = 'active' AND quantity > 0";


    // Add category filter if requested
    if ($category_id > 0) {
        $sql .= " AND category_id = ?";
    }

    $sql .= " ORDER BY created_at DESC";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        if ($category_id > 0) {
            $stmt->bind_param("i", $category_id);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $row['images'] = []; // prepare array for images
            $products[$row['product_id']] = $row;
        }

        $stmt->close();

        // Fetch product images in one query
        // Fetch product images directly from products table
foreach ($products as $pid => $row) {
    if (!empty($row['image'])) { // adjust column name if needed
        $products[$pid]['images'][] = $row['image'];
    }
}

        
    } else {
        error_log("Query preparation failed: " . $conn->error);
        echo "<div class='alert alert-warning'>Failed to fetch products. Please try again later.</div>";
    }
} else {
    echo "<div class='alert alert-warning'>Unable to connect to the database. Please try again later.</div>";
}
?>

<div class="my-4 container">
    <h3>
        <?php 
        echo $category_id > 0 
            ? "Products in " . htmlspecialchars(getCategoryName($conn, $category_id)) 
            : "All Products";
        ?>
    </h3>

    <div class="row g-3">
        <?php if (empty($products)): ?>
            <div class="col-12">
                <div class="alert alert-info">No products available at the moment.</div>
            </div>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
                <div class="col-md-3">
                    <a href="product_details.php?id=<?php echo (int)$product['product_id']; ?>" class="text-decoration-none">
                        <div class="card h-100">

                            <?php if (!empty($product['images'])): ?>
                                <!-- Bootstrap Carousel for multiple images -->
                                <div id="carousel-<?php echo $product['product_id']; ?>" class="carousel slide" data-bs-ride="carousel">
                                    <div class="carousel-inner">
                                        <?php foreach ($product['images'] as $index => $img): ?>
                                            <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                                <img src="<?php echo htmlspecialchars('uploads/' . $img); ?>" 
                                                     class="d-block w-100 card-img-top"
                                                     alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php if (count($product['images']) > 1): ?>
                                        <button class="carousel-control-prev" type="button" data-bs-target="#carousel-<?php echo $product['product_id']; ?>" data-bs-slide="prev">
                                            <span class="carousel-control-prev-icon"></span>
                                        </button>
                                        <button class="carousel-control-next" type="button" data-bs-target="#carousel-<?php echo $product['product_id']; ?>" data-bs-slide="next">
                                            <span class="carousel-control-next-icon"></span>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <!-- Fallback image -->
                                <img src="assets/images/product-placeholder.jpg" class="card-img-top" alt="No image available">
                            <?php endif; ?>

                            <div class="card-body">
                                <h6 class="card-title text-dark">
                                    <?php echo htmlspecialchars($product['product_name']); ?>
                                </h6>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <div class="fw-bold text-primary">₹<?php echo number_format($product['price'] ?? 0, 2); ?></div>
                                    <button class="btn btn-sm btn-primary" 
                                            onclick="ajaxCart.addToCart(<?php echo (int)$product['product_id']; ?>, 1)"
                                            <?php echo $product['quantity'] <= 0 ? 'disabled' : ''; ?>>
                                        Add
                                    </button>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<script>
const CSRF_TOKEN = '<?php echo csrf_token(); ?>';

const ajaxCart = {
    addToCart: function(product_id, qty) {
        fetch('add_to_cart.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `product_id=${encodeURIComponent(product_id)}&qty=${encodeURIComponent(qty)}&csrf_token=${encodeURIComponent(CSRF_TOKEN)}`
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert(result.message);
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

<?php
// Helper function to get category name by ID
function getCategoryName($conn, $category_id) {
    $category_id = (int)$category_id;
    $sql = "SELECT category_name FROM categories WHERE category_id = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $row = $result->fetch_assoc()) {
            $stmt->close();
            return $row['category_name'];
        }
        $stmt->close();
    }
    return "Category";
}
?>
