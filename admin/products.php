<?php
require_once 'includes/header.php';
require_once 'includes/sidebar.php';
require_once 'includes/db_connection.php';

// Fetch products with brand, category, and primary image
$sql = "SELECT p.*, b.brand_name, c.category_name, 
        (SELECT image_path FROM product_images pi WHERE pi.product_id = p.product_id LIMIT 1) as primary_image
        FROM products p
        LEFT JOIN brands b ON p.brand_id = b.brand_id
        LEFT JOIN categories c ON p.category_id = c.category_id
        ORDER BY p.product_id DESC";
$result = mysqli_query($conn, $sql);

if (!$result) {
    error_log("Error fetching products: " . mysqli_error($conn), 3, __DIR__ . '/logs/db_errors.log');
    die("Error fetching products. Please try again.");
}

// Get product statistics
$stats_sql = "SELECT 
    COUNT(*) as total_products,
    COUNT(CASE WHEN quantity > 0 THEN 1 END) as in_stock,
    COUNT(CASE WHEN quantity = 0 THEN 1 END) as out_of_stock,
    COALESCE(SUM(price * quantity), 0) as total_value
    FROM products";
$stats_result = mysqli_query($conn, $stats_sql);
$stats = $stats_result ? mysqli_fetch_assoc($stats_result) : [];
?>

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
    .clickable {
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .clickable:hover {
        background-color: #f8f9fa;
        transform: scale(1.02);
    }
    .stat-card {
        transition: all 0.3s ease;
        border-radius: 10px;
        overflow: hidden;
        border: none;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.15);
    }
    .btn-animated {
        transition: all 0.3s ease;
        border-radius: 6px;
    }
    .btn-animated:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
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
    .table-hover tbody tr {
        transition: all 0.2s ease;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(0,123,255,0.05);
        transform: scale(1.01);
    }
    .action-btn {
        transition: all 0.2s ease;
        border-radius: 5px;
        margin: 0 2px;
    }
    .action-btn:hover {
        transform: scale(1.1);
    }
    .product-image {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 5px;
        transition: all 0.3s ease;
    }
    .product-image:hover {
        transform: scale(1.8);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        z-index: 10;
        position: relative;
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

<div class="main-content fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Product Management</h2>
        <a href="add_product.php" class="btn btn-primary btn-animated">
            <i class="fas fa-plus-circle mr-2"></i> Add New Product
        </a>
    </div>
    
    <?php if (mysqli_num_rows($result) == 0): ?>
        <div class="card animated-card">
            <div class="card-body text-center py-5">
                <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">No Products Found</h4>
                <p class="text-muted">Get started by adding your first product to the inventory.</p>
                <a href="add_product.php" class="btn btn-primary btn-animated mt-2">
                    <i class="fas fa-plus-circle mr-2"></i> Add Your First Product
                </a>
            </div>
        </div>
    <?php else: ?>
        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card stat-card bg-primary text-white text-center clickable" onclick="window.location='?filter=all'">
                    <div class="card-body">
                        <i class="fas fa-boxes fa-2x mb-2"></i>
                        <h6>Total Products</h6>
                        <h3><?= $stats['total_products'] ?? 0 ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stat-card bg-success text-white text-center clickable" onclick="window.location='?filter=in_stock'">
                    <div class="card-body">
                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                        <h6>In Stock</h6>
                        <h3><?= $stats['in_stock'] ?? 0 ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stat-card bg-danger text-white text-center clickable" onclick="window.location='?filter=out_of_stock'">
                    <div class="card-body">
                        <i class="fas fa-exclamation-circle fa-2x mb-2"></i>
                        <h6>Out of Stock</h6>
                        <h3><?= $stats['out_of_stock'] ?? 0 ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stat-card bg-info text-white text-center clickable" onclick="window.location='?filter=all'">
                    <div class="card-body">
                        <i class="fas fa-rupee-sign fa-2x mb-2"></i>
                        <h6>Total Value</h6>
                        <h3>₹<?= number_format($stats['total_value'] ?? 0, 2) ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card animated-card">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="fas fa-list mr-2"></i> All Products</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped datatable">
                        <thead class="thead-dark">
                            <tr>
                                <th>Image</th>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Brand</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($prod = mysqli_fetch_assoc($result)): 
                                $stock_status = $prod['quantity'] > 0 ? 'In Stock' : 'Out of Stock';
                                $status_class = $prod['quantity'] > 0 ? 'success' : 'danger';
                            ?>
                            <tr class="clickable" onclick="window.location='view_product.php?id=<?= $prod['product_id'] ?>'">
                                <td>
                                    <?php if (!empty($prod['primary_image'])): ?>
                                        <img src="<?= htmlspecialchars($prod['primary_image']) ?>" class="product-image" alt="<?= htmlspecialchars($prod['product_name']) ?>">
                                    <?php else: ?>
                                        <div class="product-image bg-light d-flex align-items-center justify-content-center">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><?= $prod['product_id'] ?></td>
                                <td>
                                    <div class="font-weight-bold"><?= htmlspecialchars($prod['product_name']) ?></div>
                                    <?php if (!empty($prod['description'])): ?>
                                        <small class="text-muted"><?= substr(htmlspecialchars($prod['description']), 0, 50) ?>...</small>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($prod['brand_name'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($prod['category_name'] ?? 'N/A') ?></td>
                                <td class="font-weight-bold">₹<?= number_format($prod['price'], 2) ?></td>
                                <td><?= $prod['quantity'] ?></td>
                                <td>
                                    <span class="badge badge-status badge-<?= $status_class ?>">
                                        <?= $stock_status ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="view_product.php?id=<?= $prod['product_id'] ?>" class="btn btn-sm btn-info action-btn" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="edit_product.php?id=<?= $prod['product_id'] ?>" class="btn btn-sm btn-warning action-btn" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="delete_product.php?id=<?= $prod['product_id'] ?>" class="btn btn-sm btn-danger action-btn" onclick="return confirm('Are you sure you want to delete this product?');" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    $(document).ready(function() {
        $('.datatable').DataTable({
            responsive: true,
            ordering: true,
            searching: true,
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            dom: '<"row"<"col-md-6"l><"col-md-6"f>>rtip',
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search products...",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ products",
                infoEmpty: "Showing 0 to 0 of 0 products",
                infoFiltered: "(filtered from _MAX_ total products)"
            }
        });
    });
</script>

<?php require_once 'includes/footer.php'; ?>