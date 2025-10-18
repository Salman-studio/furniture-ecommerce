<?php 
require_once 'includes/sidebar.php';
require_once 'includes/db_connection.php';

// Initialize variables
$error = null;
$result = null;
$total_orders = 0;
$completed_orders = 0;
$pending_orders = 0;
$processing_orders = 0;
$cancelled_orders = 0;
$total_revenue = 0;

// Simple query - all customer data is already in orders table
$sql = "SELECT * FROM orders ORDER BY order_id DESC";
$result = mysqli_query($conn, $sql);

if (!$result) {
    $error = "Error fetching orders: " . mysqli_error($conn);
} else {
    // Get statistics only if the main query was successful
    $stats_sql = "SELECT 
        COUNT(*) as total_orders,
        COUNT(CASE WHEN order_status = 'completed' THEN 1 END) as completed_orders,
        COUNT(CASE WHEN order_status = 'pending' THEN 1 END) as pending_orders,
        COUNT(CASE WHEN order_status = 'processing' THEN 1 END) as processing_orders,
        COUNT(CASE WHEN order_status = 'cancelled' THEN 1 END) as cancelled_orders,
        COALESCE(SUM(CASE WHEN order_status = 'completed' THEN final_amount ELSE 0 END), 0) as total_revenue
        FROM orders";
    
    $stats_result = mysqli_query($conn, $stats_sql);
    if ($stats_result) {
        $stats = mysqli_fetch_assoc($stats_result);
        $total_orders = $stats['total_orders'];
        $completed_orders = $stats['completed_orders'];
        $pending_orders = $stats['pending_orders'];
        $processing_orders = $stats['processing_orders'];
        $cancelled_orders = $stats['cancelled_orders'];
        $total_revenue = $stats['total_revenue'];
    } else {
        $error = "Error fetching statistics: " . mysqli_error($conn);
    }
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
        padding: 8px 12px;
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
    .order-row {
        transition: all 0.2s ease;
    }
    .order-row:hover {
        cursor: pointer;
        background-color: rgba(0,123,255,0.05);
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
        <h2 class="mb-0">Order Management</h2>
        <a href="create_order.php" class="btn btn-primary btn-animated">
            <i class="fas fa-plus-circle mr-2"></i> Create New Order
        </a>
    </div>
    
    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $error ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php else: ?>
        <!-- Quick Stats -->
        <div class="row mb-4">
            <div class="col-md-2 mb-3">
                <div class="card stat-card bg-primary text-white text-center clickable" onclick="window.location='?filter=all'">
                    <div class="card-body">
                        <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                        <h6>Total Orders</h6>
                        <h3><?= number_format($total_orders) ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-2 mb-3">
                <div class="card stat-card bg-success text-white text-center clickable" onclick="window.location='?filter=paid'">
                    <div class="card-body">
                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                        <h6>Completed</h6>
                        <h3><?= number_format($completed_orders) ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-2 mb-3">
                <div class="card stat-card bg-warning text-white text-center clickable" onclick="window.location='?filter=pending'">
                    <div class="card-body">
                        <i class="fas fa-clock fa-2x mb-2"></i>
                        <h6>Pending</h6>
                        <h3><?= number_format($pending_orders) ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-2 mb-3">
                <div class="card stat-card bg-info text-white text-center clickable" onclick="window.location='?filter=processing'">
                    <div class="card-body">
                        <i class="fas fa-cog fa-2x mb-2"></i>
                        <h6>Processing</h6>
                        <h3><?= number_format($processing_orders) ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-2 mb-3">
                <div class="card stat-card bg-danger text-white text-center clickable" onclick="window.location='?filter=cancelled'">
                    <div class="card-body">
                        <i class="fas fa-times-circle fa-2x mb-2"></i>
                        <h6>Cancelled</h6>
                        <h3><?= number_format($cancelled_orders) ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-2 mb-3">
                <div class="card stat-card bg-secondary text-white text-center clickable" onclick="window.location='?filter=revenue'">
                    <div class="card-body">
                       <i class="fas fa-rupee-sign fa-2x mb-2"></i>
                        <h6>Total Revenue</h6>
                       <h3>₹<?= number_format($total_revenue, 2) ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="card animated-card">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="fas fa-list mr-2"></i> All Orders</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped datatable">
                        <thead class="thead-dark">
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Order Date</th>
                                <th>Total Amount</th>
                                <th>Payment Method</th>
                                <th>Payment Status</th>
                                <th>Order Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                                <?php while($order = mysqli_fetch_assoc($result)): ?>
                                <tr class="order-row" onclick="window.location='view_order.php?id=<?= $order['order_id'] ?>'">
                                    <td class="font-weight-bold">#<?= htmlspecialchars($order['order_number']) ?></td>
                                    <td>
                                        <div class="font-weight-bold"><?= htmlspecialchars($order['customer_name']) ?></div>
                                        <small class="text-muted">ID: <?= $order['order_id'] ?></small>
                                    </td>
                                    <td><?= htmlspecialchars($order['customer_email']) ?></td>
                                    <td><?= htmlspecialchars($order['customer_phone'] ?? 'N/A') ?></td>
                                    <td><?= date('M j, Y', strtotime($order['created_at'])) ?></td>
                                    <td class="font-weight-bold">₹<?= number_format($order['total_amount'], 2) ?></td>
                                    <td>
                                        <span class="badge badge-info badge-status"><?= ucfirst($order['payment_method'] ?? 'N/A') ?></span>
                                    </td>
                                    <td>
                                        <span class="badge badge-status badge-<?= 
                                            ($order['payment_status'] ?? '') == 'paid' ? 'success' : 
                                            (($order['payment_status'] ?? '') == 'pending' ? 'warning' : 'danger')
                                        ?>">
                                            <?= ucfirst($order['payment_status'] ?? 'unknown') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-status badge-<?= 
                                            ($order['order_status'] ?? '') == 'completed' ? 'success' : 
                                            (($order['order_status'] ?? '') == 'processing' ? 'info' : 
                                            (($order['order_status'] ?? '') == 'pending' ? 'warning' : 'danger'))
                                        ?>">
                                            <?= ucfirst($order['order_status'] ?? 'unknown') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="view_order.php?id=<?= $order['order_id'] ?>" 
                                               class="btn btn-sm btn-info action-btn" 
                                               title="View Order">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="edit_order.php?id=<?= $order['order_id'] ?>" 
                                               class="btn btn-sm btn-warning action-btn" 
                                               title="Edit Order">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="delete_order.php?id=<?= $order['order_id'] ?>" 
                                               class="btn btn-sm btn-danger action-btn" 
                                               onclick="return confirm('Are you sure you want to delete this order?');"
                                               title="Delete Order">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-5">
                                        <i class="fas fa-inbox fa-4x mb-3"></i>
                                        <h5>No Orders Found</h5>
                                        <p>Get started by creating your first order</p>
                                        <a href="create_order.php" class="btn btn-primary btn-animated mt-2">
                                            <i class="fas fa-plus-circle mr-2"></i> Create Your First Order
                                        </a>
                                    </td>
                                </tr>
                            <?php endif; ?>
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
                searchPlaceholder: "Search orders...",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ orders",
                infoEmpty: "Showing 0 to 0 of 0 orders",
                infoFiltered: "(filtered from _MAX_ total orders)"
            }
        });
    });
    
    // Function to filter orders by status (would need backend implementation)
    function filterOrders(status) {
        alert('Filtering by ' + status + ' status. This feature would need proper implementation.');
    }
</script>

<?php require_once 'includes/footer.php'; ?>