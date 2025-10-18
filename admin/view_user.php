
<?php
require_once 'includes/header.php';
require_once 'includes/sidebar.php';
require_once 'includes/db_connection.php';

$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    echo "<div class='main-content fade-in'><p class='text-danger'>Invalid user ID.</p></div>";
    require_once 'includes/footer.php';
    exit;
}

// Fetch the user
$sql = "SELECT user_id, username, first_name, last_name, email, created_at, role
        FROM users
        WHERE user_id = ?";

$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    echo "<div class='main-content fade-in'><p class='text-danger'>Database error: " . mysqli_error($conn) . "</p></div>";
    require_once 'includes/footer.php';
    exit;
}
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    echo "<div class='main-content fade-in'><p class='text-danger'>User not found.</p></div>";
    require_once 'includes/footer.php';
    exit;
}

// Fetch user's orders
$orders_sql = "SELECT order_id, order_number, created_at, order_status, total_amount
               FROM orders
               WHERE customer_id = ?";

$orders_stmt = mysqli_prepare($conn, $orders_sql);
if (!$orders_stmt) {
    echo "<div class='main-content fade-in'><p class='text-danger'>Database error: " . mysqli_error($conn) . "</p></div>";
    require_once 'includes/footer.php';
    exit;
}
mysqli_stmt_bind_param($orders_stmt, 'i', $id);
mysqli_stmt_execute($orders_stmt);
$orders_result = mysqli_stmt_get_result($orders_stmt);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User: <?= htmlspecialchars($user['username']) ?></title>
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
        .table-hover tbody tr:hover {
            background-color: rgba(0,123,255,0.05);
            transform: scale(1.01);
        }
        .btn-animated {
            transition: all 0.3s ease;
            border-radius: 6px;
        }
        .btn-animated:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
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
    <div class="main-content fade-in">
        <h2 class="mb-4">User: <?= htmlspecialchars($user['username']) ?></h2>
        <div class="card animated-card mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="fas fa-user mr-2"></i> User Details</h5>
            </div>
            <div class="card-body">
                <strong>Full Name:</strong> <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?><br>
                <strong>Email:</strong> <?= htmlspecialchars($user['email']) ?><br>
                <strong>Role:</strong> 
                <span class="badge badge-<?= $user['role'] == 'admin' ? 'danger' : 'info' ?> badge-status">
                    <?= ucfirst(htmlspecialchars($user['role'])) ?>
                </span><br>
                <strong>Joined:</strong> <?= htmlspecialchars(date('M j, Y', strtotime($user['created_at']))) ?><br>
            </div>
        </div>

        <h4>Order History</h4>
        <?php if (mysqli_num_rows($orders_result) === 0): ?>
            <p class="text-muted">No orders found for this user.</p>
        <?php else: ?>
            <div class="card animated-card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Order Number</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($order = mysqli_fetch_assoc($orders_result)): ?>
                                <tr>
                                    <td><?= htmlspecialchars($order['order_number']) ?></td>
                                    <td><?= htmlspecialchars(date('M j, Y', strtotime($order['created_at']))) ?></td>
                                    <td><?= htmlspecialchars($order['order_status']) ?></td>
                                    <td>$<?= number_format($order['total_amount'], 2) ?></td>
                                    <td><a href="order_view.php?id=<?= (int)$order['order_id'] ?>" class="btn btn-sm btn-primary btn-animated">View</a></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <a href="users.php" class="btn btn-secondary btn-animated mt-3"><i class="fa fa-arrow-left"></i> Back to Users</a>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
mysqli_stmt_close($stmt);
mysqli_stmt_close($orders_stmt);
mysqli_close($conn);
require_once 'includes/footer.php';
?>
