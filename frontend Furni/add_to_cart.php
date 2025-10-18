<?php
session_start();
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/db_connection.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($csrf_token)) {
        $response['message'] = 'Invalid CSRF token';
        echo json_encode($response);
        exit;
    }

    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $qty = isset($_POST['qty']) ? (int)$_POST['qty'] : 1;

    if ($product_id <= 0 || $qty <= 0) {
        $response['message'] = 'Invalid product ID or quantity';
        echo json_encode($response);
        exit;
    }

    $conn = include __DIR__ . '/includes/db_connection.php';
    if ($conn instanceof mysqli) {
        $stmt = $conn->prepare("SELECT product_id, product_name, price, quantity FROM products WHERE product_id = ? AND status = 'active'");
        if (!$stmt) {
            $response['message'] = 'Database query preparation failed';
            echo json_encode($response);
            exit;
        }
        $stmt->bind_param('i', $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        $stmt->close();

        if ($product) {
            if ($product['quantity'] < $qty) {
                $response['message'] = 'Insufficient stock available';
                echo json_encode($response);
                $conn->close();
                exit;
            }
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }
            $current_qty = $_SESSION['cart'][$product_id]['qty'] ?? 0;
            if ($product['quantity'] < $current_qty + $qty) {
                $response['message'] = 'Adding this quantity exceeds available stock';
                echo json_encode($response);
                $conn->close();
                exit;
            }
            $_SESSION['cart'][$product_id] = [
                'id' => $product['product_id'],
                'name' => $product['product_name'],
                'price' => $product['price'],
                'qty' => $current_qty + $qty
            ];
            $response['success'] = true;
            $response['message'] = 'Product added to cart';
        } else {
            $response['message'] = 'Product not found or inactive';
        }
        $conn->close();
    } else {
        $response['message'] = 'Database connection failed';
    }
}

echo json_encode($response);
?>