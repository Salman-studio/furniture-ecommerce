<?php
// furniture/wishlist.php - simple wishlist page (requires login)
require_once __DIR__ . '/includes/middleware/auth.php';
$pageTitle = 'Wishlist';
require_once __DIR__ . '/includes/header.php';
?>
<div class="my-4">
  <h3>Your Wishlist</h3>
  <p class="small-muted">No items yet. Browse <a href="products.php">products</a> to add to wishlist.</p>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
