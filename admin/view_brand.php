<?php
require_once 'includes/header.php';
require_once 'includes/sidebar.php';
require_once 'includes/db_connection.php';

// Get brand_id from URL
$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: brands.php');
    exit();
}

// Fetch brand details
$sql = "SELECT * FROM brands WHERE brand_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$brand = mysqli_fetch_assoc($result);

if (!$brand) {
    echo "<div class='main-content container mt-4'>
            <div class='alert alert-danger fade-in animated-card'>Brand not found.</div>
          </div>";
    require_once 'includes/footer.php';
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brand: <?= htmlspecialchars($brand['brand_name']) ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        .btn-animated {
            transition: all 0.3s ease;
            border-radius: 6px;
        }
        .btn-animated:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        .action-btn {
            transition: all 0.2s ease;
            border-radius: 5px;
            margin: 0 2px;
        }
        .action-btn:hover {
            transform: scale(1.1);
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
<div class="main-content container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4 fade-in">
        <h2>View Brand</h2>
        <a href="brands.php" class="btn btn-secondary btn-animated"><i class="fas fa-arrow-left"></i> Back to Brands</a>
    </div>

    <div class="card mb-4 animated-card">
        <div class="card-body">
            <h3 class="fade-in"><?= htmlspecialchars($brand['brand_name']) ?></h3>
            <p><strong>ID:</strong> <?= $brand['brand_id'] ?></p>
            <?php if (!empty($brand['description'])): ?>
                <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($brand['description'])) ?></p>
            <?php else: ?>
                <p><strong>Description:</strong> <span class="text-muted">No description</span></p>
            <?php endif; ?>
            <?php if (!empty($brand['created_at'])): ?>
                <p><strong>Created At:</strong> <?= htmlspecialchars($brand['created_at']) ?></p>
            <?php endif; ?>
            <?php if (!empty($brand['updated_at'])): ?>
                <p><strong>Updated At:</strong> <?= htmlspecialchars($brand['updated_at']) ?></p>
            <?php endif; ?>

            <hr>
            <div class="d-flex justify-content-end">
                <a href="edit_brand.php?id=<?= $brand['brand_id'] ?>" class="btn btn-warning btn-animated action-btn me-2">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="delete_brand.php?id=<?= $brand['brand_id'] ?>" class="btn btn-danger btn-animated action-btn" onclick="return confirm('Are you sure you want to delete this brand?');">
                    <i class="fas fa-trash"></i> Delete
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php require_once 'includes/footer.php'; ?>
</body>
</html>