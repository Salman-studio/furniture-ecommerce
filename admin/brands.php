<?php
require_once 'includes/header.php';
require_once 'includes/sidebar.php';
require_once 'includes/db_connection.php';

// Fetch brands
$sql = "SELECT * FROM brands ORDER BY brand_id ASC";
$result = mysqli_query($conn, $sql);

// Check if query was successful
if (!$result) {
    die("Error: " . mysqli_error($conn));
}
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
    .action-btn {
        transition: all 0.2s ease;
        border-radius: 5px;
        margin: 0 2px;
    }
    .action-btn:hover {
        transform: scale(1.1);
    }
    .brand-card {
        transition: all 0.3s ease;
        border-radius: 10px;
        overflow: hidden;
        border: none;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .brand-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.15);
    }
    .table-hover tbody tr {
        transition: all 0.2s ease;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(0,123,255,0.05);
        transform: scale(1.01);
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
        <h2 class="mb-0 animated fadeInDown">Brand Management</h2>
        <a href="add_brand.php" class="btn btn-primary btn-animated">
            <i class="fas fa-plus-circle mr-2"></i> Add New Brand
        </a>
    </div>
    
    <?php if (mysqli_num_rows($result) == 0): ?>
        <div class="card animated-card">
            <div class="card-body text-center py-5">
                <i class="fas fa-tag fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">No Brands Found</h4>
                <p class="text-muted">Get started by adding your first brand to the system.</p>
                <a href="add_brand.php" class="btn btn-primary btn-animated mt-2">
                    <i class="fas fa-plus-circle mr-2"></i> Add Your First Brand
                </a>
            </div>
        </div>
    <?php else: ?>
        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card brand-card bg-primary text-white text-center">
                    <div class="card-body">
                        <i class="fas fa-tag fa-2x mb-2"></i>
                        <h6>Total Brands</h6>
                        <h3><?= mysqli_num_rows($result) ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card animated-card">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="fas fa-list mr-2"></i> All Brands</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped datatable">
                        <thead class="thead-dark">
                            <tr>
                                <th>ID</th>
                                <th>Brand Name</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($brand = mysqli_fetch_assoc($result)): ?>
                            <tr class="clickable" onclick="window.location='view_brand.php?id=<?= $brand['brand_id'] ?>'">
                                <td><?= $brand['brand_id'] ?></td>
                                <td>
                                    <div class="font-weight-bold"><?= htmlspecialchars($brand['brand_name']) ?></div>
                                </td>
                                <td>
                                    <?php if (!empty($brand['description'])): ?>
                                        <?= htmlspecialchars($brand['description']) ?>
                                    <?php else: ?>
                                        <span class="text-muted">No description</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="view_brand.php?id=<?= $brand['brand_id'] ?>" class="btn btn-sm btn-info action-btn" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="edit_brand.php?id=<?= $brand['brand_id'] ?>" class="btn btn-sm btn-warning action-btn" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="delete_brand.php?id=<?= $brand['brand_id'] ?>" class="btn btn-sm btn-danger action-btn" onclick="return confirm('Are you sure you want to delete this brand?');" title="Delete">
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
    // Initialize DataTables with enhanced options
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
                searchPlaceholder: "Search brands...",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ brands",
                infoEmpty: "Showing 0 to 0 of 0 brands",
                infoFiltered: "(filtered from _MAX_ total brands)"
            }
        });
    });
</script>

<?php require_once 'includes/footer.php'; ?>