<?php
require_once('db_config.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

$user_id = $_SESSION['user_id'];

echo "<h1>Debug Orders</h1>";
echo "User ID: " . $user_id . "<br>";

// Check all orders
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ?");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();

echo "<h2>Orders found: " . count($orders) . "</h2>";

if (count($orders) > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Total</th><th>Date</th><th>Items</th></tr>";
    foreach ($orders as $o) {
        // Get item count
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM order_items WHERE order_id = ?");
        $stmt->execute([$o['id']]);
        $item_count = $stmt->fetch()['count'];
        
        echo "<tr>";
        echo "<td>" . $o['id'] . "</td>";
        echo "<td>" . $o['total_price'] . "</td>";
        echo "<td>" . $o['order_date'] . "</td>";
        echo "<td>" . $item_count . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No orders found for user ID: " . $user_id . "<br>";
    echo "<a href='catalog.php'>Go shopping</a><br>";
}

echo "<br><a href='account.php'>Back to Account</a>";
?>