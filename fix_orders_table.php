<?php
require_once('db_config.php');

echo "<h1>Fix Orders Table</h1>";

try {
    // Check if orders table exists
    $result = $conn->query("SELECT name FROM sqlite_master WHERE type='table' AND name='orders'");
    if ($result->fetch()) {
        echo "✅ Orders table exists.<br>";
    } else {
        echo "❌ Orders table does NOT exist. Creating...<br>";
        $conn->exec("
            CREATE TABLE orders (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER,
                total_price DECIMAL(10, 2) NOT NULL,
                coupon_used VARCHAR(20) DEFAULT NULL,
                order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )
        ");
        echo "✅ Orders table created!<br>";
    }
    
    // Check if order_items table exists
    $result = $conn->query("SELECT name FROM sqlite_master WHERE type='table' AND name='order_items'");
    if ($result->fetch()) {
        echo "✅ Order_items table exists.<br>";
    } else {
        echo "❌ Order_items table does NOT exist. Creating...<br>";
        $conn->exec("
            CREATE TABLE order_items (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                order_id INTEGER,
                product_id INTEGER,
                quantity INTEGER NOT NULL,
                selected_size VARCHAR(10) NOT NULL,
                selected_color VARCHAR(20) NOT NULL,
                FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
            )
        ");
        echo "✅ Order_items table created!<br>";
    }
    
    // Show existing orders
    echo "<h2>Existing Orders:</h2>";
    $orders = $conn->query("SELECT * FROM orders")->fetchAll();
    if (count($orders) > 0) {
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>ID</th><th>User ID</th><th>Total</th><th>Date</th></tr>";
        foreach ($orders as $o) {
            echo "<tr>";
            echo "<td>" . $o['id'] . "</td>";
            echo "<td>" . $o['user_id'] . "</td>";
            echo "<td>" . $o['total_price'] . "</td>";
            echo "<td>" . $o['order_date'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No orders found yet. Place an order to test.<br>";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>