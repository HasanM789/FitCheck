<?php
require_once('db_config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
    $product_id = (int)$_POST['product_id'];
    
    // Verify product exists
    $check = $conn->query("SELECT id FROM products WHERE id = $product_id");
    if ($check->num_rows === 0) {
        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'catalog.php'));
        exit();
    }
    
    // Initialize cart if not exists
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    // Add product to cart or increment quantity
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]++;
    } else {
        $_SESSION['cart'][$product_id] = 1;
    }
}

// Redirect back to previous page
header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'catalog.php'));
exit();