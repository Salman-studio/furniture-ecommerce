<?php
require_once 'includes/header.php';
require_once 'includes/sidebar.php';
require_once 'includes/db_connection.php';

// Get action from URL
$action = $_GET['action'] ?? 'list';
$invoice_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Initialize variables
$error = null;
$success = null;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        // Create new invoice
        $order_id = intval($_POST['order_id']);
        $invoice_number = $_POST['invoice_number'] ?? 'INV-' . date('Ymd-His');
        $issue_date = $_POST['issue_date'];
        $due_date = $_POST['due_date'];
        $amount = floatval($_POST['amount']);
        $tax_amount = floatval($_POST['tax_amount'] ?? 0);
        $status = $_POST['status'];
        $notes = $_POST['notes'] ?? '';

        if (empty($order_id) || empty($invoice_number) || empty($issue_date) || empty($due_date) || empty($amount)) {
            $error = "All required fields must be filled.";
        } else {
            $sql = "INSERT INTO invoices (order_id, invoice_number, issue_date, due_date, total_amount, tax_amount, status, notes) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'isssddss', $order_id, $invoice_number, $issue_date, $due_date, $amount, $tax_amount, $status, $notes);

            if (mysqli_stmt_execute($stmt)) {
                $success = "Invoice created successfully!";
                $action = 'list';
            } else {
                $error = "Error creating invoice: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'update') {
        // Update invoice
        $invoice_id = intval($_POST['invoice_id']);
        $order_id = intval($_POST['order_id']);
        $issue_date = $_POST['issue_date'];
        $due_date = $_POST['due_date'];
        $amount = floatval($_POST['amount']);
        $tax_amount = floatval($_POST['tax_amount'] ?? 0);
        $status = $_POST['status'];
        $notes = $_POST['notes'] ?? '';

        if (empty($order_id) || empty($issue_date) || empty($due_date) || empty($amount)) {
            $error = "All required fields must be filled.";
        } else {
            $sql = "UPDATE invoices SET order_id=?, issue_date=?, due_date=?, total_amount=?, tax_amount=?, status=?, notes=? 
                    WHERE invoice_id=?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'isssddsi', $order_id, $issue_date, $due_date, $amount, $tax_amount, $status, $notes, $invoice_id);

            if (mysqli_stmt_execute($stmt)) {
                $success = "Invoice updated successfully!";
                $action = 'list';
            } else {
                $error = "Error updating invoice: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// Handle delete action
if ($action === 'delete' && $invoice_id > 0) {
    $sql = "DELETE FROM invoices WHERE invoice_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $invoice_id);

    if (mysqli_stmt_execute($stmt)) {
        $success = "Invoice deleted successfully!";
        $action = 'list';
    } else {
        $error = "Error deleting invoice: " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
}

// Add custom CSS for animations (unchanged from original)
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

<?php
// Display content based on action
switch ($action) {
    case 'create':
        // Fetch orders for dropdown
        $orders_sql = "SELECT order_id, order_number, customer_name, final_amount 
                       FROM orders 
                       WHERE order_status IN ('pending', 'processing', 'paid', 'shipped')
                       ORDER BY order_id DESC";
        $orders_result = mysqli_query($conn, $orders_sql);
        if (!$orders_result) {
            $error = "Error fetching orders: " . mysqli_error($conn);
        }
        ?>
        <div class="main-content fade-in">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Create New Invoice</h2>
                <a href="?action=list" class="btn btn-secondary btn-animated">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Invoices
                </a>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($error) ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($success) ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            <?php endif; ?>

            <div class="card animated-card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-file-invoice mr-2"></i> Invoice Details</h5>
                </div>
                <div class="card-body">
                    <form method="POST" style="max-width: 800px;">
                        <input type="hidden" name="action" value="create">

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Select Order *</label>
                                <select name="order_id" required class="form-control select2" style="width: 100%;">
                                    <option value="">Select an Order</option>
                                    <?php while ($order = mysqli_fetch_assoc($orders_result)): ?>
                                        <option value="<?= $order['order_id'] ?>" data-amount="<?= $order['final_amount'] ?>">
                                            #<?= htmlspecialchars($order['order_number']) ?> - <?= htmlspecialchars($order['customer_name']) ?> - ₹<?= number_format($order['final_amount'], 2) ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Invoice Number *</label>
                                <input type="text" name="invoice_number" class="form-control" value="INV-<?= date('Ymd-His') ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Issue Date *</label>
                                <input name="issue_date" type="date" value="<?= date('Y-m-d') ?>" required class="form-control">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Due Date *</label>
                                <input name="due_date" type="date" required class="form-control" id="due_date">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Total Amount (₹) *</label>
                                <div class="input-group">
                                    <span class="input-group-text">₹</span>
                                    <input name="amount" type="number" step="0.01" required class="form-control" id="total_amount">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Tax Amount (₹)</label>
                                <div class="input-group">
                                    <span class="input-group-text">₹</span>
                                    <input name="tax_amount" type="number" step="0.01" value="0.00" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status *</label>
                                <select name="status" required class="form-control">
                                    <option value="unpaid">Unpaid</option>
                                    <option value="paid">Paid</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Notes</label>
                                <textarea name="notes" class="form-control" placeholder="Optional notes..."></textarea>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-success btn-animated mr-2">
                                <i class="fas fa-check-circle mr-2"></i> Create Invoice
                            </button>
                            <a href="?action=list" class="btn btn-secondary btn-animated">
                                <i class="fas fa-times mr-2"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <script>
                document.querySelector('select[name="order_id"]').addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const amount = selectedOption.getAttribute('data-amount');
                    if (amount) {
                        document.getElementById('total_amount').value = parseFloat(amount).toFixed(2);
                    }
                });

                const today = new Date();
                const dueDate = new Date();
                dueDate.setDate(today.getDate() + 30);
                document.getElementById('due_date').value = dueDate.toISOString().split('T')[0];

                $(document).ready(function() {
                    $('.select2').select2({
                        theme: 'bootstrap4'
                    });
                });
            </script>
        </div>
        <?php
        break;

    case 'edit':
        // Fetch invoice data
        $sql = "SELECT i.*, o.order_number, o.customer_name, o.final_amount 
                FROM invoices i 
                LEFT JOIN orders o ON i.order_id = o.order_id 
                WHERE i.invoice_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $invoice_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $invoice = mysqli_fetch_assoc($result);

        // Fetch orders for dropdown
        $orders_sql = "SELECT order_id, order_number, customer_name, final_amount 
                       FROM orders 
                       WHERE order_status IN ('pending', 'processing', 'paid', 'shipped')
                       ORDER BY order_id DESC";
        $orders_result = mysqli_query($conn, $orders_sql);
        if (!$orders_result) {
            $error = "Error fetching orders: " . mysqli_error($conn);
        }
        ?>
        <div class="main-content fade-in">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Edit Invoice #<?= htmlspecialchars($invoice['invoice_number']) ?></h2>
                <a href="?action=list" class="btn btn-secondary btn-animated">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Invoices
                </a>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($error) ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($success) ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            <?php endif; ?>

            <div class="card animated-card">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-edit mr-2"></i> Edit Invoice Details</h5>
                </div>
                <div class="card-body">
                    <form method="POST" style="max-width: 800px;">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="invoice_id" value="<?= $invoice_id ?>">

                        <div class="form-group">
                            <label class="form-label fw-semibold">Invoice Number</label>
                            <input type="text" class="form-control" value="#<?= htmlspecialchars($invoice['invoice_number']) ?>" readonly>
                        </div>

                        <div class="form-group">
                            <label class="form-label fw-semibold">Select Order *</label>
                            <select name="order_id" required class="form-control select2" style="width: 100%;">
                                <option value="">Select an Order</option>
                                <?php while ($order = mysqli_fetch_assoc($orders_result)): ?>
                                    <option value="<?= $order['order_id'] ?>" <?= $order['order_id'] == $invoice['order_id'] ? 'selected' : '' ?>>
                                        #<?= htmlspecialchars($order['order_number']) ?> - <?= htmlspecialchars($order['customer_name']) ?> - ₹<?= number_format($order['final_amount'], 2) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Issue Date *</label>
                                <input name="issue_date" type="date" value="<?= $invoice['issue_date'] ?>" required class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Due Date *</label>
                                <input name="due_date" type="date" value="<?= $invoice['due_date'] ?>" required class="form-control">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Total Amount (₹) *</label>
                                <div class="input-group">
                                    <span class="input-group-text">₹</span>
                                    <input name="amount" type="number" step="0.01" value="<?= $invoice['total_amount'] ?>" required class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tax Amount (₹)</label>
                                <div class="input-group">
                                    <span class="input-group-text">₹</span>
                                    <input name="tax_amount" type="number" step="0.01" value="<?= $invoice['tax_amount'] ?>" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status *</label>
                                <select name="status" required class="form-control">
                                    <option value="unpaid" <?= $invoice['status'] == 'unpaid' ? 'selected' : '' ?>>Unpaid</option>
                                    <option value="paid" <?= $invoice['status'] == 'paid' ? 'selected' : '' ?>>Paid</option>
                                    <option value="cancelled" <?= $invoice['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Notes</label>
                                <textarea name="notes" class="form-control"><?= htmlspecialchars($invoice['notes'] ?? '') ?></textarea>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-success btn-animated mr-2">
                                <i class="fas fa-save mr-2"></i> Update Invoice
                            </button>
                            <a href="?action=list" class="btn btn-secondary btn-animated">
                                <i class="fas fa-times mr-2"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <script>
                $(document).ready(function() {
                    $('.select2').select2({
                        theme: 'bootstrap4'
                    });
                });
            </script>
        </div>
        <?php
        break;

    case 'view':
        // Fetch invoice details
        $sql = "SELECT i.*, o.order_number, o.customer_name, o.customer_email, o.customer_phone 
                FROM invoices i 
                LEFT JOIN orders o ON i.order_id = o.order_id 
                WHERE i.invoice_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $invoice_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $invoice = mysqli_fetch_assoc($result);
        ?>
        <div class="main-content fade-in">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Invoice #<?= htmlspecialchars($invoice['invoice_number']) ?></h2>
                <a href="?action=list" class="btn btn-secondary btn-animated">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Invoices
                </a>
            </div>

            <div class="card animated-card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-file-invoice mr-2"></i> Invoice Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2">Invoice Information</h5>
                            <p><strong>Invoice Number:</strong> #<?= htmlspecialchars($invoice['invoice_number']) ?></p>
                            <p><strong>Order Number:</strong> #<?= htmlspecialchars($invoice['order_number']) ?></p>
                            <p><strong>Issue Date:</strong> <?= date('M j, Y', strtotime($invoice['issue_date'])) ?></p>
                            <p><strong>Due Date:</strong> <?= date('M j, Y', strtotime($invoice['due_date'])) ?></p>
                            <p><strong>Status:</strong> 
                                <span class="badge badge-status badge-<?= 
                                    $invoice['status'] == 'paid' ? 'success' : 
                                    ($invoice['status'] == 'unpaid' ? 'warning' : 'danger') ?>">
                                    <?= ucfirst($invoice['status']) ?>
                                </span>
                            </p>
                            <p><strong>Notes:</strong> <?= htmlspecialchars($invoice['notes'] ?? 'N/A') ?></p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2">Customer Details</h5>
                            <p><strong>Customer:</strong> <?= htmlspecialchars($invoice['customer_name']) ?></p>
                            <p><strong>Email:</strong> <?= htmlspecialchars($invoice['customer_email']) ?></p>
                            <p><strong>Phone:</strong> <?= htmlspecialchars($invoice['customer_phone'] ?? 'N/A') ?></p>
                        </div>
                    </div>

                    <hr>

                    <h5 class="border-bottom pb-2">Payment Details</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Subtotal:</strong> ₹<?= number_format($invoice['total_amount'] - $invoice['tax_amount'], 2) ?></p>
                            <p><strong>Tax Amount:</strong> ₹<?= number_format($invoice['tax_amount'], 2) ?></p>
                            <p><strong>Total Amount:</strong> ₹<?= number_format($invoice['total_amount'], 2) ?></p>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-end">
                        <a href="?action=list" class="btn btn-secondary btn-animated mr-2">
                            <i class="fas fa-arrow-left mr-2"></i> Back to Invoices
                        </a>
                        <a href="?action=print&id=<?= $invoice_id ?>" class="btn btn-info btn-animated" target="_blank">
                            <i class="fas fa-print mr-2"></i> Print Invoice
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php
        break;

    case 'print':
        // Print view
        $sql = "SELECT i.*, o.order_number, o.customer_name, o.customer_email, o.customer_phone 
                FROM invoices i 
                LEFT JOIN orders o ON i.order_id = o.order_id 
                WHERE i.invoice_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $invoice_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $invoice = mysqli_fetch_assoc($result);
        ?>
        <div class="main-content fade-in" style="padding: 20px;">
            <div class="d-flex justify-content-between mb-4">
                <h2 class="mb-0">Invoice #<?= htmlspecialchars($invoice['invoice_number']) ?></h2>
                <div>
                    <button onclick="window.print()" class="btn btn-primary btn-animated">
                        <i class="fas fa-print mr-2"></i> Print
                    </button>
                    <a href="?action=list" class="btn btn-secondary btn-animated">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Invoices
                    </a>
                </div>
            </div>

            <div style="max-width: 800px; margin: 0 auto; border: 1px solid #ddd; padding: 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                <div class="text-center mb-4">
                    <h2 style="color: #007bff;">INVOICE</h2>
                    <h3>#<?= htmlspecialchars($invoice['invoice_number']) ?></h3>
                </div>

                <div style="display: flex; justify-content: space-between; margin-bottom: 30px;">
                    <div style="flex: 1;">
                        <h4>From:</h4>
                        <p>Your Company Name<br>123 Business Street<br>City, State 12345<br>Phone: (123) 456-7890</p>
                    </div>
                    <div style="flex: 1; text-align: right;">
                        <h4>To:</h4>
                        <p><?= htmlspecialchars($invoice['customer_name']) ?><br><?= htmlspecialchars($invoice['customer_email']) ?><br><?= htmlspecialchars($invoice['customer_phone'] ?? '') ?></p>
                    </div>
                </div>

                <div style="margin-bottom: 30px; display: flex; justify-content: space-between;">
                    <div>
                        <p><strong>Invoice Date:</strong> <?= date('F j, Y', strtotime($invoice['issue_date'])) ?></p>
                    </div>
                    <div>
                        <p><strong>Due Date:</strong> <?= date('F j, Y', strtotime($invoice['due_date'])) ?></p>
                    </div>
                </div>

                <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px;">
                    <tr style="background: #f8f9fa;">
                        <th style="padding: 12px; border: 1px solid #ddd; text-align: left;">Description</th>
                        <th style="padding: 12px; border: 1px solid #ddd; text-align: right;">Amount</th>
                    </tr>
                    <tr>
                        <td style="padding: 12px; border: 1px solid #ddd;">Order #<?= htmlspecialchars($invoice['order_number']) ?></td>
                        <td style="padding: 12px; border: 1px solid #ddd; text-align: right;">₹<?= number_format($invoice['total_amount'] - $invoice['tax_amount'], 2) ?></td>
                    </tr>
                    <tr>
                        <td style="padding: 12px; border: 1px solid #ddd;">Tax</td>
                        <td style="padding: 12px; border: 1px solid #ddd; text-align: right;">₹<?= number_format($invoice['tax_amount'], 2) ?></td>
                    </tr>
                    <tr style="font-weight: bold; background: #f8f9fa;">
                        <td style="padding: 12px; border: 1px solid #ddd;">TOTAL</td>
                        <td style="padding: 12px; border: 1px solid #ddd; text-align: right;">₹<?= number_format($invoice['total_amount'], 2) ?></td>
                    </tr>
                </table>

                <?php if (!empty($invoice['notes'])): ?>
                    <div style="margin-bottom: 30px;">
                        <h4>Notes:</h4>
                        <p><?= htmlspecialchars($invoice['notes']) ?></p>
                    </div>
                <?php endif; ?>

                <div style="text-align: center; margin-top: 50px; padding-top: 20px; border-top: 1px solid #eee;">
                    <p>Thank you for your business!</p>
                </div>
            </div>
        </div>
        <?php
        break;

    default:
    case 'list':
        // List all invoices
        $sql = "SELECT i.*, o.order_number, o.customer_name, o.customer_email 
                FROM invoices i 
                LEFT JOIN orders o ON i.order_id = o.order_id 
                ORDER BY i.invoice_id DESC";
        $result = mysqli_query($conn, $sql);
        $total_invoices = mysqli_num_rows($result);

        // Get statistics
        $stats_sql = "SELECT 
            COUNT(*) as total_invoices,
            COUNT(CASE WHEN status = 'paid' THEN 1 END) as paid_invoices,
            COUNT(CASE WHEN status = 'unpaid' THEN 1 END) as unpaid_invoices,
            COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled_invoices,
            COUNT(CASE WHEN status = 'unpaid' AND due_date < NOW() THEN 1 END) as overdue_invoices,
            COALESCE(SUM(CASE WHEN status = 'paid' THEN total_amount ELSE 0 END), 0) as total_revenue
            FROM invoices";
        $stats_result = mysqli_query($conn, $stats_sql);
        $stats = $stats_result ? mysqli_fetch_assoc($stats_result) : [];
        ?>
        <div class="main-content fade-in">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Invoice Management</h2>
                <a href="?action=create" class="btn btn-primary btn-animated">
                    <i class="fas fa-plus-circle mr-2"></i> Create New Invoice
                </a>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($error) ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($success) ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            <?php endif; ?>

            <!-- Quick Stats -->
            <div class="row mb-4">
                <div class="col-md-2 mb-3">
                    <div class="card stat-card bg-primary text-white text-center clickable" onclick="window.location='?action=list'">
                        <div class="card-body">
                            <i class="fas fa-file-invoice fa-2x mb-2"></i>
                            <h6>Total Invoices</h6>
                            <h3><?= $stats['total_invoices'] ?? 0 ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 mb-3">
                    <div class="card stat-card bg-success text-white text-center clickable" onclick="filterByStatus('paid')">
                        <div class="card-body">
                            <i class="fas fa-check-circle fa-2x mb-2"></i>
                            <h6>Paid</h6>
                            <h3><?= $stats['paid_invoices'] ?? 0 ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 mb-3">
                    <div class="card stat-card bg-warning text-white text-center clickable" onclick="filterByStatus('unpaid')">
                        <div class="card-body">
                            <i class="fas fa-clock fa-2x mb-2"></i>
                            <h6>Unpaid</h6>
                            <h3><?= $stats['unpaid_invoices'] ?? 0 ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 mb-3">
                    <div class="card stat-card bg-danger text-white text-center clickable" onclick="filterByStatus('overdue')">
                        <div class="card-body">
                            <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                            <h6>Overdue</h6>
                            <h3><?= $stats['overdue_invoices'] ?? 0 ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 mb-3">
                    <div class="card stat-card bg-info text-white text-center clickable" onclick="filterByStatus('cancelled')">
                        <div class="card-body">
                            <i class="fas fa-ban fa-2x mb-2"></i>
                            <h6>Cancelled</h6>
                            <h3><?= $stats['cancelled_invoices'] ?? 0 ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 mb-3">
                    <div class="card stat-card bg-secondary text-white text-center clickable" onclick="window.location='?action=list'">
                        <div class="card-body">
                            <i class="fas fa-dollar-sign fa-2x mb-2"></i>
                            <h6>Revenue</h6>
                            <h3>₹<?= number_format($stats['total_revenue'] ?? 0, 2) ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card animated-card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-list mr-2"></i> All Invoices (<?= $total_invoices ?>)</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped table-bordered datatable">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Total Amount</th>
                                    <th>Tax Amount</th>
                                    <th>Issue Date</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                                    <?php while ($invoice = mysqli_fetch_assoc($result)):
                                        $is_overdue = $invoice['status'] == 'unpaid' && strtotime($invoice['due_date']) < time();
                                    ?>
                                    <tr class="clickable" onclick="window.location='?action=view&id=<?= $invoice['invoice_id'] ?>'">
                                        <td>#<?= htmlspecialchars($invoice['invoice_number']) ?></td>
                                        <td>#<?= htmlspecialchars($invoice['order_number']) ?></td>
                                        <td>
                                            <div><?= htmlspecialchars($invoice['customer_name']) ?></div>
                                            <small class="text-muted"><?= htmlspecialchars($invoice['customer_email']) ?></small>
                                        </td>
                                        <td>₹<?= number_format($invoice['total_amount'], 2) ?></td>
                                        <td>₹<?= number_format($invoice['tax_amount'], 2) ?></td>
                                        <td><?= date('M j, Y', strtotime($invoice['issue_date'])) ?></td>
                                        <td><?= date('M j, Y', strtotime($invoice['due_date'])) ?></td>
                                        <td>
                                            <span class="badge badge-status badge-<?= 
                                                $invoice['status'] == 'paid' ? 'success' : 
                                                ($invoice['status'] == 'unpaid' ? ($is_overdue ? 'danger' : 'warning') : 'danger') ?>">
                                                <?= ucfirst($invoice['status']) ?>
                                                <?php if ($is_overdue): ?> (Overdue) <?php endif; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="?action=view&id=<?= $invoice['invoice_id'] ?>" class="btn btn-sm btn-info action-btn" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="?action=edit&id=<?= $invoice['invoice_id'] ?>" class="btn btn-sm btn-warning action-btn" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="?action=delete&id=<?= $invoice['invoice_id'] ?>" class="btn btn-sm btn-danger action-btn" onclick="return confirm('Are you sure you want to delete this invoice?');" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                                <a href="?action=print&id=<?= $invoice['invoice_id'] ?>" class="btn btn-sm btn-secondary action-btn" target="_blank" title="Print">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="9" class="text-center text-muted py-4">No invoices found. <a href="?action=create">Create your first invoice</a></td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <script>
                function filterByStatus(status) {
                    // Placeholder for filtering logic
                    alert('Filtering by ' + status + ' status. Implement server-side filtering for production.');
                }

                $(document).ready(function() {
                    $('.datatable').DataTable({
                        responsive: true,
                        ordering: true,
                        searching: true,
                        pageLength: 10,
                        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                        dom: '<"row"<"col-md-6"l><"col-md-6"f>>rtip'
                    });
                });
            </script>
        </div>
        <?php
        break;
}

require_once 'includes/footer.php';
?>