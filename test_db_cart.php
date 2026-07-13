<?php
require_once('db_config.php');

echo "<h1>Database Cart Test</h1>";

$session_id = $_SESSION['cart_session_id'];
echo "Session ID: " . $session_id . "<br><br>";

// Check if cart table exists
try {
    $stmt = $conn->query("SELECT name FROM sqlite_master WHERE type='table' AND name='cart'");
    if ($stmt->fetch()) {
        echo "✅ Cart table exists<br>";
    } else {
        echo "❌ Cart table does NOT exist<br>";
        echo "Creating cart table...<br>";
        $conn->exec("
            CREATE TABLE IF NOT EXISTS cart (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                session_id VARCHAR(255) NOT NULL,
                product_id INTEGER NOT NULL,
                quantity INTEGER NOT NULL DEFAULT 1,
                added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        echo "✅ Cart table created!<br>";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}

// Show current cart items
echo "<h2>Current Cart Items:</h2>";
$stmt = $conn->prepare("
    SELECT c.*, p.name, p.price 
    FROM cart c
    LEFT JOIN products p ON c.product_id = p.id
    WHERE c.session_id = ?
");
$stmt->execute([$session_id]);
$items = $stmt->fetchAll();

if (count($items) > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Product ID</th><th>Product Name</th><th>Quantity</th><th>Price</th></tr>";
    foreach ($items as $item) {
        echo "<tr>";
        echo "<td>" . $item['id'] . "</td>";
        echo "<td>" . $item['product_id'] . "</td>";
        echo "<td>" . htmlspecialchars($item['name'] ?? 'Unknown') . "</td>";
        echo "<td>" . $item['quantity'] . "</td>";
        echo "<td>" . ($item['price'] ?? 'N/A') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "❌ No items in cart<br>";
}

echo "<h2>Actions:</h2>";
echo "<a href='?add=1'>Add test item (Product ID 17)</a><br>";
echo "<a href='?clear=1'>Clear cart</a><br>";
echo "<a href='cart.php'>Go to Cart</a><br>";

if (isset($_GET['add'])) {
    try {
        $stmt = $conn->prepare("INSERT INTO cart (session_id, product_id, quantity) VALUES (?, ?, 1)");
        $stmt->execute([$session_id, 17]);
        echo "<p style='color:green;'>✅ Test item added! Refresh this page.</p>";
    } catch (Exception $e) {
        echo "<p style='color:red;'>❌ Error: " . $e->getMessage() . "</p>";
    }
}

if (isset($_GET['clear'])) {
    try {
        $stmt = $conn->prepare("DELETE FROM cart WHERE session_id = ?");
        $stmt->execute([$session_id]);
        echo "<p style='color:red;'>✅ Cart cleared! Refresh this page.</p>";
    } catch (Exception $e) {
        echo "<p style='color:red;'>❌ Error: " . $e->getMessage() . "</p>";
    }
}

// Show all cart items (any session)
echo "<h2>All Cart Items (all sessions):</h2>";
$all = $conn->query("SELECT * FROM cart")->fetchAll();
if (count($all) > 0) {
    echo "<pre>";
    print_r($all);
    echo "</pre>";
} else {
    echo "No items in any cart<br>";
}
?>