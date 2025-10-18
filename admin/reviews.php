<?php
ob_start(); // Start output buffering
require_once 'includes/header.php';
require_once 'includes/sidebar.php';
require_once 'includes/db_connection.php';

// Fetch reviews with product information
$sql = "SELECT reviews.*, products.product_name
        FROM reviews
        LEFT JOIN products ON reviews.product_id = products.product_id
        ORDER BY reviews.review_id DESC";

$result = mysqli_query($conn, $sql);

// Check if query was successful
if (!$result) {
    die("Error: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <style>
        :root {
            --primary-color: #4e73df;
            --success-color: #1cc88a;
            --danger-color: #e74a3b;
            --warning-color: #f6c23e;
            --info-color: #36b9cc;
            --dark-color: #5a5c69;
            --light-color: #f8f9fc;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fc;
        }
        .main-content {
            padding: 30px;
            max-width: 1400px;
            margin: 0 auto;
        }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e3e6f0;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            border: none;
            margin-bottom: 30px;
        }
        .card-header {
            background-color: white;
            border-bottom: 1px solid #e3e6f0;
            padding: 15px 20px;
            font-weight: 600;
            color: var(--dark-color);
            border-radius: 10px 10px 0 0 !important;
        }
        .table-container {
            overflow: hidden;
            border-radius: 10px;
        }
        .table {
            margin-bottom: 0;
        }
        .table th {
            background-color: var(--light-color);
            font-weight: 600;
            color: var(--dark-color);
            padding: 12px 15px;
        }
        .table td {
            padding: 12px 15px;
            vertical-align: middle;
        }
        .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-success {
            background: linear-gradient(135deg, var(--success-color), #17a673);
            border: none;
        }
        .btn-danger {
            background: linear-gradient(135deg, var(--danger-color), #c03527);
            border: none;
        }
        .btn-success:hover, .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
        .alert {
            border-radius: 10px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        }
        .form-select {
            border-radius: 8px;
            border: 1px solid #d1d3e2;
            transition: all 0.3s ease;
            cursor: pointer;
            padding: 6px 12px;
            font-size: 0.875rem;
        }
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }
        .rating-stars {
            color: #ffc107;
            margin-right: 5px;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .status-approved {
            background-color: rgba(28, 200, 138, 0.2);
            color: var(--success-color);
        }
        .status-pending {
            background-color: rgba(246, 194, 62, 0.2);
            color: var(--warning-color);
        }
        .status-rejected {
            background-color: rgba(231, 74, 59, 0.2);
            color: var(--danger-color);
        }
        .animate__animated.animate__fadeInUp {
            --animate-duration: 0.5s;
        }
        .dataTables_wrapper {
            padding: 0 5px;
        }
        .dataTables_info, .dataTables_paginate {
            padding: 15px 0;
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
    <div class="page-header animate__animated animate__fadeInUp">
        <h2 class="m-0">Reviews Management</h2>
        <div class="btn-group">
            <button type="button" class="btn btn-primary">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
        </div>
    </div>
    
    <div class="card animate__animated animate__fadeInUp">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-star me-2"></i>Customer Reviews</span>
            <span class="badge bg-primary"><?php echo mysqli_num_rows($result); ?> Reviews</span>
        </div>
        <div class="card-body">
            <?php if (mysqli_num_rows($result) == 0): ?>
                <div class="alert alert-info text-center animate__animated animate__fadeIn">
                    <i class="fas fa-info-circle fa-2x mb-3"></i>
                    <h4>No Reviews Found</h4>
                    <p class="mb-0">There are no customer reviews in the database yet.</p>
                </div>
            <?php else: ?>
                <div class="table-container">
                    <table class="table table-hover datatable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Product</th>
                                <th>Customer</th>
                                <th>Email</th>
                                <th>Rating</th>
                                <th>Review</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($rev = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?= $rev['review_id'] ?></td>
                                <td><?= htmlspecialchars($rev['product_name'] ?? 'Product ID: ' . $rev['product_id']) ?></td>
                                <td><?= htmlspecialchars($rev['customer_name']) ?></td>
                                <td><a href="mailto:<?= $rev['customer_email'] ?>"><?= htmlspecialchars($rev['customer_email']) ?></a></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="rating-stars">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star<?= $i <= $rev['rating'] ? '' : '-half-alt' ?>" style="color: <?= $i <= $rev['rating'] ? '#ffc107' : '#ddd' ?>"></i>
                                            <?php endfor; ?>
                                        </span>
                                        <span class="ms-2">(<?= $rev['rating'] ?>)</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="review-text-truncate" data-bs-toggle="tooltip" title="<?= htmlspecialchars($rev['review_text']) ?>">
                                        <?= strlen($rev['review_text']) > 50 ? substr(htmlspecialchars($rev['review_text']), 0, 50) . '...' : htmlspecialchars($rev['review_text']) ?>
                                    </div>
                                </td>
                                <td>
                                    <select class="form-select status-select" data-review-id="<?= $rev['review_id'] ?>" id="status_<?= $rev['review_id'] ?>">
                                        <option value="approved" <?= $rev['status'] === 'approved' ? 'selected' : '' ?>>Approved</option>
                                        <option value="pending" <?= $rev['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="rejected" <?= $rev['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                    </select>
                                </td>
                                <td><?= date('M j, Y g:i A', strtotime($rev['created_at'])) ?></td>
                                <td>
                                    <a href="delete_review.php?id=<?= $rev['review_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this review?');">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize DataTables
    $('.datatable').DataTable({
        "pageLength": 10,
        "order": [[0, "desc"]],
        "responsive": true,
        "language": {
            "search": "_INPUT_",
            "searchPlaceholder": "Search reviews...",
            "lengthMenu": "Show _MENU_ entries",
            "info": "Showing _START_ to _END_ of _TOTAL_ entries",
            "paginate": {
                "previous": "<i class='fas fa-chevron-left'></i>",
                "next": "<i class='fas fa-chevron-right'></i>"
            }
        },
        "columnDefs": [
            { "responsivePriority": 1, "targets": 0 },
            { "responsivePriority": 2, "targets": -1 }
        ]
    });

    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();

    // Handle status change
    $('.status-select').on('change', function() {
        var reviewId = $(this).data('review-id');
        var newStatus = $(this).val();
        var selectElement = $(this);

        $.ajax({
            url: 'update_review_status.php',
            method: 'POST',
            data: {
                review_id: reviewId,
                status: newStatus
            },
            success: function(response) {
                try {
                    var result = JSON.parse(response);
                    if (result.success) {
                        // Show success notification
                        $('body').append('<div class="alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3" style="z-index: 9999; min-width: 300px;" role="alert">Status updated successfully!<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                        
                        // Auto-close alert after 3 seconds
                        setTimeout(function() {
                            $('.alert-success').alert('close');
                        }, 3000);
                    } else {
                        alert('Error: ' + result.error);
                        // Revert dropdown on error
                        selectElement.val(result.current_status);
                    }
                } catch (e) {
                    alert('Error processing response.');
                }
            },
            error: function() {
                alert('Error updating status.');
                selectElement.val(selectElement.data('previous-value'));
            }
        });
    });

    // Store previous value before change
    $('.status-select').on('focus', function() {
        $(this).data('previous-value', $(this).val());
    });
});
</script>
<?php ob_end_flush(); // Flush output buffer ?>
</body>
</html>
<?php require_once 'includes/footer.php'; ?>