<?php
require_once 'includes/header.php';
require_once 'includes/sidebar.php';
require_once 'includes/db_connection.php';

// Check if orders table exists
$table_check = mysqli_query($conn, "SHOW TABLES LIKE 'orders'");
$has_orders = (mysqli_num_rows($table_check) > 0);

if ($has_orders) {
    // Monthly sales totals using your actual column names
    $sql = "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, 
                   SUM(final_amount) as total_sales,
                   COUNT(*) as order_count
            FROM orders 
            WHERE order_status IN ('Completed', 'completed', 'delivered', 'shipped')
            GROUP BY month 
            ORDER BY month DESC 
            LIMIT 12";
    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        $error = "Error fetching sales data: " . mysqli_error($conn);
    }

    // Get total sales overall
    $total_sales_sql = "SELECT SUM(final_amount) as grand_total FROM orders WHERE order_status IN ('Completed', 'completed', 'delivered', 'shipped')";
    $total_sales_result = mysqli_query($conn, $total_sales_sql);
    $total_sales = $total_sales_result ? mysqli_fetch_assoc($total_sales_result)['grand_total'] : 0;

    // Get total orders count
    $total_orders_sql = "SELECT COUNT(*) as total_orders FROM orders WHERE order_status IN ('Completed', 'completed', 'delivered', 'shipped')";
    $total_orders_result = mysqli_query($conn, $total_orders_sql);
    $total_orders = $total_orders_result ? mysqli_fetch_assoc($total_orders_result)['total_orders'] : 0;

    // Get recent orders for quick overview
    $recent_orders_sql = "SELECT order_number, customer_name, final_amount, order_status, created_at 
                         FROM orders 
                         ORDER BY created_at DESC 
                         LIMIT 5";
    $recent_orders_result = mysqli_query($conn, $recent_orders_sql);
} else {
    $error = "Orders table not found in database.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Dashboard</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
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
        
        .bg-success {
            background: linear-gradient(135deg, #4cc9f0, #4895ef) !important;
        }
        
        .bg-info {
            background: linear-gradient(135deg, #7209b7, #560bad) !important;
        }
        
        .bg-warning {
            background: linear-gradient(135deg, #f72585, #b5179e) !important;
        }
        
        .list-group-item {
            border: none;
            border-left: 4px solid transparent;
            margin-bottom: 10px;
            border-radius: 8px !important;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .list-group-item:hover {
            border-left-color: var(--primary-color);
            background-color: #f8f9fa;
            transform: translateX(5px);
        }
        
        .badge {
            border-radius: 20px;
            padding: 6px 12px;
            font-weight: 500;
        }
        
        .table-hover tbody tr {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(67, 97, 238, 0.05);
            transform: scale(1.01);
        }
        
        .chart-container {
            position: relative;
            height: 250px;
            width: 100%;
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
        
        .export-buttons .btn {
            margin-left: 10px;
            border-radius: 8px;
        }
    </style>
</head>
<body>
<div class="main-content">
    <div class="page-header">
        <h2 class="animated fadeInDown">Sales Dashboard</h2>
        <div class="export-buttons">
            <button class="btn btn-outline-primary btn-action">
                <i class="fas fa-download me-2"></i>Export PDF
            </button>
            <button class="btn btn-outline-success btn-action">
                <i class="fas fa-file-excel me-2"></i>Export Excel
            </button>
        </div>
    </div>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger animated fadeInUp"><?= $error ?></div>
        <div class="alert alert-info animated fadeInUp">
            <strong>Note:</strong> This page requires an 'orders' table with your e-commerce structure.
        </div>
    <?php else: ?>
        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="summary-card bg-primary animated fadeInUp" style="animation-delay: 0.1s;" onclick="window.location.href='orders.php?status=completed'">
                    <div class="card-body">
                        <h5 class="card-title">Total Revenue</h5>
                        <h3>$<?= number_format($total_sales, 2) ?></h3>
                        <p class="card-text">All completed orders</p>
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="summary-card bg-success animated fadeInUp" style="animation-delay: 0.2s;" onclick="window.location.href='orders.php'">
                    <div class="card-body">
                        <h5 class="card-title">Total Orders</h5>
                        <h3><?= number_format($total_orders) ?></h3>
                        <p class="card-text">Completed orders</p>
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="summary-card bg-info animated fadeInUp" style="animation-delay: 0.3s;" onclick="alert('Average Order Value: $<?= $total_orders > 0 ? number_format($total_sales / $total_orders, 2) : '0.00' ?>')">
                    <div class="card-body">
                        <h5 class="card-title">Avg Order Value</h5>
                        <h3>$<?= $total_orders > 0 ? number_format($total_sales / $total_orders, 2) : '0.00' ?></h3>
                        <p class="card-text">Per completed order</p>
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="summary-card bg-warning animated fadeInUp" style="animation-delay: 0.4s;" onclick="window.location.href='orders.php?status=pending'">
                    <div class="card-body">
                        <h5 class="card-title">Pending Orders</h5>
                        <?php
                        $pending_sql = "SELECT COUNT(*) as pending FROM orders WHERE order_status IN ('pending', 'processing')";
                        $pending_result = mysqli_query($conn, $pending_sql);
                        $pending_count = $pending_result ? mysqli_fetch_assoc($pending_result)['pending'] : 0;
                        ?>
                        <h3><?= number_format($pending_count) ?></h3>
                        <p class="card-text">Awaiting processing</p>
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Chart Section -->
            <div class="col-md-8">
                <div class="card mb-4 animated fadeInUp" style="animation-delay: 0.5s;">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Monthly Revenue Trend</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="salesReportChart" width="600" height="250"></canvas>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <div class="row text-center">
                            <div class="col-4">
                                <span class="text-muted">Current Month</span>
                                <h5 class="mb-0">$<?= isset($totals[0]) ? number_format($totals[0], 2) : '0.00' ?></h5>
                            </div>
                            <div class="col-4">
                                <span class="text-muted">Growth</span>
                                <h5 class="mb-0 text-success">+12.5% <i class="fas fa-arrow-up"></i></h5>
                            </div>
                            <div class="col-4">
                                <span class="text-muted">Target</span>
                                <h5 class="mb-0">$15,000</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="col-md-4">
                <div class="card animated fadeInUp" style="animation-delay: 0.6s;">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Recent Orders</h5>
                        <a href="orders.php" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body p-0">
                        <?php if ($recent_orders_result && mysqli_num_rows($recent_orders_result) > 0): ?>
                            <div class="list-group list-group-flush">
                                <?php while($order = mysqli_fetch_assoc($recent_orders_result)): ?>
                                <div class="list-group-item" onclick="window.location.href='order_details.php?id=<?= $order['order_number'] ?>'">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">#<?= $order['order_number'] ?></h6>
                                        <strong class="text-primary">$<?= number_format($order['final_amount'], 2) ?></strong>
                                    </div>
                                    <p class="mb-1"><?= htmlspecialchars($order['customer_name']) ?></p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <?= date('M j, Y', strtotime($order['created_at'])) ?>
                                        </small>
                                        <span class="badge bg-<?= 
                                            $order['order_status'] == 'completed' ? 'success' : 
                                            ($order['order_status'] == 'processing' ? 'warning' : 
                                            ($order['order_status'] == 'pending' ? 'secondary' : 'info')) ?>">
                                            <?= ucfirst($order['order_status']) ?>
                                        </span>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No recent orders found.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="card mt-4 animated fadeInUp" style="animation-delay: 0.7s;">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Monthly Sales Report</h5>
                <div>
                    <button class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <button class="btn btn-sm btn-outline-primary ms-1">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Month</th>
                                <th>Total Revenue ($)</th>
                                <th>Order Count</th>
                                <th>Average Order ($)</th>
                                <th>Growth</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $months = [];
                            $totals = [];
                            $order_counts = [];
                            $previous_total = 0;
                            
                            if ($result && mysqli_num_rows($result) > 0) {
                                mysqli_data_seek($result, 0); // Reset result pointer
                                while($row = mysqli_fetch_assoc($result)): 
                                    $months[] = date('M Y', strtotime($row['month'] . '-01'));
                                    $totals[] = $row['total_sales'];
                                    $order_counts[] = $row['order_count'];
                                    
                                    // Calculate growth percentage
                                    $growth = $previous_total > 0 ? (($row['total_sales'] - $previous_total) / $previous_total) * 100 : 0;
                                    $previous_total = $row['total_sales'];
                            ?>
                            <tr onclick="window.location.href='orders.php?month=<?= $row['month'] ?>'">
                                <td><strong><?= date('F Y', strtotime($row['month'] . '-01')) ?></strong></td>
                                <td>$<?= number_format($row['total_sales'], 2) ?></td>
                                <td><?= number_format($row['order_count']) ?></td>
                                <td>$<?= number_format($row['total_sales'] / $row['order_count'], 2) ?></td>
                                <td>
                                    <?php if ($growth > 0): ?>
                                        <span class="text-success">+<?= number_format($growth, 1) ?>% <i class="fas fa-arrow-up"></i></span>
                                    <?php elseif ($growth < 0): ?>
                                        <span class="text-danger"><?= number_format($growth, 1) ?>% <i class="fas fa-arrow-down"></i></span>
                                    <?php else: ?>
                                        <span class="text-muted">0%</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-chart-bar"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; 
                            } else {
                                echo '<tr><td colspan="6" class="text-center py-4">No sales data available</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function(){
            var ctx = document.getElementById('salesReportChart').getContext('2d');
            var chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: <?= json_encode($months) ?>,
                    datasets: [{
                        label: 'Monthly Revenue ($)',
                        data: <?= json_encode($totals) ?>,
                        backgroundColor: 'rgba(67, 97, 238, 0.1)',
                        borderColor: 'rgba(67, 97, 238, 1)',
                        borderWidth: 3,
                        pointBackgroundColor: 'rgba(67, 97, 238, 1)',
                        pointBorderColor: '#fff',
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: 'rgba(67, 97, 238, 1)',
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return '$' + context.raw.toFixed(2);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                drawBorder: false
                            },
                            ticks: {
                                callback: function(value) {
                                    return '$' + value.toFixed(2);
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    animation: {
                        duration: 2000,
                        easing: 'easeOutQuart'
                    }
                }
            });
            
            // Add animation to summary cards on hover
            document.querySelectorAll('.summary-card').forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.classList.add('pulse');
                });
                
                card.addEventListener('mouseleave', function() {
                    this.classList.remove('pulse');
                });
            });
        });
        </script>
    <?php endif; ?>
</div>

<!-- Bootstrap JS and Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>
<?php require_once 'includes/footer.php'; ?>