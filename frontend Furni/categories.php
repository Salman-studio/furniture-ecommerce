<?php
// furniture/categories.php
$pageTitle = 'Categories';
require_once __DIR__ . '/includes/header.php';
$conn = require_once __DIR__ . '/includes/db_connection.php';

// Fetch categories from DB
$sql = "SELECT * FROM categories WHERE status = 'active' ORDER BY category_name ASC";
$result = mysqli_query($conn, $sql);
$categories = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<div class="container my-5">
  <h2 class="text-center mb-4">Browse Categories</h2>
  <div class="row g-4">
    <?php if (!empty($categories)): ?>
      <?php foreach ($categories as $cat): ?>
        <div class="col-md-3 col-sm-6">
          <a href="products.php?category_id=<?= urlencode($cat['category_id']); ?>" class="text-decoration-none">
            <div class="card shadow-sm h-100 text-center">
              <?php
              // --- FIX: Safer image path handling ---
              $imageFile = $cat['image'] ?? ''; // adjust this if your column is named differently
              $imagePath = 'uploads/categories/' . basename($imageFile);

              if (!empty($imageFile) && file_exists(__DIR__ . '/' . $imagePath)) {
                  $image = $imagePath;
              } else {
                  $image = 'assets/images/default.jpg';
              }

              // Debug (you can remove later)
              echo "<!-- Debug: Using image = {$image} -->";
              ?>
              <img src="<?= htmlspecialchars($image); ?>"
                   alt="<?= htmlspecialchars($cat['category_name']); ?>"
                   class="card-img-top"
                   style="height: 180px; object-fit: cover;">
              <div class="card-body">
                <h5 class="card-title mb-0"><?= htmlspecialchars($cat['category_name']); ?></h5>
                <?php if (!empty($cat['description'])): ?>
                  <p class="text-muted small mt-2"><?= htmlspecialchars($cat['description']); ?></p>
                <?php endif; ?>
              </div>
            </div>
          </a>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-center">No categories available.</p>
    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
