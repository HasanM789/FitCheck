<?php
// ============================================
// CART ACTION - FIX FOR RENDER
// ============================================

// Set session cookie parameters BEFORE session start
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax'
]);

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once('db_config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
    $product_id = (int)$_POST['product_id'];
    
    // Verify product exists
    $stmt = $conn->prepare("SELECT id FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    if ($stmt->rowCount() === 0) {
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
    
    // Debug: Log cart contents
    error_log("Cart after adding: " . print_r($_SESSION['cart'], true));
    
    // Force session to save
    session_write_close();
}

// Redirect back to previous page
header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'catalog.php'));
exit();
?>