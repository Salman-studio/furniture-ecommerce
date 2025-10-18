<?php
// furniture/includes/sidebar.php
// Enhanced sidebar navigation for admin or product filters. Include where appropriate.

?>
<aside class="admin-sidebar">
  <div class="sidebar-header">
    <h3 class="sidebar-title">
      <i class="fas fa-cog me-2"></i>Admin Panel
    </h3>
    <button class="sidebar-toggle" id="sidebarToggle">
      <i class="fas fa-bars"></i>
    </button>
  </div>
  
  <div class="sidebar-content">
    <div class="user-info mb-4">
      <div class="user-avatar">
        <i class="fas fa-user-circle"></i>
      </div>
      <div class="user-details">
        <h5 class="user-name"><?php echo $_SESSION['user_name'] ?? 'Admin User'; ?></h5>
        <span class="user-role"><?php echo $_SESSION['user_role'] ?? 'Administrator'; ?></span>
      </div>
    </div>
    
    <ul class="sidebar-menu">
      <li class="menu-item">
        <a href="dashboard.php" class="menu-link">
          <span class="menu-icon"><i class="fas fa-tachometer-alt"></i></span>
          <span class="menu-text">Dashboard</span>
        </a>
      </li>
      
      <li class="menu-item">
        <a href="products.php" class="menu-link">
          <span class="menu-icon"><i class="fas fa-box"></i></span>
          <span class="menu-text">Products</span>
          <span class="menu-badge">12</span>
        </a>
      </li>
      
      <li class="menu-item">
        <a href="categories.php" class="menu-link">
          <span class="menu-icon"><i class="fas fa-tags"></i></span>
          <span class="menu-text">Categories</span>
        </a>
      </li>
      
      <li class="menu-item">
        <a href="orders.php" class="menu-link">
          <span class="menu-icon"><i class="fas fa-receipt"></i></span>
          <span class="menu-text">Orders</span>
          <span class="menu-badge badge-warning">5</span>
        </a>
      </li>
      
      <li class="menu-item">
        <a href="customers.php" class="menu-link">
          <span class="menu-icon"><i class="fas fa-users"></i></span>
          <span class="menu-text">Customers</span>
        </a>
      </li>
      
      <li class="menu-item">
        <a href="inventory.php" class="menu-link">
          <span class="menu-icon"><i class="fas fa-warehouse"></i></span>
          <span class="menu-text">Inventory</span>
        </a>
      </li>
      
      <li class="menu-divider"></li>
      
      <li class="menu-item">
        <a href="reports.php" class="menu-link">
          <span class="menu-icon"><i class="fas fa-chart-bar"></i></span>
          <span class="menu-text">Reports</span>
        </a>
      </li>
      
      <li class="menu-item">
        <a href="settings.php" class="menu-link">
          <span class="menu-icon"><i class="fas fa-cog"></i></span>
          <span class="menu-text">Settings</span>
        </a>
      </li>
    </ul>
  </div>
  
  <div class="sidebar-footer">
    <a href="logout.php" class="logout-btn">
      <i class="fas fa-sign-out-alt"></i> Logout
    </a>
  </div>
</aside>

<style>
  /* Sidebar Styles */
  .admin-sidebar {
    width: 280px;
    height: 100vh;
    background: linear-gradient(to bottom, var(--dark-color), var(--primary-color));
    color: white;
    position: fixed;
    left: 0;
    top: 0;
    z-index: 1000;
    display: flex;
    flex-direction: column;
    box-shadow: 3px 0 15px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
  }
  
  .sidebar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem 1.25rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  }
  
  .sidebar-title {
    font-family: 'Playfair Display', serif;
    font-size: 1.4rem;
    margin: 0;
    color: white;
  }
  
  .sidebar-toggle {
    background: transparent;
    border: none;
    color: white;
    font-size: 1.2rem;
    cursor: pointer;
    transition: all 0.3s ease;
  }
  
  .sidebar-toggle:hover {
    color: var(--secondary-color);
  }
  
  .sidebar-content {
    flex: 1;
    padding: 1.25rem;
    overflow-y: auto;
  }
  
  .user-info {
    display: flex;
    align-items: center;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    margin-bottom: 1.5rem;
  }
  
  .user-avatar {
    font-size: 2.5rem;
    margin-right: 1rem;
    color: var(--secondary-color);
  }
  
  .user-name {
    font-size: 1rem;
    margin: 0;
    font-weight: 600;
  }
  
  .user-role {
    font-size: 0.8rem;
    opacity: 0.8;
  }
  
  .sidebar-menu {
    list-style: none;
    padding: 0;
    margin: 0;
  }
  
  .menu-item {
    margin-bottom: 0.5rem;
  }
  
  .menu-link {
    display: flex;
    align-items: center;
    padding: 0.85rem 1rem;
    color: rgba(255, 255, 255, 0.85);
    text-decoration: none;
    border-radius: 6px;
    transition: all 0.3s ease;
    position: relative;
  }
  
  .menu-link:hover, .menu-link.active {
    background: rgba(255, 255, 255, 0.1);
    color: white;
    transform: translateX(5px);
  }
  
  .menu-icon {
    width: 24px;
    text-align: center;
    margin-right: 12px;
    font-size: 1.1rem;
  }
  
  .menu-text {
    flex: 1;
  }
  
  .menu-badge {
    padding: 0.2rem 0.5rem;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 600;
    background: var(--accent-color);
  }
  
  .badge-warning {
    background: #ffc107;
    color: #212529;
  }
  
  .menu-divider {
    height: 1px;
    background: rgba(255, 255, 255, 0.1);
    margin: 1rem 0;
  }
  
  .sidebar-footer {
    padding: 1.25rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
  }
  
  .logout-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0.75rem;
    background: rgba(255, 255, 255, 0.1);
    color: white;
    text-decoration: none;
    border-radius: 6px;
    transition: all 0.3s ease;
  }
  
  .logout-btn:hover {
    background: var(--accent-color);
    color: white;
  }
  
  /* Collapsed state */
  .sidebar-collapsed {
    width: 80px;
  }
  
  .sidebar-collapsed .sidebar-title,
  .sidebar-collapsed .user-details,
  .sidebar-collapsed .menu-text,
  .sidebar-collapsed .menu-badge,
  .sidebar-collapsed .logout-btn span {
    display: none;
  }
  
  .sidebar-collapsed .user-info {
    justify-content: center;
    padding: 0.5rem;
  }
  
  .sidebar-collapsed .user-avatar {
    margin-right: 0;
    font-size: 2rem;
  }
  
  .sidebar-collapsed .menu-link {
    justify-content: center;
    padding: 1rem;
  }
  
  .sidebar-collapsed .menu-icon {
    margin-right: 0;
    font-size: 1.3rem;
  }
  
  .sidebar-collapsed .logout-btn {
    padding: 1rem;
  }
  
  .sidebar-collapsed .logout-btn i {
    margin-right: 0;
  }
  
  /* Responsive adjustments */
  @media (max-width: 992px) {
    .admin-sidebar {
      transform: translateX(-100%);
      width: 280px;
    }
    
    .sidebar-show {
      transform: translateX(0);
    }
    
    .sidebar-overlay {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.5);
      z-index: 999;
      display: none;
    }
    
    .sidebar-overlay.show {
      display: block;
    }
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('.admin-sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const overlay = document.createElement('div');
    overlay.className = 'sidebar-overlay';
    document.body.appendChild(overlay);
    
    // Toggle sidebar on button click
    if (sidebarToggle) {
      sidebarToggle.addEventListener('click', function() {
        sidebar.classList.toggle('sidebar-collapsed');
        
        // On mobile, we want a different behavior
        if (window.innerWidth <= 992) {
          sidebar.classList.toggle('sidebar-show');
          overlay.classList.toggle('show');
        }
      });
    }
    
    // Close sidebar when clicking on overlay
    overlay.addEventListener('click', function() {
      sidebar.classList.remove('sidebar-show');
      overlay.classList.remove('show');
    });
    
    // Auto-collapse on smaller screens
    function handleResize() {
      if (window.innerWidth <= 992) {
        sidebar.classList.remove('sidebar-collapsed');
      } else {
        sidebar.classList.add('sidebar-collapsed');
        overlay.classList.remove('show');
      }
    }
    
    // Initial call and event listener
    handleResize();
    window.addEventListener('resize', handleResize);
    
    // Add active class to current page
    const currentPage = window.location.pathname.split('/').pop();
    const menuLinks = document.querySelectorAll('.menu-link');
    
    menuLinks.forEach(link => {
      const href = link.getAttribute('href');
      if (href === currentPage) {
        link.classList.add('active');
      }
    });
  });
</script>