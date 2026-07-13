<?php
require_once('db_config.php');

$session_id = $_SESSION['cart_session_id'];

// Clear cart
$stmt = $conn->prepare("DELETE FROM cart WHERE session_id = ?");
$stmt->execute([$session_id]);

echo "✅ Cart cleared!<br>";
echo "<a href='cart.php'>View Cart</a><br>";
echo "<a href='catalog.php'>Go to Catalog</a>";
?>