<?php
require_once('db_config.php');

echo "<h1>Test Adding to Cart</h1>";

$session_id = $_SESSION['cart_session_id'];
echo "Session ID: " . $session_id . "<br>";

// First, show what products exist
echo "<h2>Available Products:</h2>";
$products = $conn->query("SELECT id, name FROM products")->fetchAll();
if (count($products) > 0) {
    echo "<ul>";
    foreach ($products as $p) {
        echo "<li>ID: " . $p['id'] . " - " . $p['name'] . "</li>";
    }
    echo "</ul>";
} else {
    echo "❌ No products found!<br>";
}

// Use the FIRST product ID from the database
$product_id = 1; // Default to 1, but we'll get the actual first product

if (count($products) > 0) {
    $product_id = $products[0]['id'];
    echo "<br>Using product ID: " . $product_id . "<br>";
}

// Try to add the product
try {
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
        echo "❌ Product not found!<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

echo "<br><a href='cart.php'>Go to Cart</a><br>";
echo "<a href='test_db_cart.php'>Go to DB Test</a><br>";
echo "<a href='check_products.php'>Check Products</a>";
?>