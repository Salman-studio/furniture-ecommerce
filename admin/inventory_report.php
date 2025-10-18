<?php
require_once 'includes/header.php';
require_once 'includes/sidebar.php';
require_once 'includes/db_connection.php';

// Check if products table exists
$table_check = mysqli_query($conn, "SHOW TABLES LIKE 'products'");
$has_products = (mysqli_num_rows($table_check) > 0);

if ($has_products) {
    // Check actual column names in products table
    $columns_check = mysqli_query($conn, "SHOW COLUMNS FROM products");
    $product_columns = [];
    while ($col = mysqli_fetch_assoc($columns_check)) {
        $product_columns[] = $col['Field'];
    }
    
    // Determine stock column name
    $stock_column = in_array('stock', $product_columns) ? 'stock' : 
                   (in_array('quantity', $product_columns) ? 'quantity' : 
                   (in_array('inventory', $product_columns) ? 'inventory' : 'stock'));
    
    // Determine product name column
    $name_column = in_array('product_name', $product_columns) ? 'product_name' :
                  (in_array('name', $product_columns) ? 'name' :
                  (in_array('title', $product_columns) ? 'title' : 'product_name'));
    
    // Get low inventory threshold from settings or use default
    $low_stock_threshold = 5;
    
    // Fetch products with low inventory
    $sql = "SELECT p.*, b.brand_name 
            FROM products p 
            LEFT JOIN brands b ON p.brand_id = b.brand_id
            WHERE p.$stock_column < $low_stock_threshold
            ORDER BY p.$stock_column ASC";
    
    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        $error = "Error fetching inventory data: " . mysqli_error($conn);
    }
    
    // Get inventory summary
    $summary_sql = "SELECT 
                    COUNT(*) as total_products,
                    SUM($stock_column) as total_inventory,
                    COUNT(CASE WHEN $stock_column < $low_stock_threshold THEN 1 END) as low_stock_count,
                    COUNT(CASE WHEN $stock_column = 0 THEN 1 END) as out_of_stock_count
                    FROM products";
    $summary_result = mysqli_query($conn, $summary_sql);
    $summary = $summary_result ? mysqli_fetch_assoc($summary_result) : [];
    
} else {
    $error = "Products table not found in database.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --success-color: #4cc9f0;
            --warning-color: #f72585;
            --light-color: #f8f9fa;
            --dark-color: #212529;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f7fb;
            color: #333;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
        }
        
        .main-content {
            padding: 20px;
            margin-left: 250px;
            transition: all 0.3s ease;
        }
        
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
        }
        
        .animated {
            animation-duration: 1s;
            animation-fill-mode: both;
        }
        
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translate3d(0, -20px, 0);
            }
            to {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }
        
        .fadeInDown {
            animation-name: fadeInDown;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translate3d(0, 20px, 0);
            }
            to {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }
        
        .fadeInUp {
            animation-name: fadeInUp;
        }
        
        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
        }
        
        .pulse {
            animation-name: pulse;
            animation-duration: 2s;
            animation-iteration-count: infinite;
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            overflow: hidden;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }
        
        .card-title {
            font-weight: 600;
            color: #333;
        }
        
        .summary-card {
            color: white;
            border-radius: 12px;
            padding: 20px;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .summary-card:hover {
            transform: translateY(-5px);
        }
        
        .summary-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.1), transparent);
            transform: rotate(45deg);
            transition: all 0.5s ease;
        }
        
        .summary-card:hover::before {
            animation: shine 1.5s ease;
        }
        
        @keyframes shine {
            0% {
                transform: translateX(-100%) rotate(45deg);
            }
            100% {
                transform: translateX(100%) rotate(45deg);
            }
        }
        
        .summary-card i {
            font-size: 2.5rem;
            opacity: 0.8;
            position: absolute;
            right: 20px;
            bottom: 20px;
        }
        
        .bg-primary {
            background: linear-gradient(135deg, #4361ee, #3a0ca3) !important;
        }
        
        .bg-info {
            background: linear-gradient(135deg, #4cc9f0, #4895ef) !important;
        }
        
        .bg-warning {
            background: linear-gradient(135deg, #f9a826, #ff8c00) !important;
        }
        
        .bg-danger {
            background: linear-gradient(135deg, #f72585, #b5179e) !important;
        }
        
        .table-hover tbody tr {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(67, 97, 238, 0.05);
            transform: scale(1.01);
        }
        
        .alert-card {
            border-left: 5px solid;
        }
        
        .alert-warning {
            border-left-color: #f9a826;
        }
        
        .alert-danger {
            border-left-color: #f72585;
        }
        
        .badge {
            border-radius: 20px;
            padding: 6px 12px;
            font-weight: 500;
        }
        
        .btn-action {
            border-radius: 50px;
            padding: 8px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .inventory-bar {
            height: 8px;
            border-radius: 4px;
            background: #e9ecef;
            overflow: hidden;
            margin-top: 5px;
        }
        
        .inventory-progress {
            height: 100%;
            border-radius: 4px;
            transition: width 1s ease-in-out;
        }
        
        .low-stock {
            background: linear-gradient(to right, #ff8c00, #ff6b00);
        }
        
        .out-of-stock {
            background: linear-gradient(to right, #f72585, #b5179e);
        }
        
        .healthy-stock {
            background: linear-gradient(to right, #4cc9f0, #4895ef);
        }
    </style>
</head>
<body>
<div class="main-content">
    <div class="page-header">
        <h2 class="animated fadeInDown">Inventory Management</h2>
        <div class="export-buttons">
            <button class="btn btn-outline-primary btn-action">
                <i class="fas fa-download me-2"></i>Export Report
            </button>
            <button class="btn btn-outline-success btn-action">
                <i class="fas fa-bell me-2"></i>Set Alerts
            </button>
        </div>
    </div>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger animated fadeInUp"><?= $error ?></div>
    <?php else: ?>
        <!-- Inventory Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="summary-card bg-primary animated fadeInUp" style="animation-delay: 0.1s;" onclick="window.location.href='products.php'">
                    <div class="card-body">
                        <h5 class="card-title">Total Products</h5>
                        <h3><?= number_format($summary['total_products'] ?? 0) ?></h3>
                        <p class="card-text">In catalog</p>
                        <i class="fas fa-boxes"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="summary-card bg-info animated fadeInUp" style="animation-delay: 0.2s;" onclick="window.location.href='inventory.php'">
                    <div class="card-body">
                        <h5 class="card-title">Total Inventory</h5>
                        <h3><?= number_format($summary['total_inventory'] ?? 0) ?></h3>
                        <p class="card-text">Units in stock</p>
                        <i class="fas fa-warehouse"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="summary-card bg-warning animated fadeInUp" style="animation-delay: 0.3s;" onclick="window.location.href='#low-stock'">
                    <div class="card-body">
                        <h5 class="card-title">Low Stock</h5>
                        <h3><?= number_format($summary['low_stock_count'] ?? 0) ?></h3>
                        <p class="card-text">Need restocking</p>
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="summary-card bg-danger animated fadeInUp" style="animation-delay: 0.4s;" onclick="window.location.href='#out-of-stock'">
                    <div class="card-body">
                        <h5 class="card-title">Out of Stock</h5>
                        <h3><?= number_format($summary['out_of_stock_count'] ?? 0) ?></h3>
                        <p class="card-text">Need immediate attention</p>
                        <i class="fas fa-times-circle"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Stock Alert Section -->
        <div class="card animated fadeInUp" style="animation-delay: 0.5s;" id="low-stock">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fa fa-exclamation-triangle text-warning"></i> Low Inventory Alert
                    <span class="badge bg-warning ms-2"><?= mysqli_num_rows($result) ?> items</span>
                </h5>
                <div>
                    <button class="btn btn-sm btn-outline-warning me-1" data-bs-toggle="modal" data-bs-target="#settingsModal">
                        <i class="fas fa-cog"></i> Settings
                    </button>
                    <button class="btn btn-sm btn-outline-primary" onclick="window.location.href='purchase_orders.php'">
                        <i class="fas fa-cart-plus"></i> Order Supplies
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Product ID</th>
                                    <th>Product Name</th>
                                    <th>Brand</th>
                                    <th>Current Stock</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($prod = mysqli_fetch_assoc($result)): 
                                    $stock = $prod[$stock_column] ?? $prod['stock'] ?? 0;
                                    $is_out_of_stock = $stock == 0;
                                    $is_low_stock = $stock > 0 && $stock < $low_stock_threshold;
                                    $stock_percentage = min(100, ($stock / $low_stock_threshold) * 100);
                                ?>
                                <tr onclick="window.location.href='edit_product.php?id=<?= $prod['product_id'] ?? $prod['id'] ?>'" 
                                    class="<?= $is_out_of_stock ? 'alert-danger' : ($is_low_stock ? 'alert-warning' : '') ?>">
                                    <td><strong>#<?= $prod['product_id'] ?? $prod['id'] ?></strong></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if (isset($prod['image']) && $prod['image']): ?>
                                            <img src="<?= $prod['image'] ?>" alt="Product Image" class="rounded me-3" width="40" height="40">
                                            <?php else: ?>
                                            <div class="bg-secondary rounded me-3 d-flex align-items-center justify-content-center" width="40" height="40">
                                                <i class="fas fa-box text-white"></i>
                                            </div>
                                            <?php endif; ?>
                                            <div>
                                                <?= htmlspecialchars($prod[$name_column] ?? $prod['product_name'] ?? 'N/A') ?>
                                                <?php if ($is_out_of_stock): ?>
                                                    <span class="badge bg-danger ms-2">Out of Stock</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($prod['brand_name'] ?? 'No Brand') ?></td>
                                    <td>
                                        <div>
                                            <span class="font-weight-bold <?= $is_out_of_stock ? 'text-danger' : ($is_low_stock ? 'text-warning' : 'text-success') ?>">
                                                <?= $stock ?> / <?= $low_stock_threshold ?>
                                            </span>
                                            <div class="inventory-bar">
                                                <div class="inventory-progress <?= $is_out_of_stock ? 'out-of-stock' : ($is_low_stock ? 'low-stock' : 'healthy-stock') ?>" 
                                                     style="width: <?= $stock_percentage ?>%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>₹<?= number_format($prod['price'] ?? 0, 2) ?></td>
                                    <td>
                                        <?php if ($is_out_of_stock): ?>
                                            <span class="badge bg-danger">Out of Stock</span>
                                        <?php elseif ($is_low_stock): ?>
                                            <span class="badge bg-warning">Low Stock</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">In Stock</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" 
                                                onclick="event.stopPropagation(); window.location.href='edit_product.php?id=<?= $prod['product_id'] ?? $prod['id'] ?>'"
                                                title="Restock Product">
                                            <i class="fa fa-edit"></i> Restock
                                        </button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <h5 class="text-success">Great news!</h5>
                        <p class="text-muted">No products are low on inventory.</p>
                        <button class="btn btn-outline-primary mt-2" onclick="window.location.href='products.php'">
                            <i class="fas fa-box me-1"></i> View All Products
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card animated fadeInUp" style="animation-delay: 0.6s;">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            <a href="products.php" class="btn btn-outline-primary btn-action">
                                <i class="fa fa-box me-2"></i> View All Products
                            </a>
                            <a href="add_product.php" class="btn btn-outline-success btn-action">
                                <i class="fa fa-plus me-2"></i> Add New Product
                            </a>
                            <a href="purchase_orders.php" class="btn btn-outline-info btn-action">
                                <i class="fa fa-shopping-cart me-2"></i> Create Purchase Order
                            </a>
                            <button class="btn btn-outline-secondary btn-action" onclick="window.print()">
                                <i class="fa fa-print me-2"></i> Print Report
                            </button>
                            <button class="btn btn-outline-warning btn-action" data-bs-toggle="modal" data-bs-target="#settingsModal">
                                <i class="fa fa-cog me-2"></i> Alert Settings
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?php endif; ?>
</div>

<!-- Settings Modal -->
<div class="modal fade" id="settingsModal" tabindex="-1" aria-labelledby="settingsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="settingsModalLabel">Inventory Alert Settings</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="lowStockThreshold" class="form-label">Low Stock Threshold</label>
                        <input type="number" class="form-control" id="lowStockThreshold" value="<?= $low_stock_threshold ?>" min="1" max="100">
                        <div class="form-text">Products with inventory below this value will be flagged as low stock.</div>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="emailAlerts" checked>
                        <label class="form-check-label" for="emailAlerts">Enable email alerts for low stock</label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alert Frequency</label>
                        <select class="form-select">
                            <option value="daily">Daily</option>
                            <option value="weekly" selected>Weekly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS and Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add animation to summary cards on hover
        document.querySelectorAll('.summary-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.classList.add('pulse');
            });
            
            card.addEventListener('mouseleave', function() {
                this.classList.remove('pulse');
            });
        });
        
        // Animate progress bars
        setTimeout(function() {
            document.querySelectorAll('.inventory-progress').forEach(progress => {
                progress.style.width = progress.style.width;
            });
        }, 500);
    });
</script>
</body>
</html>
<?php require_once 'includes/footer.php'; ?>