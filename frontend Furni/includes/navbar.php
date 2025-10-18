<?php
// furniture/includes/navbar.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Simulate user data - replace with actual user session
$isLoggedIn  = isset($_SESSION['user_id']);
$userName    = $isLoggedIn ? ($_SESSION['user_name'] ?? 'User') : 'Account';
$userAvatar  = $isLoggedIn ? ($_SESSION['user_avatar'] ?? null) : null;
$cartCount   = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>
<nav class="navbar navbar-expand-lg navbar-dark sticky-top shadow">
  <div class="container">
    <!-- Brand / Logo -->
    <a class="navbar-brand d-flex align-items-center" href="index.php">
      <img src="assets/images/logo.png" alt="NoirLuxe Logo" class="me-2" height="40"
           onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-block';">
      <span class="logo-text fw-bold fs-3 d-none">NoirLuxe</span>
    </a>

    <!-- Hamburger -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" 
            aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Nav links -->
    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link <?php echo ($pageTitle ?? '') === 'Home' ? 'active' : ''; ?>" href="index.php">
            <i class="fas fa-home me-1"></i>Home
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo ($pageTitle ?? '') === 'Shop' ? 'active' : ''; ?>" href="products.php">
            <i class="fas fa-store me-1"></i>Shop
          </a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle <?php echo ($pageTitle ?? '') === 'Categories' ? 'active' : ''; ?>" href="categories.php" id="categoriesDropdown" 
             role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-list me-1"></i>Categories
          </a>
          <ul class="dropdown-menu" aria-labelledby="categoriesDropdown">
            <li><a class="dropdown-item" href="categories.php?type=chairs">Chairs</a></li>
            <li><a class="dropdown-item" href="categories.php?type=tables">Tables</a></li>
            <li><a class="dropdown-item" href="categories.php?type=sofas">Sofas</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="categories.php">View All</a></li>
          </ul>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo ($pageTitle ?? '') === 'Custom Work' ? 'active' : ''; ?>" href="custom_work.php">
            <i class="fas fa-hammer me-1"></i>Custom Work
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo ($pageTitle ?? '') === 'Repair' ? 'active' : ''; ?>" href="repair.php">
            <i class="fas fa-tools me-1"></i>Repair
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo ($pageTitle ?? '') === 'Contact' ? 'active' : ''; ?>" href="contact.php">
            <i class="fas fa-envelope me-1"></i>Contact
          </a>
        </li>
      </ul>
      
      <!-- Right side: search + account + cart -->
      <div class="d-flex align-items-center flex-wrap">
        <!-- Search -->
        <form class="d-none d-md-flex me-2" role="search">
          <div class="input-group">
            <input class="form-control form-control-sm" type="search" placeholder="Search products..." aria-label="Search">
            <button class="btn btn-outline-light btn-sm" type="submit">
              <i class="fas fa-search"></i>
            </button>
          </div>
        </form>

        <!-- User Dropdown -->
        <div class="dropdown me-3">
          <a class="btn btn-outline-light btn-sm dropdown-toggle d-flex align-items-center" href="#" role="button" 
             id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <?php if ($isLoggedIn): ?>
              <div class="profile-image-container me-2">
                <?php if ($userAvatar): ?>
                  <img src="<?php echo htmlspecialchars($userAvatar); ?>" alt="Profile" class="rounded-circle" width="24" height="24">
                <?php endif; ?>
                <div class="profile-placeholder rounded-circle d-flex align-items-center justify-content-center <?php echo $userAvatar ? 'd-none' : 'd-flex'; ?>" 
                     style="width: 24px; height: 24px; background-color: var(--secondary-color);">
                  <i class="fas fa-user text-white" style="font-size: 12px;"></i>
                </div>
              </div>
              <span class="d-none d-sm-inline"><?php echo htmlspecialchars($userName); ?></span>
            <?php else: ?>
              <i class="fas fa-user me-1"></i><span class="d-none d-sm-inline">Account</span>
            <?php endif; ?>
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
            <?php if ($isLoggedIn): ?>
              <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user-circle me-2"></i>My Profile</a></li>
              <li><a class="dropdown-item" href="edit_profile.php"><i class="fas fa-edit me-2"></i>Edit Profile</a></li>
              <li><a class="dropdown-item" href="wishlist.php"><i class="fas fa-heart me-2"></i>Wishlist</a></li>
              <li><a class="dropdown-item" href="order_history.php"><i class="fas fa-history me-2"></i>Order History</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
            <?php else: ?>
              <li><a class="dropdown-item" href="login.php"><i class="fas fa-sign-in-alt me-2"></i>Login</a></li>
              <li><a class="dropdown-item" href="register.php"><i class="fas fa-user-plus me-2"></i>Register</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="wishlist.php"><i class="fas fa-heart me-2"></i>Wishlist</a></li>
              <li><a class="dropdown-item" href="order_history.php"><i class="fas fa-history me-2"></i>Order History</a></li>
            <?php endif; ?>
          </ul>
        </div>

        <!-- Cart -->
        <a class="btn btn-primary position-relative mt-2 mt-md-0" href="cart.php">
          <i class="fas fa-shopping-cart"></i>
          <?php if ($cartCount > 0): ?>
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
            <?php echo $cartCount; ?>
          </span>
          <?php endif; ?>
        </a>
      </div>
    </div>
  </div>
</nav>

<!-- Mobile search -->
<div class="d-md-none bg-light p-2 border-bottom">
  <form class="d-flex" role="search">
    <input class="form-control form-control-sm me-2" type="search" placeholder="Search products..." aria-label="Search">
    <button class="btn btn-outline-primary btn-sm" type="submit"><i class="fas fa-search"></i></button>
  </form>
</div>

<style>
/* Gradient background */
.navbar {
  background: linear-gradient(135deg, var(--primary-color) 0%, var(--dark-color) 100%) !important;
  padding: 0.5rem 0;
}
.nav-link { position: relative; transition: all .3s ease; margin: 0 2px; }
.nav-link:hover { background: rgba(255,255,255,.1); transform: translateY(-2px); }
.nav-link.active { color: var(--secondary-color) !important; font-weight: 600; }

/* Dropdown */
.dropdown-menu { border: none; box-shadow: 0 5px 15px rgba(0,0,0,.15); min-width: 200px; }
.dropdown-item:hover { background: var(--secondary-color); color: #fff; }

/* Mobile collapse styling */
@media (max-width: 991.98px) {
  .navbar-collapse {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--dark-color) 100%);
    padding: 1rem; border-radius: 0 0 10px 10px;
    margin-top: 0.5rem;
  }
  .d-flex.align-items-center.flex-wrap { flex-direction: column; align-items: flex-start !important; }
  .dropdown.me-3 { margin-bottom: 1rem; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
  // Fix smooth scroll only if target exists
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', e => {
      const target = document.querySelector(anchor.getAttribute('href'));
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth' });
      }
    });
  });
});
</script> 