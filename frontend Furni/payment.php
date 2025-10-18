<?php
session_start();
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/middleware/auth.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Please log in to continue.";
    header('Location: login.php');
    exit;
}

$conn = include __DIR__ . '/includes/db_connection.php';
$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    $_SESSION['error'] = "Your cart is empty!";
    header('Location: cart.php');
    exit;
}

$full_name = trim($_POST['full_name'] ?? '');
$address = trim($_POST['address'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$delivery_preference = trim($_POST['delivery_preference'] ?? '');
$same_as_shipping = isset($_POST['same_as_shipping']);
$billing_address = $same_as_shipping ? $address : trim($_POST['billing_address'] ?? '');
$payment_method = trim($_POST['payment_method'] ?? '');
$notes = trim($_POST['notes'] ?? '');

if (!$full_name || !$address || !$phone || !$payment_method) {
    $_SESSION['error'] = "Please fill all required fields!";
    header('Location: checkout.php');
    exit;
}

// Store checkout data in session for form persistence
$_SESSION['checkout_data'] = [
    'full_name' => $full_name,
    'address' => $address,
    'phone' => $phone,
    'delivery_preference' => $delivery_preference,
    'billing_address' => $billing_address,
    'notes' => $notes
];

// Validate cart stock
$total_amount = 0;
$shipping_cost = 50;
$tax_amount = 0; // As per schema
$discount_amount = 0; // As per schema
$final_amount = 0;
$products = [];

if ($conn instanceof mysqli) {
    $product_ids = array_keys($cart);
    if (!empty($product_ids)) {
        $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
        $stmt = $conn->prepare("SELECT product_id, price, quantity FROM products WHERE product_id IN ($placeholders) AND status = 'active'");
        if ($stmt) {
            $stmt->bind_param(str_repeat('i', count($product_ids)), ...$product_ids);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                if ($row['quantity'] >= $cart[$row['product_id']]['qty']) {
                    $products[$row['product_id']] = $row;
                    $total_amount += $row['price'] * $cart[$row['product_id']]['qty'];
                } else {
                    $_SESSION['error'] = "Some items are out of stock.";
                    header('Location: cart.php');
                    $stmt->close();
                    $conn->close();
                    exit;
                }
            }
            $stmt->close();
        } else {
            $_SESSION['error'] = "Failed to validate cart items.";
            header('Location: checkout.php');
            $conn->close();
            exit;
        }
    }
    $final_amount = $total_amount + $shipping_cost + $tax_amount - $discount_amount;

    // Fetch customer info
    $stmt = $conn->prepare("SELECT customer_id, email FROM customers WHERE customer_id = ?");
    if ($stmt) {
        $stmt->bind_param('i', $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $customer = $result->fetch_assoc();
        $stmt->close();

        if (!$customer) {
            $_SESSION['error'] = "Customer not found!";
            header('Location: checkout.php');
            $conn->close();
            exit;
        }

        $customer_name = $full_name; // Use form input
        $customer_email = $customer['email'];
        $customer_id = $customer['customer_id'];

        // Generate order number
        $order_number = 'ORD' . str_pad(mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT);

        // Set payment and order status
        $payment_status = ($payment_method === 'COD') ? 'Pending' : 'Completed';
        $order_status = 'Processing';

        // Insert order
        $stmt = $conn->prepare("
            INSERT INTO orders (
                order_number, customer_id, customer_name, customer_email, customer_phone, 
                shipping_address, billing_address, total_amount, tax_amount, shipping_cost, 
                discount_amount, final_amount, payment_method, payment_status, order_status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        if ($stmt) {
            $stmt->bind_param(
                'sisssssddddssss',
                $order_number,
                $customer_id,
                $customer_name,
                $customer_email,
                $phone,
                $address,
                $billing_address,
                $total_amount,
                $tax_amount,
                $shipping_cost,
                $discount_amount,
                $final_amount,
                $payment_method,
                $payment_status,
                $order_status
            );
            if (!$stmt->execute()) {
                $_SESSION['error'] = "Failed to create order: " . $stmt->error;
                header('Location: checkout.php');
                $stmt->close();
                $conn->close();
                exit;
            }
            $order_id = $stmt->insert_id;
            $stmt->close();

            // Insert order items and update stock
            $stmt = $conn->prepare("
                INSERT INTO order_items (order_id, product_id, product_name, quantity, unit_price, total_price)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stock_stmt = $conn->prepare("UPDATE products SET quantity = quantity - ? WHERE product_id = ?");
            if ($stmt && $stock_stmt) {
                foreach ($cart as $product_id => $item) {
                    if (isset($products[$product_id])) {
                        $qty = $item['qty'];
                        $unit_price = $item['price'];
                        $total_price = $qty * $unit_price;
                        $stmt->bind_param('iisidd', $order_id, $product_id, $item['name'], $qty, $unit_price, $total_price);
                        $stmt->execute();

                        // Update stock
                        $stock_stmt->bind_param('ii', $qty, $product_id);
                        $stock_stmt->execute();
                    }
                }
                $stmt->close();
                $stock_stmt->close();
            } else {
                $_SESSION['error'] = "Failed to process order items.";
                header('Location: checkout.php');
                $conn->close();
                exit;
            }

            // Clear cart and checkout data
            unset($_SESSION['cart']);
            unset($_SESSION['checkout_data']);

            // Redirect to order confirmation
            header("Location: order_confirmation.php?order_id={$order_id}");
            $conn->close();
            exit;
        } else {
            $_SESSION['error'] = "Failed to prepare order query: " . $conn->error;
            header('Location: checkout.php');
            $conn->close();
            exit;
        }
    } else {
        $_SESSION['error'] = "Customer query failed: " . $conn->error;
        header('Location: checkout.php');
        $conn->close();
        exit;
    }
} else {
    $_SESSION['error'] = "Database connection failed.";
    header('Location: checkout.php');
    exit;
}
?>