<?php
require_once 'includes/header.php';
require_once 'includes/sidebar.php';
require_once 'includes/db_connection.php';

// Get product_id from URL
$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: products.php');
    exit();
}

// Fetch product with brand, category, and subcategory
$sql = "SELECT p.*, b.brand_name, c.category_name, s.subcategory_name
        FROM products p
        LEFT JOIN brands b ON p.brand_id = b.brand_id
        LEFT JOIN categories c ON p.category_id = c.category_id
        LEFT JOIN subcategories s ON p.subcategory_id = s.subcategory_id
        WHERE p.product_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    echo "<div class='main-content container mt-4'>
            <div class='alert alert-danger fade-in animated-card'>Product not found.</div>
          </div>";
    require_once 'includes/footer.php';
    exit();
}

// Fetch product images
$images = [];
$sql_images = "SELECT image_path FROM product_images WHERE product_id = ?";
$stmt_images = mysqli_prepare($conn, $sql_images);
mysqli_stmt_bind_param($stmt_images, 'i', $id);
mysqli_stmt_execute($stmt_images);
$result_images = mysqli_stmt_get_result($stmt_images);
while ($row = mysqli_fetch_assoc($result_images)) {
    $images[] = $row['image_path'];
}
mysqli_stmt_close($stmt_images);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product: <?= htmlspecialchars($product['product_name']) ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
       <style>
        .animated-card {
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border-radius: 10px;
            overflow: hidden;
        }
        .animated-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        }
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .badge-status {
            padding: 6px 10px;
            border-radius: 20px;
            font-weight: 500;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(0,123,255,0.05);
            transform: scale(1.01);
        }
        .btn-animated {
            transition: all 0.3s ease;
            border-radius: 6px;
        }
        .btn-animated:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        .main-content {
            padding: 20px;
            margin-left: 250px;
            transition: all 0.3s;
        }
        
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
<div class="main-content container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4 fade-in">
        <h2>View Product</h2>
        <a href="products.php" class="btn btn-secondary btn-animated"><i class="fas fa-arrow-left"></i> Back to Products</a>
    </div>

    <div class="card mb-4 animated-card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <?php if (!empty($images)): ?>
                        <div id="productImageCarousel" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                <?php foreach ($images as $index => $image): ?>
                                    <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                        <img src="<?= htmlspecialchars($image) ?>" class="d-block w-100 rounded" alt="<?= htmlspecialchars($product['product_name']) ?>" onerror="this.src=''">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php if (count($images) > 1): ?>
                                <button class="carousel-control-prev" type="button" data-bs-target="#productImageCarousel" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Previous</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#productImageCarousel" data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Next</span>
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="bg-light d-flex align-items-center justify-content-center fade-in" style="height: 250px;">
                            <i class="fas fa-image fa-3x text-muted"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-8">
                    <h3 class="fade-in"><?= htmlspecialchars($product['product_name']) ?></h3>
                    <p><strong>SKU:</strong> <?= htmlspecialchars($product['sku']) ?></p>
                    <p><strong>Brand:</strong> <?= htmlspecialchars($product['brand_name'] ?? '-') ?></p>
                    <p><strong>Category:</strong> <?= htmlspecialchars($product['category_name'] ?? '-') ?></p>
                    <p><strong>Subcategory:</strong> <?= htmlspecialchars($product['subcategory_name'] ?? '-') ?></p>
                    <p><strong>Price:</strong> ₹<?= number_format($product['price'], 2) ?></p>
                    <p><strong>Cost Price:</strong> $<?= number_format($product['cost_price'], 2) ?></p>
                    <p><strong>Quantity:</strong> <?= $product['quantity'] ?></p>
                    <p><strong>Minimum Stock Level:</strong> <?= $product['min_stock_level'] ?></p>
                    <p><strong>Weight:</strong> <?= $product['weight'] ?> kg</p>
                    <p><strong>Dimensions:</strong> <?= htmlspecialchars($product['dimensions']) ?></p>
                    <p><strong>Status:</strong> 
                        <span class="badge badge-status <?= $product['status'] ? 'bg-success' : 'bg-danger' ?>">
                            <?= $product['status'] ? 'Active' : 'Inactive' ?>
                        </span>
                    </p>
                    <p><strong>Featured:</strong> 
                        <span class="badge badge-status <?= $product['featured'] ? 'bg-warning text-dark' : 'bg-secondary' ?>">
                            <?= $product['featured'] ? 'Yes' : 'No' ?>
                        </span>
                    </p>
                </div>
            </div>

            <?php if (!empty($product['description'])): ?>
                <hr>
                <h5 class="fade-in">Description:</h5>
                <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
            <?php endif; ?>

            <hr>
            <div class="d-flex justify-content-end">
                <a href="edit_product.php?id=<?= $product['product_id'] ?>" class="btn btn-warning btn-animated me-2">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="delete_product.php?id=<?= $product['product_id'] ?>" class="btn btn-danger btn-animated" onclick="return confirm('Are you sure you want to delete this product?');">
                    <i class="fas fa-trash"></i> Delete
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php require_once 'includes/footer.php'; ?>
</body>
</html>