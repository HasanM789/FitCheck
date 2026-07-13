<?php
require_once('db_config.php');

echo "<h1>Header Cart Badge Test</h1>";

$session_id = $_SESSION['cart_session_id'];
echo "Session ID: " . $session_id . "<br>";

// Get cart count
try {
    $stmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE session_id = ?");
    $stmt->execute([$session_id]);
    $result = $stmt->fetch();
    $total_items = $result ? $result['total'] : 0;
    
    echo "Cart total items: " . $total_items . "<br>";
    
    // Show what the header would display
    echo "<h2>Header would show: <span style='color:red;font-size:24px;'>" . $total_items . "</span></h2>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}

echo "<br><a href='test_db_cart.php'>View Cart Database</a><br>";
echo "<a href='test_add_cart.php'>Add Test Item</a><br>";
echo "<a href='cart.php'>Go to Cart</a><br>";
?>