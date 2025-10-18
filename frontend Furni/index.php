<?php
// furniture/index.php
$pageTitle = 'Home';
require_once __DIR__ . '/includes/header.php';

// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection - using MySQLi
try {
    $conn = require_once __DIR__ . '/includes/db_connection.php'; // Returns $conn (MySQLi)

    // Fetch categories
    $categoriesResult = mysqli_query($conn, "SELECT * FROM categories WHERE status = 1 LIMIT 6");
    if ($categoriesResult) {
        $categories = mysqli_fetch_all($categoriesResult, MYSQLI_ASSOC);
        mysqli_free_result($categoriesResult);
    } else {
        $categories = [];
        error_log("Error fetching categories: " . mysqli_error($conn));
    }

    // Fetch featured products
    $productsResult = mysqli_query($conn, "SELECT * FROM products WHERE featured = 1 AND status = 1 LIMIT 8");
    if ($productsResult) {
        $products = mysqli_fetch_all($productsResult, MYSQLI_ASSOC);
        mysqli_free_result($productsResult);
    } else {
        $products = [];
        error_log("Error fetching products: " . mysqli_error($conn));
    }
} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    die("Database error: " . htmlspecialchars($e->getMessage()) . ". Please check the error log for details.");
}
?>

<!-- Inline CSS for Animations and Responsive Design -->
<style>
/* Fade-in animation for sections */
section {
    opacity: 0;
    transform: translateY(20px);
    animation: fadeInUp 0.6s ease-out forwards;
}
section:nth-child(2) { animation-delay: 0.2s; }
section:nth-child(3) { animation-delay: 0.4s; }
section:nth-child(4) { animation-delay: 0.6s; }
section:nth-child(5) { animation-delay: 0.8s; }
section:nth-child(6) { animation-delay: 1s; }

@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Hover animation for category cards */
.category-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.category-card:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

/* Hover animation for product cards */
.product-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
}

/* Hover effect for buttons */
.btn-primary-custom {
    transition: background-color 0.3s ease, transform 0.2s ease;
}
.btn-primary-custom:hover {
    background-color: #005f73;
    transform: scale(1.1);
}

/* Inspiration card animation */
.inspiration-card {
    opacity: 0;
    transform: translateX(-20px);
    animation: slideIn 0.5s ease-out forwards;
}
.inspiration-card:nth-child(2) { animation-delay: 0.2s; }
.inspiration-card:nth-child(3) { animation-delay: 0.4s; }

@keyframes slideIn {
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Responsive Design */
@media (max-width: 768px) {
    /* Hero Section */
    .hero {
        height: 350px !important;
    }
    .hero h1 {
        font-size: 1.8rem !important;
    }
    .hero p {
        font-size: 1rem !important;
    }
    .hero .btn {
        font-size: 0.9rem;
        padding: 8px 16px;
    }

    /* Categories */
    .category-card {
        padding: 1.5rem;
    }
    .category-card img {
        height: 80px !important;
    }
    .category-card h6 {
        font-size: 0.9rem;
    }

    /* Products */
    .product-card .product-img {
        height: 150px !important;
    }
    .product-card h5 {
        font-size: 1rem;
    }
    .product-card p {
        font-size: 0.85rem;
    }
    .product-card .price {
        font-size: 1rem;
    }
    .product-card .btn {
        font-size: 0.85rem;
        padding: 6px 12px;
    }

    /* Why Choose Us */
    .why-choose-us .fs-2 {
        font-size: 1.5rem !important;
    }
    .why-choose-us h5 {
        font-size: 1rem;
    }
    .why-choose-us p {
        font-size: 0.85rem;
    }

    /* Design Inspiration */
    .inspiration-card img {
        height: 120px !important;
    }
    .inspiration-card h5 {
        font-size: 1rem;
    }
    .inspiration-card p {
        font-size: 0.85rem;
    }
    .inspiration-card .btn {
        font-size: 0.85rem;
        padding: 6px 12px;
    }

    /* Repair & Service */
    .repair-service h3 {
        font-size: 1.5rem;
    }
    .repair-service p {
        font-size: 1rem;
    }
    .repair-service .btn {
        font-size: 0.9rem;
        padding: 8px 16px;
    }

    /* Disable hover animations on mobile */
    .category-card:hover, .product-card:hover, .btn-primary-custom:hover {
        transform: none;
        box-shadow: none;
    }
}

@media (max-width: 576px) {
    /* Stack categories and products in single column */
    .col-6, .col-md-2, .col-md-3, .col-md-4 {
        flex: 0 0 100%;
        max-width: 100%;
    }
}
</style>

<!-- Hero/Banner Section -->
<section class="hero rounded position-relative overflow-hidden" style="background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('assets/images/luxury-furniture-banner.jpg'); background-size: cover; background-position: center; height: 500px;">
  <div class="text-center text-white position-absolute top-50 start-50 translate-middle">
    <h1 class="display-4 fw-bold">Discover Luxury Handcrafted Furniture</h1>
    <p class="lead mt-3">Elevate Your Home with Timeless Designs & Exclusive Offers</p>
    <div class="mt-4">
      <a href="products.php" class="btn btn-primary-custom btn-lg me-2">Shop Now</a>
      <a href="custom_work.php" class="btn btn-outline-light">Explore Custom Designs</a>
    </div>
  </div>
</section>

<!-- Featured Categories -->
<section class="my-5">
  <h2 class="text-center mb-4">Explore Our Categories</h2>
  <div class="row g-3">
    <?php if (!empty($categories)): ?>
      <?php foreach ($categories as $category): ?>
        <div class="col-6 col-md-2">
          <a href="products.php?category_id=<?php echo urlencode($category['category_id']); ?>" class="text-decoration-none">
            <div class="category-card text-center p-3 border rounded h-100">
              <?php
              // Build correct image path
              $imageFile = $category['image'] ?? '';
              $imagePath = 'uploads/categories/' . basename($imageFile);

              if (!empty($imageFile) && file_exists(__DIR__ . '/' . $imagePath)) {
                  $image = htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8');
              } else {
                  $image = 'assets/images/default.jpg';
              }
              ?>
              <img src="<?php echo $image; ?>" 
                   alt="<?php echo htmlspecialchars($category['category_name'] ?? 'Category', ENT_QUOTES, 'UTF-8'); ?>" 
                   class="img-fluid mb-2" 
                   style="height: 100px; object-fit: cover; width: 100%;">
              <h6 class="mb-0"><?php echo htmlspecialchars($category['category_name'], ENT_QUOTES, 'UTF-8'); ?></h6>
            </div>
          </a>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-center">No categories available at the moment.</p>
    <?php endif; ?>
  </div>
</section>

<!-- Featured Products / New Arrivals -->
<section class="my-5">
  <h2 class="text-center mb-4">New Arrivals</h2>
  <div class="row g-3">
    <?php if (!empty($products)): ?>
      <?php foreach ($products as $product): ?>
        <div class="col-6 col-md-3">
          <div class="product-card border rounded h-100">
            <?php
            // Build correct product image path
            $imageFile = $product['image'] ?? '';
            $imagePath = 'uploads/products/' . basename($imageFile);

            if (!empty($imageFile) && file_exists(__DIR__ . '/' . $imagePath)) {
                $image = htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8');
            } else {
                $image = 'assets/images/default.jpg';
            }
            ?>
            <a href="product_details.php?product_id=<?php echo urlencode($product['product_id']); ?>" class="text-decoration-none">
              <div class="product-img" style="background-image: url('<?php echo $image; ?>'); height: 200px; background-size: cover; background-position: center;"></div>
            </a>
            <div class="product-body p-3">
              <a href="product_details.php?product_id=<?php echo urlencode($product['product_id']); ?>" class="text-decoration-none text-dark">
                <h5><?php echo htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8'); ?></h5>
              </a>
              <p class="small text-muted"><?php echo htmlspecialchars($product['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
              <div class="d-flex justify-content-between align-items-center mt-2">
                <div class="price">₹<?php echo number_format($product['price'], 2); ?></div>
                <button class="btn btn-primary-custom" onclick="ajaxCart.addToCart({product_id: <?php echo $product['product_id']; ?>, qty: 1}, function(){ alert('Added to cart'); })">Add to Cart</button>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-center">No products available at the moment.</p>
    <?php endif; ?>
  </div>
</section>

<!-- Design Inspiration -->
<section class="my-5 bg-light py-5">
  <h2 class="text-center mb-4">Design Inspiration</h2>
  <div class="row g-3">
    <div class="col-12 col-md-4">
      <div class="inspiration-card border rounded p-4 text-center">
        <img src="assets/images/inspiration1.jpg" 
             alt="Modern Living Room" 
             class="img-fluid mb-3" 
             style="height: 150px; object-fit: cover; width: 100%;">
        <h5>Modern Living Room</h5>
        <p class="text-muted">Create a sleek and stylish living space with our minimalist designs.</p>
        <a href="inspiration.php?design=modern" class="btn btn-primary-custom mt-2">Get Inspired</a>
      </div>
    </div>
    <div class="col-12 col-md-4">
      <div class="inspiration-card border rounded p-4 text-center">
        <img src="assets/images/inspiration2.jpg" 
             alt="Cozy Bedroom" 
             class="img-fluid mb-3" 
             style="height: 150px; object-fit: cover; width: 100%;">
        <h5>Cozy Bedroom</h5>
        <p class="text-muted">Transform your bedroom into a warm and inviting retreat.</p>
        <a href="inspiration.php?design=cozy" class="btn btn-primary-custom mt-2">Get Inspired</a>
      </div>
    </div>
    <div class="col-12 col-md-4">
      <div class="inspiration-card border rounded p-4 text-center">
        <img src="assets/images/inspiration3.jpg" 
             alt="Elegant Dining" 
             class="img-fluid mb-3" 
             style="height: 150px; object-fit: cover; width: 100%;">
        <h5>Elegant Dining</h5>
        <p class="text-muted">Elevate your dining experience with our luxurious furniture sets.</p>
        <a href="inspiration.php?design=dining" class="btn btn-primary-custom mt-2">Get Inspired</a>
      </div>
    </div>
  </div>
</section>

<!-- Why Choose Us -->
<section class="my-5 bg-light py-5 why-choose-us">
  <h2 class="text-center mb-4">Why Choose Us?</h2>
  <div class="row g-3 text-center">
    <div class="col-12 col-md-4">
      <div class="p-3">
        <i class="bi bi-award fs-2 text-primary"></i>
        <h5 class="mt-2">Premium Quality</h5>
        <p class="text-muted">Handcrafted with the finest materials for lasting durability.</p>
      </div>
    </div>
    <div class="col-12 col-md-4">
      <div class="p-3">
        <i class="bi bi-tools fs-2 text-primary"></i>
        <h5 class="mt-2">Custom Designs</h5>
        <p class="text-muted">Tailor-made furniture to match your unique style.</p>
      </div>
    </div>
    <div class="col-12 col-md-4">
      <div class="p-3">
        <i class="bi bi-truck fs-2 text-primary"></i>
        <h5 class="mt-2">Free Delivery</h5>
        <p class="text-muted">Nationwide shipping with hassle-free delivery.</p>
      </div>
    </div>
  </div>
</section>

<!-- Repair & Service -->
<section class="my-5 repair-service">
  <div class="text-center bg-secondary-subtle p-5 rounded">
    <h3>We Offer Repair & Service</h3>
    <p class="lead mt-3">Extend the life of your furniture with our expert repair and reupholstery services.</p>
    <a href="services.php" class="btn btn-primary-custom mt-3">Learn More</a>
  </div>
</section>

<?php
require_once __DIR__ . '/includes/footer.php';
mysqli_close($conn);
?>