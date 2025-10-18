<?php
require_once 'includes/header.php';
require_once 'includes/sidebar.php';
require_once 'includes/db_connection.php';

// Fetch categories
$sql = "SELECT * FROM categories ORDER BY category_id DESC";
$result = mysqli_query($conn, $sql);
?>

<div class="main-content p-4">
    <!-- Page Title -->
    <div class="d-flex justify-content-between align-items-center mb-4 animate__animated animate__fadeInDown">
        <h2 class="fw-bold text-dark">📂 Categories</h2>
        <a href="add_category.php" class="btn btn-primary shadow-sm animate__animated animate__pulse animate__infinite">
            <i class="fa fa-plus"></i> Add Category
        </a>
    </div>

    <!-- Categories Table -->
    <div class="card shadow-sm animate__animated animate__fadeInUp">
        <div class="card-body">
            <table class="table table-hover table-bordered datatable align-middle">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">#ID</th>
                        <th scope="col">Name</th>
                        <th scope="col">Description</th>
                        <th scope="col" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($cat = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= $cat['category_id'] ?></td>
                            <td class="fw-semibold"><?= htmlspecialchars($cat['category_name']) ?></td>
                            <td><?= htmlspecialchars($cat['description']) ?></td>
                            <td class="text-center">
                                <a href="edit_category.php?id=<?= $cat['category_id'] ?>" 
                                   class="btn btn-sm btn-warning me-1 animate__animated animate__fadeInLeft">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                                <a href="delete_category.php?id=<?= $cat['category_id'] ?>" 
                                   class="btn btn-sm btn-danger animate__animated animate__fadeInRight"
                                   onclick="return confirm('Delete this category?');">
                                    <i class="fa fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

<style>
    body {
        font-family: 'Poppins', sans-serif;
    }
    .main-content h2 {
        font-size: 24px;
        color: #2c3e50;
    }
    .btn-primary {
        background: #34495e;
        border: none;
    }
    .btn-primary:hover {
        background: #2c3e50;
        transform: scale(1.05);
        transition: 0.3s ease-in-out;
    }
    .table thead th {
        background: #2c3e50 !important;
        color: #ecf0f1;
        font-weight: 600;
    }
    .table tbody tr:hover {
        background: #f9f9f9;
        transform: scale(1.01);
        transition: 0.2s ease-in-out;
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
