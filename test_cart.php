<?php
require_once('db_config.php');

// Add a test item - uses the SAME session as the rest of your site
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add product ID 17 (Classic White Tee)
if (isset($_SESSION['cart'][17])) {
    $_SESSION['cart'][17]++;
} else {
    $_SESSION['cart'][17] = 1;
}

// Also add product 18 (Relaxed Fit Denim)
if (isset($_SESSION['cart'][18])) {
    $_SESSION['cart'][18]++;
} else {
    $_SESSION['cart'][18] = 1;
}

echo "✅ Test items added to cart!<br>";
echo "Product 17 (Classic White Tee) x " . $_SESSION['cart'][17] . "<br>";
echo "Product 18 (Relaxed Fit Denim) x " . $_SESSION['cart'][18] . "<br>";
echo "<br><a href='cart.php'>View Cart</a>";
?>