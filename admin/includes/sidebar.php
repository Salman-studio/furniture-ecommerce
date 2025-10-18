<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

<!-- Bootstrap, Font Awesome & Animate.css -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<!-- Sidebar Toggle Button -->
<button class="btn btn-dark d-md-none m-2" id="sidebarToggle">
    <i class="fa fa-bars"></i>
</button>

<!-- Sidebar -->
<nav class="sidebar animate__animated animate__fadeInLeft" id="sidebar">
    <div class="sidebar-header text-center text-white py-3">
        <h5 class="mb-0">Furniture Admin</h5>
    </div>
    <ul>
        <li><a href="dashboard.php"><i class="fa fa-tachometer"></i> Dashboard</a></li>
        <li><a href="categories.php"><i class="fa fa-list"></i> Categories</a></li>
        <li><a href="subcategories.php"><i class="fa fa-list-alt"></i> Subcategories</a></li>
        <li><a href="brands.php"><i class="fa fa-industry"></i> Brands</a></li>
        <li><a href="products.php"><i class="fa fa-cube"></i> Products</a></li>
        <li><a href="orders.php"><i class="fa fa-shopping-cart"></i> Orders</a></li>
        <li><a href="invoices.php"><i class="fa fa-file-text"></i> Invoices</a></li>
        <li><a href="reports.php"><i class="fa fa-bar-chart"></i> Reports</a></li>
        <li><a href="users.php"><i class="fa fa-users"></i> Users</a></li>
        <li><a href="reviews.php"><i class="fa fa-star"></i> Reviews</a></li>
        <li><a href="company_profile.php"><i class="fa fa-building"></i> Company Profile</a></li>
        <li><a href="logout.php"><i class="fa fa-sign-out"></i> Logout</a></li>
    </ul>
</nav>

<style>
/* Sidebar Styling */
.sidebar {
    width: 230px;
    background: #2c3e50;
    height: 100vh;
    position: fixed;
    top: 60px; /* Adjust if header height changes */
    left: 0;
    padding-top: 10px;
    transition: all 0.3s ease-in-out;
    box-shadow: 2px 0px 10px rgba(0,0,0,0.15);
    font-family: 'Poppins', sans-serif;
    z-index: 1050;
}

/* Collapsed Sidebar */
.sidebar.collapsed {
    left: -250px;
}

.sidebar ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.sidebar ul li {
    margin: 6px 0;
}

.sidebar ul li a {
    display: flex;
    align-items: center;
    color: #ecf0f1;
    padding: 12px 20px;
    text-decoration: none;
    font-size: 15px;
    font-weight: 500;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.sidebar ul li a i {
    margin-right: 12px;
    font-size: 16px;
    transition: transform 0.3s ease;
}

.sidebar ul li a:hover {
    background: #34495e;
    color: #f1c40f;
    transform: translateX(5px);
}

.sidebar ul li a:hover i {
    transform: scale(1.2) rotate(8deg);
}

/* Mobile adjustments */
@media (max-width: 768px) {
    .sidebar {
        left: -250px;
    }
    .sidebar.active {
        left: 0;
    }
}
</style>

<!-- Sidebar Toggle Script -->
<script>
    document.getElementById("sidebarToggle").addEventListener("click", function() {
        document.getElementById("sidebar").classList.toggle("active");
    });
</script>
