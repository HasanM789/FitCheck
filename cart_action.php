<?php
// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once('db_config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
    $product_id = (int)$_POST['product_id'];
    $session_id = $_SESSION['cart_session_id'];
    
    // Verify product exists
    $stmt = $conn->prepare("SELECT id FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    if ($stmt->rowCount() === 0) {
        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'catalog.php'));
        exit();
    }
    
    // Check if product already in cart
    $stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE session_id = ? AND product_id = ?");
    $stmt->execute([$session_id, $product_id]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        // Update quantity
        $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE id = ?");
        $stmt->execute([$existing['id']]);
    } else {
        // Insert new cart item
        $stmt = $conn->prepare("INSERT INTO cart (session_id, product_id, quantity) VALUES (?, ?, 1)");
        $stmt->execute([$session_id, $product_id]);
    }
}

// Redirect back
header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'catalog.php'));
exit();
?>