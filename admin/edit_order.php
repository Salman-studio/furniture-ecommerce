<?php
require_once 'includes/header.php';


require_once 'includes/db_connection.php';

$id = intval($_GET['id'] ?? 0);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_status   = $_POST['order_status'] ?? '';
    $payment_status = $_POST['payment_status'] ?? '';
    $notes          = $_POST['notes'] ?? '';

    $sql = "UPDATE orders 
            SET order_status = ?, payment_status = ?, notes = ?, updated_at = NOW() 
            WHERE order_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'sssi', $order_status, $payment_status, $notes, $id);
    mysqli_stmt_execute($stmt);

    // Redirect back to order details
    header("Location: view_order.php?id=" . $id);
    exit;
}

// Fetch the order
$sql = "SELECT * FROM orders WHERE order_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$order = mysqli_fetch_assoc($result);

if (!$order) {
    echo "<div class='alert alert-danger fade-in'>Order not found.</div>";
    require_once 'includes/footer.php';
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Order: <?= htmlspecialchars($order['order_number']) ?></title>
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
    <h2 class="fade-in">Edit Order #<?= htmlspecialchars($order['order_number']) ?></h2>
    <div class="card mb-4 animated-card">
        <div class="card-body">
            <form method="POST">
                <div class="form-group mb-3">
                    <label for="order_status"><strong>Order Status</strong></label>
                    <select name="order_status" id="order_status" class="form-control" required>
                        <option value="Pending"   <?= $order['order_status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="Processing" <?= $order['order_status'] === 'Processing' ? 'selected' : '' ?>>Processing</option>
                        <option value="Shipped"   <?= $order['order_status'] === 'Shipped' ? 'selected' : '' ?>>Shipped</option>
                        <option value="Delivered" <?= $order['order_status'] === 'Delivered' ? 'selected' : '' ?>>Delivered</option>
                        <option value="Cancelled" <?= $order['order_status'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label for="payment_status"><strong>Payment Status</strong></label>
                    <select name="payment_status" id="payment_status" class="form-control" required>
                        <option value="Pending"   <?= $order['payment_status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="Paid"      <?= $order['payment_status'] === 'Paid' ? 'selected' : '' ?>>Paid</option>
                        <option value="Refunded"  <?= $order['payment_status'] === 'Refunded' ? 'selected' : '' ?>>Refunded</option>
                        <option value="Failed"    <?= $order['payment_status'] === 'Failed' ? 'selected' : '' ?>>Failed</option>
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label for="notes"><strong>Admin Notes</strong></label>
                    <textarea name="notes" id="notes" class="form-control" rows="4"><?= htmlspecialchars($order['notes']) ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-animated"><i class="fa fa-save"></i> Save Changes</button>
                <a href="view_order.php?id=<?= $id ?>" class="btn btn-secondary btn-animated"><i class="fa fa-arrow-left"></i> Cancel</a>
            </form>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
</body>
</html>