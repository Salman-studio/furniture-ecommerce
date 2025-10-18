<?php
require_once 'includes/header.php';
require_once 'includes/sidebar.php';
require_once 'includes/db_connection.php';

$id = intval($_GET['id'] ?? 0);

// Fetch the order
$sql = "SELECT orders.*, 
               users.username AS user_username, 
               users.first_name AS user_first_name, 
               users.last_name AS user_last_name, 
               users.email AS user_email
        FROM orders
        LEFT JOIN users ON orders.customer_id = users.user_id
        WHERE orders.order_id = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$order = mysqli_fetch_assoc($result);

// Fetch order items
$items_sql = "SELECT order_items.*, products.product_name
              FROM order_items
              JOIN products ON order_items.product_id = products.product_id
              WHERE order_items.order_id = ?";

$items_stmt = mysqli_prepare($conn, $items_sql);
mysqli_stmt_bind_param($items_stmt, 'i', $id);
mysqli_stmt_execute($items_stmt);
$items_result = mysqli_stmt_get_result($items_stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order: <?= htmlspecialchars($order['order_number']) ?></title>
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
<div class="main-content">
    <h2 class="fade-in">Order #<?= htmlspecialchars($order['order_number']) ?></h2>
    <div class="card mb-4 animated-card">
        <div class="card-body">
            <strong>User:</strong> <?= htmlspecialchars($order['user_first_name'] . ' ' . $order['user_last_name']) ?> (<?= htmlspecialchars($order['user_email']) ?>)<br>
            <strong>Date:</strong> <?= htmlspecialchars($order['created_at']) ?><br>
            <strong>Status:</strong> <span class="badge-status"><?= htmlspecialchars($order['order_status']) ?></span><br>
            <strong>Total:</strong> $<?= number_format($order['total_amount'], 2) ?>
        </div>
    </div>

    <h4>Order Items</h4>
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>Product</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php while($item = mysqli_fetch_assoc($items_result)): ?>
            <tr>
                <td><?= htmlspecialchars($item['product_name']) ?></td>
                <td><?= (int)$item['quantity'] ?></td>
                <td>$<?= number_format($item['unit_price'], 2) ?></td>
                <td>$<?= number_format($item['unit_price'] * $item['quantity'], 2) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <a href="orders.php" class="btn btn-secondary btn-animated"><i class="fa fa-arrow-left"></i> Back to Orders</a>
</div>

<?php require_once 'includes/footer.php'; ?>
</body>
</html>