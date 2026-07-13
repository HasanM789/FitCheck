<?php
require_once('db_config.php');

echo "<h1>Test Adding to Cart</h1>";

$session_id = $_SESSION['cart_session_id'];
echo "Session ID: " . $session_id . "<br>";

// Show products
echo "<h2>Available Products:</h2>";
$products = $conn->query("SELECT id, name FROM products")->fetchAll();

if (count($products) > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Name</th></tr>";
    foreach ($products as $p) {
        echo "<tr><td>" . $p['id'] . "</td><td>" . $p['name'] . "</td></tr>";
    }
    echo "</table>";
    
    // Use product ID 17 (Classic White Tee)
    $product_id = 17;
    echo "<br>Using product ID: " . $product_id . "<br>";
    
    // Check if product exists
    $stmt = $conn->prepare("SELECT id, name FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();
    
    if ($product) {
        echo "✅ Product found: " . $product['name'] . "<br>";
        
        // Check if already in cart
        $stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE session_id = ? AND product_id = ?");
        $stmt->execute([$session_id, $product_id]);
        $existing = $stmt->fetch();
        
        if ($existing) {
            $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE id = ?");
            $stmt->execute([$existing['id']]);
            echo "✅ Quantity updated!<br>";
        } else {
            $stmt = $conn->prepare("INSERT INTO cart (session_id, product_id, quantity) VALUES (?, ?, 1)");
            $stmt->execute([$session_id, $product_id]);
            echo "✅ Product added to cart!<br>";
        }
        
        // Show cart contents
        echo "<h2>Cart Contents:</h2>";
        $stmt = $conn->prepare("
            SELECT c.*, p.name, p.price 
            FROM cart c
            JOIN products p ON c.product_id = p.id
            WHERE c.session_id = ?
        ");
        $stmt->execute([$session_id]);
        $items = $stmt->fetchAll();
        
        if (count($items) > 0) {
            foreach ($items as $item) {
                echo "- " . $item['name'] . " x " . $item['quantity'] . " = " . ($item['price'] * $item['quantity']) . " BD<br>";
            }
        } else {
            echo "❌ Cart is still empty!<br>";
        }
        
    } else {
        echo "❌ Product ID $product_id not found!<br>";
    }
} else {
    echo "❌ No products found!<br>";
}

echo "<br><a href='cart.php'>Go to Cart</a><br>";
echo "<a href='clear_cart.php'>Clear Cart</a><br>";
?>