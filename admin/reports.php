<?php
require_once 'includes/header.php';
require_once 'includes/sidebar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports Dashboard</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    
    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #1cc88a;
            --accent-color: #36b9cc;
            --dark-color: #2e59d9;
            --light-color: #f8f9fc;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fb;
            color: #5a5c69;
        }
        
        .main-content {
            padding: 30px;
            animation: fadeIn 0.8s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .dashboard-header {
            margin-bottom: 30px;
            border-bottom: 1px solid #e3e6f0;
            padding-bottom: 15px;
        }
        
        .dashboard-card {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: none;
            border-radius: 15px;
            overflow: hidden;
            position: relative;
            text-decoration: none;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            height: 100%;
        }
        
        .dashboard-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .dashboard-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }
        
        .dashboard-card:hover::before {
            opacity: 1;
        }
        
        .dashboard-card .card-body {
            padding: 2rem;
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
        
        .dashboard-card .icon-wrapper {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            background: rgba(255, 255, 255, 0.2);
            transition: transform 0.3s ease;
        }
        
        .dashboard-card:hover .icon-wrapper {
            transform: scale(1.1) rotate(5deg);
        }
        
        .dashboard-card img {
            width: 40px;
            height: 40px;
            filter: brightness(0) invert(1);
        }
        
        .dashboard-card h4 {
            margin-top: 15px;
            font-weight: 600;
            color: #fff;
            margin-bottom: 5px;
        }
        
        .dashboard-card p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
            margin-bottom: 0;
        }
        
        .dashboard-card .card-hover-content {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 15px;
            background: rgba(0, 0, 0, 0.1);
            text-align: center;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.3s ease;
        }
        
        .dashboard-card:hover .card-hover-content {
            opacity: 1;
            transform: translateY(0);
        }
        
        .bg-sales {
            background: linear-gradient(135deg, var(--primary-color), var(--dark-color));
        }
        
        .bg-inventory {
            background: linear-gradient(135deg, var(--secondary-color), #17a673);
        }
        
        .bg-customers {
            background: linear-gradient(135deg, #f6c23e, #dda20a);
        }
        
        .bg-analytics {
            background: linear-gradient(135deg, #e74a3b, #be2617);
        }
        
        .animate-delay-1 {
            animation-delay: 0.2s;
        }
        
        .animate-delay-2 {
            animation-delay: 0.4s;
        }
        
        .animate-delay-3 {
            animation-delay: 0.6s;
        }
        
        .animate-delay-4 {
            animation-delay: 0.8s;
        }
        
        /* Ripple effect */
        .ripple {
            position: relative;
            overflow: hidden;
        }
        
        .ripple-effect {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.4);
            transform: scale(0);
            animation: ripple 0.6s linear;
            pointer-events: none;
        }
        
        @keyframes ripple {
            to {
                transform: scale(2.5);
                opacity: 0;
            }
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .main-content {
                padding: 15px;
            }
            
            .dashboard-card .card-body {
                padding: 1.5rem;
            }
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
<div class="main-content">
    <div class="dashboard-header">
        <h2 class="animate__animated animate__fadeInDown mb-2">Reports Dashboard</h2>
        <p class="text-muted animate__animated animate__fadeIn">Access all your business reports in one place</p>
    </div>
    
    <div class="row">
        <!-- Sales Report Card -->
        <div class="col-md-6 col-lg-3 mb-4">
            <a href="sales_report.php" class="card bg-sales text-white dashboard-card ripple animate__animated animate__fadeInUp animate-delay-1">
                <div class="card-body">
                    <div class="icon-wrapper">
                        <img src="assets/images/report-icons/sales-icon.png" alt="Sales Report">
                    </div>
                    <h4>Sales Report</h4>
                    <p>View and analyze sales performance</p>
                    <div class="card-hover-content">
                        <small>Click to view <i class="fas fa-arrow-right ms-1"></i></small>
                    </div>
                </div>
            </a>
        </div>
        
        <!-- Inventory Report Card -->
        <div class="col-md-6 col-lg-3 mb-4">
            <a href="inventory_report.php" class="card bg-inventory text-white dashboard-card ripple animate__animated animate__fadeInUp animate-delay-2">
                <div class="card-body">
                    <div class="icon-wrapper">
                        <img src="assets/images/report-icons/inventory-icon.png" alt="Inventory Report">
                    </div>
                    <h4>Inventory Report</h4>
                    <p>Manage and track inventory levels</p>
                    <div class="card-hover-content">
                        <small>Click to view <i class="fas fa-arrow-right ms-1"></i></small>
                    </div>
                </div>
            </a>
        </div>
        
        <!-- Customer Report Card -->
        <div class="col-md-6 col-lg-3 mb-4">
            <a href="customer_report.php" class="card bg-customers text-white dashboard-card ripple animate__animated animate__fadeInUp animate-delay-3">
                <div class="card-body">
                    <div class="icon-wrapper">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                    <h4>Customer Report</h4>
                    <p>Analyze customer data and trends</p>
                    <div class="card-hover-content">
                        <small>Click to view <i class="fas fa-arrow-right ms-1"></i></small>
                    </div>
                </div>
            </a>
        </div>
        
        <!-- Analytics Report Card -->
        <div class="col-md-6 col-lg-3 mb-4">
            <a href="analytics_report.php" class="card bg-analytics text-white dashboard-card ripple animate__animated animate__fadeInUp animate-delay-4">
                <div class="card-body">
                    <div class="icon-wrapper">
                        <i class="fas fa-chart-line fa-2x"></i>
                    </div>
                    <h4>Analytics Report</h4>
                    <p>Detailed analytics and insights</p>
                    <div class="card-hover-content">
                        <small>Click to view <i class="fas fa-arrow-right ms-1"></i></small>
                    </div>
                </div>
            </a>
        </div>
    </div>
    
    <!-- Quick Stats Section -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card animate__animated animate__fadeIn">
                <div class="card-header bg-white">
                    <h5 class="m-0">Quick Stats</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-6 text-center mb-3">
                            <div class="p-3 border rounded">
                                <i class="fas fa-shopping-cart fa-2x text-primary mb-2"></i>
                                <h4 class="mb-0">page not created</h4>
                                <small class="text-muted">Today's Orders</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 text-center mb-3">
                            <div class="p-3 border rounded">
                                <i class="fas fa-rupee-sign"></i>
                                <h4 class="mb-0"></h4>
                                <small class="text-muted">Revenue</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 text-center mb-3">
                            <div class="p-3 border rounded">
                                <i class="fas fa-boxes fa-2x text-warning mb-2"></i>
                                <h4 class="mb-0"></h4>
                                <small class="text-muted">Low Stock Items</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 text-center mb-3">
                            <div class="p-3 border rounded">
                                <i class="fas fa-user-plus fa-2x text-info mb-2"></i>
                                <h4 class="mb-0"></h4>
                                <small class="text-muted">New Customers</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS and Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>

<script>
    // Add ripple effect to cards
    document.querySelectorAll('.ripple').forEach(card => {
        card.addEventListener('click', function(e) {
            const x = e.clientX - e.target.getBoundingClientRect().left;
            const y = e.clientY - e.target.getBoundingClientRect().top;
            
            const ripple = document.createElement('span');
            ripple.classList.add('ripple-effect');
            ripple.style.left = `${x}px`;
            ripple.style.top = `${y}px`;
            
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
    
    // Add animation on scroll
    document.addEventListener('DOMContentLoaded', function() {
        const animatedElements = document.querySelectorAll('.animate__animated');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const animation = entry.target.getAttribute('data-animation');
                    entry.target.classList.add(animation);
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1
        });
        
        animatedElements.forEach(element => {
            observer.observe(element);
        });
    });
</script>
</body>
</html>
<?php require_once 'includes/footer.php'; ?>