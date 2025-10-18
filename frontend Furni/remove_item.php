<?php
session_start();
require_once __DIR__ . '/includes/functions.php';

header('Content-Type: application/json');

// CSRF verification
$csrf = $_POST['csrf_token'] ?? '';
if(!verify_csrf_token($csrf)){
    echo json_encode(['success'=>false,'message'=>'Invalid CSRF token']);
    exit;
}

// Validate product_id
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

if($product_id <= 0 || empty($_SESSION['cart'][$product_id])){
    echo json_encode(['success'=>false,'message'=>'Invalid product ID']);
    exit;
}

// Remove from cart
unset($_SESSION['cart'][$product_id]);

echo json_encode(['success'=>true,'message'=>'Item removed']);
