<?php
require_once 'includes/header.php';
require_once 'includes/sidebar.php';
require_once 'includes/db_connection.php';

// Fetch subcategories (with category name)
$sql = "SELECT subcategories.*, categories.category_name AS category_name FROM subcategories 
        JOIN categories ON subcategories.category_id = categories.category_id
        ORDER BY subcategories.subcategory_id DESC";
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
    <title>Subcategories Management</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    
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
        
        .page-header {
            margin-bottom: 30px;
            border-bottom: 1px solid #e3e6f0;
            padding-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .btn-primary {
            background: linear-gradient(to right, var(--primary-color), var(--dark-color));
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(78, 115, 223, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(78, 115, 223, 0.4);
        }
        
        .table-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            padding: 20px;
        }
        
        .table thead th {
            background: linear-gradient(to right, var(--primary-color), var(--dark-color));
            color: white;
            font-weight: 500;
            border: none;
            padding: 15px;
        }
        
        .table tbody tr {
            transition: all 0.3s ease;
        }
        
        .table tbody tr:hover {
            background-color: rgba(78, 115, 223, 0.05);
            transform: translateX(5px);
        }
        
        .btn-sm {
            border-radius: 6px;
            padding: 5px 12px;
            font-size: 0.875rem;
            transition: all 0.3s ease;
        }
        
        .btn-warning {
            background: linear-gradient(to right, #f6c23e, #dda20a);
            border: none;
            box-shadow: 0 2px 8px rgba(246, 194, 62, 0.3);
        }
        
        .btn-danger {
            background: linear-gradient(to right, #e74a3b, #be2617);
            border: none;
            box-shadow: 0 2px 8px rgba(231, 74, 59, 0.3);
        }
        
        .btn-warning:hover, .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
        
        .alert-info {
            background: linear-gradient(to right, var(--accent-color), #2c9faf);
            color: white;
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(54, 185, 204, 0.3);
        }
        
        /* Animation for table rows */
        .table tbody tr {
            animation: fadeInUp 0.5s ease forwards;
            opacity: 0;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Staggered animation for table rows */
        .table tbody tr:nth-child(1) { animation-delay: 0.1s; }
        .table tbody tr:nth-child(2) { animation-delay: 0.2s; }
        .table tbody tr:nth-child(3) { animation-delay: 0.3s; }
        .table tbody tr:nth-child(4) { animation-delay: 0.4s; }
        .table tbody tr:nth-child(5) { animation-delay: 0.5s; }
        .table tbody tr:nth-child(n+6) { animation-delay: 0.6s; }
        
        /* Custom DataTables styling */
        .dataTables_filter input {
            border-radius: 20px;
            padding: 5px 15px;
            border: 1px solid #d1d3e2;
        }
        
        .dataTables_length select {
            border-radius: 20px;
            padding: 5px 15px;
            border: 1px solid #d1d3e2;
        }
        
        .dataTables_paginate .paginate_button {
            border-radius: 8px !important;
            margin: 0 3px;
            border: 1px solid #d1d3e2 !important;
        }
        
        .dataTables_paginate .paginate_button.current {
            background: linear-gradient(to right, var(--primary-color), var(--dark-color)) !important;
            color: white !important;
            border: none !important;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .main-content {
                padding: 15px;
            }
            
            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .page-header .btn {
                margin-top: 15px;
            }
            
            .table-container {
                padding: 10px;
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
    <div class="page-header">
        <h2 class="animate__animated animate__fadeInDown">Subcategories Management</h2>
        <a href="add_subcategory.php" class="btn btn-primary animate__animated animate__fadeIn">
            <i class="fas fa-plus-circle me-2"></i> Add Subcategory
        </a>
    </div>
    
    <?php if (mysqli_num_rows($result) == 0): ?>
        <div class="alert alert-info animate__animated animate__fadeIn">
            <i class="fas fa-info-circle me-2"></i> No subcategories found. Add your first subcategory!
        </div>
    <?php else: ?>
        <div class="table-container animate__animated animate__fadeInUp">
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Subcategory Name</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($sub = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $sub['subcategory_id'] ?></td>
                        <td><?= htmlspecialchars($sub['subcategory_name']) ?></td>
                        <td>
                            <span class="badge bg-primary"><?= htmlspecialchars($sub['category_name']) ?></span>
                        </td>
                        <td>
                            <?php if (!empty($sub['description'])): ?>
                                <?= htmlspecialchars($sub['description']) ?>
                            <?php else: ?>
                                <span class="text-muted">No description</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="edit_subcategory.php?id=<?= $sub['subcategory_id'] ?>" class="btn btn-sm btn-warning me-1">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </a>
                                <a href="delete_subcategory.php?id=<?= $sub['subcategory_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this subcategory?');">
                                    <i class="fas fa-trash me-1"></i> Delete
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Bootstrap 5 JS and Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    // Initialize DataTable
    $(document).ready(function() {
        $('.datatable').DataTable({
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search subcategories...",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                paginate: {
                    previous: "<i class='fas fa-chevron-left'></i>",
                    next: "<i class='fas fa-chevron-right'></i>"
                }
            },
            dom: '<"row"<"col-md-6"l><"col-md-6"f>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
            drawCallback: function() {
                    // Add animations to newly created rows after page change
                    $('tbody tr').css('opacity', '0').addClass('animate__animated animate__fadeInUp');
                    setTimeout(function() {
                        $('tbody tr').css('opacity', '1');
                    }, 100);
                }
        });
        
        // Add animation to search and filter elements
        $('.dataTables_filter input, .dataTables_length select').addClass('animate__animated animate__fadeIn');
        
        // Add hover effect to table rows
        $('.table tbody tr').hover(
            function() {
                $(this).addClass('animate__animated animate__pulse');
            },
            function() {
                $(this).removeClass('animate__animated animate__pulse');
            }
        );
    });
    
    // Add confirmation to delete buttons
    document.querySelectorAll('.btn-danger').forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this subcategory?')) {
                e.preventDefault();
            }
        });
    });
</script>
</body>
</html>
<?php require_once 'includes/footer.php'; ?>