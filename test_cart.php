<?php
session_start();

// Add a test item
$_SESSION['cart'][17] = 2;

echo "✅ Test item added!<br>";
echo "<a href='cart.php'>View Cart</a>";
?>