<?php
require_once 'includes/header.php';
require_once 'includes/sidebar.php';
require_once 'includes/db_connection.php';

// Fetch users safely with prepared statement
$sql = "SELECT * FROM users ORDER BY user_id DESC";
$result = mysqli_query($conn, $sql);

// Check for errors
if (!$result) {
    die("Error: " . mysqli_error($conn));
}

// Get user statistics (removed is_active references)
$stats_sql = "SELECT 
    COUNT(*) as total_users,
    COUNT(CASE WHEN role = 'admin' THEN 1 END) as admin_users,
    COUNT(CASE WHEN role = 'user' THEN 1 END) as regular_users
    FROM users";
$stats_result = mysqli_query($conn, $stats_sql);
$stats = $stats_result ? mysqli_fetch_assoc($stats_result) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
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
        .stat-card {
            transition: all 0.3s ease;
            border-radius: 10px;
            overflow: hidden;
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.15);
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
        .badge-status {
            padding: 6px 10px;
            border-radius: 20px;
            font-weight: 500;
        }
        .table-hover tbody tr {
            transition: all 0.2s ease;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(0,123,255,0.05);
            transform: scale(1.01);
        }
        .action-btn {
            transition: all 0.2s ease;
            border-radius: 5px;
            margin: 0 2px;
        }
        .action-btn:hover {
            transform: scale(1.1);
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            background-color: #007bff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 16px;
        }
        .user-row {
            transition: all 0.2s ease;
        }
        .user-row:hover {
            cursor: pointer;
            background-color: rgba(0,123,255,0.05);
        }
        .main-content {
            padding: 20px;
            margin-left: 250px;
        }
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="main-content fade-in">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">User Management</h2>
            <a href="add_user.php" class="btn btn-primary btn-animated">
                <i class="fas fa-user-plus mr-2"></i> Add New User
            </a>
        </div>
        
        <!-- User Statistics -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card stat-card bg-primary text-white text-center clickable" onclick="filterUsers('all')">
                    <div class="card-body">
                        <i class="fas fa-users fa-2x mb-2"></i>
                        <h6>Total Users</h6>
                        <h3><?= $stats['total_users'] ?? 0 ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stat-card bg-danger text-white text-center clickable" onclick="filterUsers('admin')">
                    <div class="card-body">
                        <i class="fas fa-crown fa-2x mb-2"></i>
                        <h6>Admin Users</h6>
                        <h3><?= $stats['admin_users'] ?? 0 ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stat-card bg-info text-white text-center clickable" onclick="filterUsers('user')">
                    <div class="card-body">
                        <i class="fas fa-user fa-2x mb-2"></i>
                        <h6>Regular Users</h6>
                        <h3><?= $stats['regular_users'] ?? 0 ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stat-card bg-secondary text-white text-center clickable" onclick="window.location='add_user.php'">
                    <div class="card-body">
                        <i class="fas fa-plus-circle fa-2x mb-2"></i>
                        <h6>Add New</h6>
                        <h3>+</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card animated-card">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="fas fa-users mr-2"></i> All Users</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped datatable">
                        <thead class="thead-dark">
                            <tr>
                                <th>Avatar</th>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php while($user = mysqli_fetch_assoc($result)): 
                                    $role_class = $user['role'] == 'admin' ? 'danger' : 'info';
                                    $initials = strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1));
                                ?>
                                <tr class="user-row" onclick="window.location='view_user.php?id=<?= $user['user_id'] ?>'">
                                    <td>
                                        <div class="user-avatar">
                                            <?= $initials ?>
                                        </div>
                                    </td>
                                    <td><?= $user['user_id'] ?></td>
                                    <td>
                                        <div class="font-weight-bold"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></div>
                                        <?php if (!empty($user['created_at'])): ?>
                                        <small class="text-muted">Joined: <?= date('M j, Y', strtotime($user['created_at'])) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td>
                                        <span class="badge badge-<?= $role_class ?> badge-status">
                                            <?= ucfirst(htmlspecialchars($user['role'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="view_user.php?id=<?= $user['user_id'] ?>" class="btn btn-sm btn-info action-btn" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="edit_user.php?id=<?= $user['user_id'] ?>" class="btn btn-sm btn-warning action-btn" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="delete_user.php?id=<?= $user['user_id'] ?>" class="btn btn-sm btn-danger action-btn" onclick="return confirm('Are you sure you want to delete this user?');" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        <i class="fas fa-users fa-4x mb-3"></i>
                                        <h5>No Users Found</h5>
                                        <p>Get started by adding your first user to the system</p>
                                        <a href="add_user.php" class="btn btn-primary btn-animated mt-2">
                                            <i class="fas fa-user-plus mr-2"></i> Add Your First User
                                        </a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

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
                    searchPlaceholder: "Search users...",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ users",
                    infoEmpty: "Showing 0 to 0 of 0 users",
                    infoFiltered: "(filtered from _MAX_ total users)"
                }
            });
        });
        
        // Function to filter users by status (would need backend implementation)
        function filterUsers(filter) {
            alert('Filtering by ' + filter + '. This feature would need proper implementation.');
        }
    </script>
</body>
</html>