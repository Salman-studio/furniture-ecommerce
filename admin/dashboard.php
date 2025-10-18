<?php
require_once 'includes/header.php';
require_once 'includes/sidebar.php';
require_once 'includes/db_connection.php';



// Initialize variables to avoid "undefined" warnings
$products_count = 0;
$orders_count = 0;
$users_count = 0;
$total_sales = 0.00;
$recent_orders_result = [];
$completed_orders = 0;
$pending_orders = 0;
$cancelled_orders = 0;
$average_rating = 0;
$avg_order_value = 0.00;
$success_rate = 0;

// ===================== PRODUCTS =====================
$sql = "SELECT COUNT(*) AS total FROM products";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $products_count = $row['total'];
}

// ===================== ORDERS =====================
$sql = "SELECT COUNT(*) AS total FROM orders";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $orders_count = $row['total'];
}

// ===================== USERS =====================
$sql = "SELECT COUNT(*) AS total FROM users";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $users_count = $row['total'];
}

// ===================== TOTAL SALES =====================
$sql = "SELECT SUM(total_amount) AS sales FROM orders WHERE order_status='completed'";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $total_sales = $row['sales'] ?? 0;
}

// ===================== RECENT ORDERS =====================
$sql = "SELECT order_id, customer_id, total_amount, order_status, created_at 
        FROM orders 
        ORDER BY created_at DESC 
        LIMIT 5";
$recent_orders_result = $conn->query($sql);

// ===================== ORDER STATUS COUNTS =====================
$sql = "SELECT order_status, COUNT(*) as total FROM orders GROUP BY order_status";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        if ($row['order_status'] == 'completed') $completed_orders = $row['total'];
        if ($row['order_status'] == 'pending') $pending_orders = $row['total'];
        if ($row['order_status'] == 'cancelled') $cancelled_orders = $row['total'];
    }
}

// ===================== AVERAGE RATING =====================
$sql = "SELECT AVG(rating) as avg_rating FROM reviews";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $average_rating = round($row['avg_rating'], 2);
}

// ===================== AVERAGE ORDER VALUE =====================
if ($orders_count > 0) {
    $avg_order_value = round($total_sales / $orders_count, 2);
}

// ===================== SUCCESS RATE =====================
if ($orders_count > 0) {
    $success_rate = round(($completed_orders / $orders_count) * 100, 2);
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        :root {
            --primary-color: #4e73df;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --secondary-color: #858796;
            --light-color: #f8f9fc;
            --dark-color: #5a5c69;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fc;
            color: #5a5c69;
        }
        
        h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
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
        
        .dashboard-card {
            margin-bottom: 20px;
            perspective: 1000px;
        }
        
        .dashboard-card .card {
            transition: all 0.5s ease;
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .dashboard-card .card:hover {
            transform: translateY(-10px) rotateX(5deg);
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }
        
        .dashboard-card .card-body {
            padding: 1.5rem;
            position: relative;
        }
        
        .dashboard-card .card-body i {
            position: absolute;
            top: 15px;
            right: 15px;
            opacity: 0.2;
            font-size: 2.5rem;
            transition: all 0.3s;
        }
        
        .dashboard-card .card:hover .card-body i {
            transform: scale(1.2) rotate(15deg);
            opacity: 0.3;
        }
        
        .dashboard-card .card.bg-primary {
            background: linear-gradient(135deg, var(--primary-color), #2a3db9) !important;
        }
        
        .dashboard-card .card.bg-success {
            background: linear-gradient(135deg, var(--success-color), #17a673) !important;
        }
        
        .dashboard-card .card.bg-info {
            background: linear-gradient(135deg, var(--info-color), #258391) !important;
        }
        
        .dashboard-card .card.bg-warning {
            background: linear-gradient(135deg, var(--warning-color), #dda20a) !important;
        }
        
        .stats-item {
            padding: 20px 15px;
            transition: all 0.3s;
            border-radius: 8px;
        }
        
        .stats-item:hover {
            background-color: rgba(0,0,0,0.03);
            transform: translateY(-5px);
        }
        
        .stats-item i {
            font-size: 2rem;
            margin-bottom: 10px;
            transition: all 0.3s;
        }
        
        .stats-item:hover i {
            transform: scale(1.2);
        }
        
        .list-group-item {
            border: 1px solid #eee;
            margin-bottom: 10px;
            border-radius: 8px;
            transition: all 0.3s;
        }
        
        .list-group-item:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
        }
        
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-bottom: 24px;
            transition: all 0.3s;
        }
        
        .card:hover {
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        .card-header {
            background-color: white;
            border-bottom: 1px solid #e3e6f0;
            padding: 1rem 1.35rem;
            font-weight: 600;
            border-radius: 10px 10px 0 0 !important;
        }
        
        .animate-bounce {
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {transform: translateY(0);}
            40% {transform: translateY(-20px);}
            60% {transform: translateY(-10px);}
        }
        
        .animate-pulse {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% {transform: scale(1);}
            50% {transform: scale(1.05);}
            100% {transform: scale(1);}
        }
        
        .counter {
            font-size: 1.8rem;
            font-weight: 700;
            transition: all 0.5s;
        }
        
        .badge {
            font-weight: 500;
            padding: 0.35em 0.65em;
        }
        
        /* Animation delays */
        .delay-1 { animation-delay: 0.2s; }
        .delay-2 { animation-delay: 0.4s; }
        .delay-3 { animation-delay: 0.6s; }
        .delay-4 { animation-delay: 0.8s; }
        .delay-5 { animation-delay: 1s; }
    </style>
</head>
<body>
<div class="main-content">
    <h1 class="animate__animated animate__fadeInDown mb-4">Dashboard Overview</h1>
    
    <!-- Statistics Cards -->
    <div class="row">
        <!-- Products -->
        <div class="col-md-3 dashboard-card">
            <a href="products.php" class="text-decoration-none text-white">
                <div class="card bg-primary text-white mb-4 animate__animated animate__fadeInUp">
                    <div class="card-body text-center position-relative">
                        <i class="fas fa-cube"></i>
                        <h4>Products</h4>
                        <h2 class="counter"><?= number_format($products_count) ?></h2>
                        <p class="mb-0">Total Products</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Orders -->
        <div class="col-md-3 dashboard-card">
            <a href="orders.php" class="text-decoration-none text-white">
                <div class="card bg-success text-white mb-4 animate__animated animate__fadeInUp delay-1">
                    <div class="card-body text-center position-relative">
                        <i class="fas fa-shopping-cart"></i>
                        <h4>Orders</h4>
                        <h2 class="counter"><?= number_format($orders_count) ?></h2>
                        <p class="mb-0">Total Orders</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Users -->
        <div class="col-md-3 dashboard-card">
            <a href="users.php" class="text-decoration-none text-white">
                <div class="card bg-info text-white mb-4 animate__animated animate__fadeInUp delay-2">
                    <div class="card-body text-center position-relative">
                        <i class="fas fa-users"></i>
                        <h4>Users</h4>
                        <h2 class="counter"><?= number_format($users_count) ?></h2>
                        <p class="mb-0">Registered Users</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Sales -->
        <div class="col-md-3 dashboard-card">
            <a href="sales_report.php" class="text-decoration-none text-white">
                <div class="card bg-warning text-white mb-4 animate__animated animate__fadeInUp delay-3">
                    <div class="card-body text-center position-relative">
                        <i class="fas fa-rupee-sign"></i>
                        <h4>Sales</h4>
                        <h2 class="counter">₹<?= number_format($total_sales, 2) ?></h2>
                        <p class="mb-0">Total Revenue</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Quick Stats Row -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card animate__animated animate__fadeInUp">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Quick Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">

                        <!-- Completed Orders -->
                        <div class="col-md-2">
                            <a href="orders.php?status=completed" class="text-decoration-none text-dark">
                                <div class="stats-item">
                                    <i class="fas fa-check-circle fa-2x text-success"></i>
                                    <h4 class="counter"><?= number_format($completed_orders) ?></h4>
                                    <small>Completed Orders</small>
                                </div>
                            </a>
                        </div>

                        <!-- Pending Orders -->
                        <div class="col-md-2">
                            <a href="orders.php?status=pending" class="text-decoration-none text-dark">
                                <div class="stats-item">
                                    <i class="fas fa-clock fa-2x text-warning"></i>
                                    <h4 class="counter"><?= number_format($pending_orders) ?></h4>
                                    <small>Pending Orders</small>
                                </div>
                            </a>
                        </div>

                        <!-- Cancelled Orders -->
                        <div class="col-md-2">
                            <a href="orders.php?status=cancelled" class="text-decoration-none text-dark">
                                <div class="stats-item">
                                    <i class="fas fa-times-circle fa-2x text-danger"></i>
                                    <h4 class="counter"><?= number_format($cancelled_orders) ?></h4>
                                    <small>Cancelled Orders</small>
                                </div>
                            </a>
                        </div>

                        <!-- Reviews / Ratings -->
                        <div class="col-md-2">
                            <a href="reviews.php" class="text-decoration-none text-dark">
                                <div class="stats-item">
                                    <i class="fas fa-star fa-2x text-info"></i>
                                    <h4 class="counter"><?= $average_rating ?></h4>
                                    <small>Average Rating</small>
                                </div>
                            </a>
                        </div>

                        <!-- Avg Order Value -->
                        <div class="col-md-2">
                            <a href="orders_report.php" class="text-decoration-none text-dark">
                                <div class="stats-item">
                                    <i class="fas fa-shopping-basket fa-2x text-primary"></i>
                                    <h4 class="counter">₹<?= number_format($avg_order_value, 2) ?></h4>
                                    <small>Avg Order Value</small>
                                </div>
                            </a>
                        </div>

                        <!-- Success Rate -->
                        <div class="col-md-2">
                            <a href="orders.php" class="text-decoration-none text-dark">
                                <div class="stats-item">
                                    <i class="fas fa-sync-alt fa-2x text-secondary"></i>
                                    <h4 class="counter"><?= $success_rate ?>%</h4>
                                    <small>Success Rate</small>
                                </div>
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap & jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Counter Animation -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/waypoints/4.0.1/jquery.waypoints.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Counter-Up/1.0.0/jquery.counterup.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize counter animation
    $('.counter').counterUp({
        delay: 10,
        time: 1000
    });
    
    // Sales Chart
    var salesChart = new Chart(document.getElementById('salesChart'), {
        type: 'line',
        data: {
            labels: <?= json_encode($monthly_labels) ?>,
            datasets: [{
                label: 'Monthly Sales',
                data: <?= json_encode($monthly_sales) ?>,
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                borderColor: 'rgba(78, 115, 223, 1)',
                borderWidth: 2,
                pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
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
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toFixed(2);
                        }
                    },
                    grid: {
                        drawBorder: false,
                        color: 'rgba(0,0,0,0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
    
    // Add animation to cards on scroll
    function animateElements() {
        $('.animate__animated').each(function() {
            var position = $(this).offset().top;
            var scroll = $(window).scrollTop();
            var windowHeight = $(window).height();
            
            if (scroll + windowHeight - 100 > position) {
                var animation = $(this).data('animation');
                $(this).addClass(animation);
            }
        });
    }
    
    // Initial call
    animateElements();
    
    // Call on scroll
    $(window).scroll(function() {
        animateElements();
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>